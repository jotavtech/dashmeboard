import crypto from "node:crypto";
import request from "supertest";
import type { Express } from "express";
import { prisma } from "../lib/prisma.js";

// Random per test run — never a real credential (keeps secret scanners quiet).
export const TEST_PASSWORD = `Tt1!${crypto.randomBytes(9).toString("base64url")}`;

/** Wipes every table touched by auth + legacy suites, FK-safe order. */
export async function resetAllData() {
  await prisma.refreshToken.deleteMany();
  await prisma.auditLog.deleteMany();
  await prisma.invitation.deleteMany();
  await prisma.membership.deleteMany();
  await prisma.organization.deleteMany();
  await prisma.task.deleteMany();
  await prisma.project.deleteMany();
  await prisma.user.deleteMany();
}

/** Registers a fresh owner + organization through the public API. */
export async function registerOwner(
  app: Express,
  overrides: { email?: string; name?: string; organizationName?: string } = {},
) {
  const email = overrides.email ?? "owner@dashme.io";
  const res = await request(app)
    .post("/api/auth/register")
    .send({
      name: overrides.name ?? "Owner Test",
      email,
      password: TEST_PASSWORD,
      organizationName: overrides.organizationName ?? "Empresa Teste",
    })
    .expect(201);

  return {
    email,
    accessToken: res.body.accessToken as string,
    user: res.body.user as { id: string; name: string; email: string },
    organization: res.body.organization as { id: string; name: string; slug: string },
    cookies: res.headers["set-cookie"] as unknown as string[],
  };
}
