import crypto from "node:crypto";
import jwt from "jsonwebtoken";
import type { RoleKey } from "@prisma/client";
import { env } from "./env.js";

export type AccessTokenPayload = {
  /** userId */
  sub: string;
  /** active organizationId */
  org: string;
  /** RoleKey of the active membership */
  role: RoleKey;
};

export function signAccessToken(payload: AccessTokenPayload): string {
  return jwt.sign(payload, env.JWT_SECRET, {
    expiresIn: `${env.ACCESS_TOKEN_TTL_MIN}m`,
  });
}

export function verifyAccessToken(token: string): AccessTokenPayload {
  const decoded = jwt.verify(token, env.JWT_SECRET);
  if (typeof decoded === "string") throw new jwt.JsonWebTokenError("invalid payload");
  return decoded as AccessTokenPayload;
}

/** Opaque refresh token: random value for the client, only its hash persisted. */
export function generateRefreshToken(): { token: string; tokenHash: string } {
  const token = crypto.randomBytes(48).toString("base64url");
  return { token, tokenHash: hashRefreshToken(token) };
}

export function hashRefreshToken(token: string): string {
  return crypto.createHash("sha256").update(token).digest("hex");
}

export function refreshTokenExpiry(): Date {
  const d = new Date();
  d.setDate(d.getDate() + env.REFRESH_TOKEN_TTL_DAYS);
  return d;
}
