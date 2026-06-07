import type { Request, Response, NextFunction, RequestHandler } from "express";

type Options = {
  /** Sliding window length in milliseconds. */
  windowMs: number;
  /** Max requests allowed per client within the window. */
  max: number;
};

/**
 * Minimal in-memory sliding-window rate limiter.
 *
 * Intended to keep the unauthenticated, billable AI endpoints from being
 * spammed (CORS does not protect against `curl`). It is per-process, which is
 * fine for a single Railway instance; swap for a Redis-backed limiter if the
 * backend is ever scaled horizontally.
 */
export function rateLimit({ windowMs, max }: Options): RequestHandler {
  const hits = new Map<string, number[]>();

  return (req: Request, res: Response, next: NextFunction) => {
    const key = req.ip ?? "unknown";
    const now = Date.now();
    const recent = (hits.get(key) ?? []).filter((ts) => now - ts < windowMs);

    if (recent.length >= max) {
      const retryAfter = Math.ceil((recent[0] + windowMs - now) / 1000);
      res.setHeader("Retry-After", String(retryAfter));
      res.status(429).json({
        message: `Too many requests. Try again in ${retryAfter}s.`,
      });
      return;
    }

    recent.push(now);
    hits.set(key, recent);
    next();
  };
}
