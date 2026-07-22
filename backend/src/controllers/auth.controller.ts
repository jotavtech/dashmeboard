import type { Request, Response } from "express";
import { z } from "zod";
import { env } from "../lib/env.js";
import { HttpError } from "../middlewares/error.js";
import * as authService from "../services/auth.service.js";

const REFRESH_COOKIE = "dashme_refresh";

const registerSchema = z.object({
  name: z.string().trim().min(2).max(120),
  email: z.string().trim().email().max(254),
  password: z.string().min(8).max(128),
  organizationName: z.string().trim().min(2).max(120),
  segment: z.string().trim().max(60).optional(),
});

const loginSchema = z.object({
  email: z.string().trim().email(),
  password: z.string().min(1),
});

function sessionContext(req: Request) {
  return { userAgent: req.headers["user-agent"], ip: req.ip };
}

function setRefreshCookie(res: Response, token: string) {
  res.cookie(REFRESH_COOKIE, token, {
    httpOnly: true,
    secure: env.NODE_ENV === "production",
    sameSite: "lax",
    // Only auth endpoints ever need it — keeps the token off every other request.
    path: "/api/auth",
    maxAge: env.REFRESH_TOKEN_TTL_DAYS * 24 * 60 * 60 * 1000,
  });
}

function clearRefreshCookie(res: Response) {
  res.clearCookie(REFRESH_COOKIE, { path: "/api/auth" });
}

function toBody(session: Awaited<ReturnType<typeof authService.login>>) {
  const { refreshToken: _refreshToken, ...body } = session;
  return body;
}

export async function register(req: Request, res: Response) {
  const data = registerSchema.parse(req.body);
  const session = await authService.register(data, sessionContext(req));
  setRefreshCookie(res, session.refreshToken);
  res.status(201).json(toBody(session));
}

export async function login(req: Request, res: Response) {
  const data = loginSchema.parse(req.body);
  const session = await authService.login(data, sessionContext(req));
  setRefreshCookie(res, session.refreshToken);
  res.json(toBody(session));
}

export async function refresh(req: Request, res: Response) {
  const raw: unknown = req.cookies?.[REFRESH_COOKIE];
  if (typeof raw !== "string" || !raw) throw new HttpError(401, "Missing session");
  try {
    const session = await authService.rotateRefresh(raw, sessionContext(req));
    setRefreshCookie(res, session.refreshToken);
    res.json(toBody(session));
  } catch (err) {
    clearRefreshCookie(res);
    throw err;
  }
}

export async function logout(req: Request, res: Response) {
  const raw: unknown = req.cookies?.[REFRESH_COOKIE];
  await authService.logout(typeof raw === "string" ? raw : undefined);
  clearRefreshCookie(res);
  res.status(204).end();
}

export async function me(req: Request, res: Response) {
  // requireAuth guarantees req.auth
  res.json(await authService.getMe(req.auth!.userId));
}
