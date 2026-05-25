import { Router } from "express";
import { prisma } from "../lib/prisma.js";
import { asyncHandler } from "../middlewares/async.js";

export const healthRouter = Router();

healthRouter.get(
  "/",
  asyncHandler(async (_req, res) => {
    try {
      await prisma.$queryRaw`SELECT 1`;
      res.json({
        status: "ok",
        uptime: process.uptime(),
        timestamp: new Date().toISOString(),
        database: "up",
      });
    } catch {
      res.status(503).json({
        status: "degraded",
        uptime: process.uptime(),
        timestamp: new Date().toISOString(),
        database: "down",
      });
    }
  }),
);
