import { Router } from "express";
import { analyticsService } from "../services/analytics.service.js";
import { asyncHandler } from "../middlewares/async.js";

export const analyticsRouter = Router();

analyticsRouter.get(
  "/overview",
  asyncHandler(async (_req, res) => {
    res.json(await analyticsService.overview());
  }),
);

analyticsRouter.get(
  "/activity",
  asyncHandler(async (req, res) => {
    const limit = Math.min(50, Math.max(1, Number(req.query.limit) || 12));
    res.json(await analyticsService.activity(limit));
  }),
);

analyticsRouter.get(
  "/throughput",
  asyncHandler(async (_req, res) => {
    res.json(await analyticsService.throughput());
  }),
);

analyticsRouter.get(
  "/deadlines",
  asyncHandler(async (_req, res) => {
    res.json(await analyticsService.deadlines());
  }),
);

analyticsRouter.get(
  "/database",
  asyncHandler(async (_req, res) => {
    res.json(await analyticsService.database());
  }),
);
