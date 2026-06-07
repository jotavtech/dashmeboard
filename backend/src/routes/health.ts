import { Router } from "express";
import { asyncHandler } from "../middlewares/async.js";
import { env } from "../lib/env.js";

export const healthRouter = Router();

healthRouter.get(
  "/",
  asyncHandler(async (_req, res) => {
    res.json({
      status: "ok",
      uptime: process.uptime(),
      timestamp: new Date().toISOString(),
      environment: env.NODE_ENV,
      services: {
        api: "up",
      },
    });
  }),
);

healthRouter.get(
  "/ready",
  asyncHandler(async (_req, res) => {
    try {
      const { prisma } = await import("../lib/prisma.js");
      await prisma.$queryRaw`SELECT 1`;
      res.json({
        status: "ok",
        uptime: process.uptime(),
        timestamp: new Date().toISOString(),
        environment: env.NODE_ENV,
        services: {
          api: "up",
          database: "up",
        },
      });
    } catch {
      res.status(503).json({
        status: "degraded",
        uptime: process.uptime(),
        timestamp: new Date().toISOString(),
        environment: env.NODE_ENV,
        services: {
          api: "up",
          database: "down",
        },
      });
    }
  }),
);
