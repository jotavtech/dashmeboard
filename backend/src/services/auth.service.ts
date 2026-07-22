import crypto from "node:crypto";
import bcrypt from "bcryptjs";
import { MembershipStatus, OrgStatus, RoleKey, UserStatus } from "@prisma/client";
import { prisma } from "../lib/prisma.js";
import { HttpError } from "../middlewares/error.js";
import {
  generateRefreshToken,
  hashRefreshToken,
  refreshTokenExpiry,
  signAccessToken,
} from "../lib/tokens.js";

const BCRYPT_COST = 12;

type SessionContext = { userAgent?: string; ip?: string };

type AuthSession = {
  accessToken: string;
  refreshToken: string;
  user: { id: string; name: string; email: string };
  organization: { id: string; name: string; slug: string };
  roleKey: RoleKey;
};

function slugify(name: string): string {
  return name
    .normalize("NFD")
    .replace(/[̀-ͯ]/g, "")
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "")
    .slice(0, 40) || "empresa";
}

async function uniqueSlug(name: string): Promise<string> {
  const base = slugify(name);
  const existing = await prisma.organization.findUnique({ where: { slug: base } });
  if (!existing) return base;
  return `${base}-${crypto.randomBytes(3).toString("hex")}`;
}

/** Picks the active org for a user (single-org UX for now: earliest membership). */
async function activeMembership(userId: string) {
  const membership = await prisma.membership.findFirst({
    where: {
      userId,
      status: MembershipStatus.ACTIVE,
      organization: { status: OrgStatus.ACTIVE },
    },
    orderBy: { createdAt: "asc" },
    include: { organization: true },
  });
  if (!membership) throw new HttpError(403, "No active organization for this user");
  return membership;
}

async function issueSession(userId: string, ctx: SessionContext): Promise<AuthSession> {
  const membership = await activeMembership(userId);
  const user = await prisma.user.findUniqueOrThrow({ where: { id: userId } });

  const { token, tokenHash } = generateRefreshToken();
  await prisma.refreshToken.create({
    data: {
      userId,
      tokenHash,
      expiresAt: refreshTokenExpiry(),
      userAgent: ctx.userAgent?.slice(0, 255),
      ip: ctx.ip,
    },
  });

  return {
    accessToken: signAccessToken({
      sub: userId,
      org: membership.organizationId,
      role: membership.roleKey,
    }),
    refreshToken: token,
    user: { id: user.id, name: user.name, email: user.email },
    organization: {
      id: membership.organization.id,
      name: membership.organization.name,
      slug: membership.organization.slug,
    },
    roleKey: membership.roleKey,
  };
}

export async function register(
  data: { name: string; email: string; password: string; organizationName: string; segment?: string },
  ctx: SessionContext,
): Promise<AuthSession> {
  const email = data.email.toLowerCase();
  const existing = await prisma.user.findUnique({ where: { email } });
  if (existing?.passwordHash) throw new HttpError(409, "E-mail already registered");

  const passwordHash = await bcrypt.hash(data.password, BCRYPT_COST);
  const slug = await uniqueSlug(data.organizationName);

  const userId = await prisma.$transaction(async (tx) => {
    // Legacy seed users have no password; claiming the e-mail upgrades them.
    const user = existing
      ? await tx.user.update({ where: { id: existing.id }, data: { name: data.name, passwordHash } })
      : await tx.user.create({ data: { name: data.name, email, passwordHash } });

    const organization = await tx.organization.create({
      data: { name: data.organizationName, slug, segment: data.segment },
    });
    await tx.membership.create({
      data: {
        organizationId: organization.id,
        userId: user.id,
        roleKey: RoleKey.OWNER,
        acceptedAt: new Date(),
      },
    });
    await tx.auditLog.create({
      data: {
        organizationId: organization.id,
        actorUserId: user.id,
        action: "auth.register",
        entityType: "organization",
        entityId: organization.id,
      },
    });
    return user.id;
  });

  return issueSession(userId, ctx);
}

export async function login(
  data: { email: string; password: string },
  ctx: SessionContext,
): Promise<AuthSession> {
  const user = await prisma.user.findUnique({ where: { email: data.email.toLowerCase() } });
  // Constant-shape failure: same error whether the user exists or not.
  if (!user?.passwordHash) throw new HttpError(401, "Invalid credentials");
  if (user.status !== UserStatus.ACTIVE) throw new HttpError(403, "Account is blocked");

  const ok = await bcrypt.compare(data.password, user.passwordHash);
  if (!ok) throw new HttpError(401, "Invalid credentials");

  await prisma.user.update({ where: { id: user.id }, data: { lastLoginAt: new Date() } });
  return issueSession(user.id, ctx);
}

export async function rotateRefresh(rawToken: string, ctx: SessionContext): Promise<AuthSession> {
  const tokenHash = hashRefreshToken(rawToken);
  const stored = await prisma.refreshToken.findUnique({ where: { tokenHash } });
  if (!stored) throw new HttpError(401, "Invalid session");

  // Reuse of a rotated/revoked token means the value leaked: kill every
  // session for this user (ADR-001 §3).
  if (stored.revokedAt) {
    await prisma.refreshToken.updateMany({
      where: { userId: stored.userId, revokedAt: null },
      data: { revokedAt: new Date() },
    });
    await prisma.auditLog.create({
      data: {
        actorUserId: stored.userId,
        action: "auth.refresh_reuse_detected",
        entityType: "refresh_token",
        entityId: stored.id,
      },
    });
    throw new HttpError(401, "Session revoked");
  }
  if (stored.expiresAt < new Date()) throw new HttpError(401, "Session expired");

  const user = await prisma.user.findUniqueOrThrow({ where: { id: stored.userId } });
  if (user.status !== UserStatus.ACTIVE) throw new HttpError(403, "Account is blocked");

  const session = await issueSession(stored.userId, ctx);
  const newHash = hashRefreshToken(session.refreshToken);
  const replacement = await prisma.refreshToken.findUniqueOrThrow({ where: { tokenHash: newHash } });
  await prisma.refreshToken.update({
    where: { id: stored.id },
    data: { revokedAt: new Date(), replacedById: replacement.id },
  });

  return session;
}

export async function logout(rawToken: string | undefined): Promise<void> {
  if (!rawToken) return;
  await prisma.refreshToken.updateMany({
    where: { tokenHash: hashRefreshToken(rawToken), revokedAt: null },
    data: { revokedAt: new Date() },
  });
}

export async function getMe(userId: string) {
  const user = await prisma.user.findUnique({ where: { id: userId } });
  if (!user || user.status !== UserStatus.ACTIVE) throw new HttpError(401, "Unauthorized");
  const membership = await activeMembership(userId);
  return {
    user: { id: user.id, name: user.name, email: user.email, avatarUrl: user.avatarUrl },
    organization: {
      id: membership.organization.id,
      name: membership.organization.name,
      slug: membership.organization.slug,
      segment: membership.organization.segment,
    },
    roleKey: membership.roleKey,
  };
}
