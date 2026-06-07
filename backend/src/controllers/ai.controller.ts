import type { Request, Response } from "express";
import { z } from "zod";
import { aiService } from "../services/ai.service.js";

const projectPlanBody = z.object({
  projectId: z.string().uuid(),
});

export const aiController = {
  async list(_req: Request, res: Response) {
    const insights = await aiService.listInsights();
    res.json(insights);
  },

  async createDashboardInsight(_req: Request, res: Response) {
    const insight = await aiService.generateDashboardInsight();
    res.status(201).json(insight);
  },

  async createProjectPlan(req: Request, res: Response) {
    const { projectId } = projectPlanBody.parse(req.body);
    const plan = await aiService.generateProjectPlan(projectId);
    res.status(201).json(plan);
  },
};
