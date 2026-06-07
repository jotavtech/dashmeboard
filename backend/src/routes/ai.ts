import { Router } from "express";
import { aiController } from "../controllers/ai.controller.js";
import { asyncHandler } from "../middlewares/async.js";
import { rateLimit } from "../middlewares/rateLimit.js";

export const aiRouter = Router();

// Each generation triggers a paid OpenAI call — cap how often it can fire.
const generationLimit = rateLimit({ windowMs: 60_000, max: 5 });

aiRouter.get("/insights", asyncHandler(aiController.list));
aiRouter.post("/insights", generationLimit, asyncHandler(aiController.createDashboardInsight));
aiRouter.post("/project-plan", generationLimit, asyncHandler(aiController.createProjectPlan));
