import type { NextFunction, Request, Response } from "express";
import jwt from "jsonwebtoken";
import type { RoleKey } from "@prisma/client";
import { verifyAccessToken } from "../lib/tokens.js";
import { HttpError } from "./error.js";

export type AuthContext = {
  userId: string;
  organizationId: string;
  roleKey: RoleKey;
};

declare module "express-serve-static-core" {
  interface Request {
    auth?: AuthContext;
  }
}

export function requireAuth(req: Request, _res: Response, next: NextFunction) {
  const header = req.headers.authorization;
  if (!header?.startsWith("Bearer ")) {
    next(new HttpError(401, "Missing access token"));
    return;
  }
  try {
    const payload = verifyAccessToken(header.slice("Bearer ".length));
    req.auth = { userId: payload.sub, organizationId: payload.org, roleKey: payload.role };
    next();
  } catch (err) {
    next(
      err instanceof jwt.TokenExpiredError
        ? new HttpError(401, "Access token expired")
        : new HttpError(401, "Invalid access token"),
    );
  }
}

/** Requires one of the given roles (use after requireAuth). */
export function requireRole(...roles: RoleKey[]) {
  return (req: Request, _res: Response, next: NextFunction) => {
    if (!req.auth) {
      next(new HttpError(401, "Missing access token"));
      return;
    }
    if (!roles.includes(req.auth.roleKey)) {
      next(new HttpError(403, "Insufficient role"));
      return;
    }
    next();
  };
}
