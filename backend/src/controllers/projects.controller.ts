import type { Request, Response } from "express";
import { z } from "zod";
import { projectsService } from "../services/projects.service.js";

const StatusEnum = z.enum(["PLANNED", "ACTIVE", "PAUSED", "DONE", "ARCHIVED"]);
const PriorityEnum = z.enum(["LOW", "MEDIUM", "HIGH", "CRITICAL"]);

const listQuery = z.object({
  q: z.string().trim().min(1).max(100).optional(),
  status: StatusEnum.optional(),
  priority: PriorityEnum.optional(),
});

const createBody = z.object({
  title: z.string().trim().min(1).max(160),
  description: z.string().max(2000).nullable().optional(),
  status: StatusEnum.default("PLANNED"),
  priority: PriorityEnum.default("MEDIUM"),
  owner: z.string().trim().email().max(120),
});

const updateBody = createBody.partial();

const idParam = z.object({ id: z.string().uuid() });

export const projectsController = {
  async list(req: Request, res: Response) {
    const filters = listQuery.parse(req.query);
    const projects = await projectsService.list(filters);
    res.json(projects);
  },

  async get(req: Request, res: Response) {
    const { id } = idParam.parse(req.params);
    const project = await projectsService.get(id);
    res.json(project);
  },

  async create(req: Request, res: Response) {
    const data = createBody.parse(req.body);
    const project = await projectsService.create(data);
    res.status(201).json(project);
  },

  async update(req: Request, res: Response) {
    const { id } = idParam.parse(req.params);
    const data = updateBody.parse(req.body);
    const project = await projectsService.update(id, data);
    res.json(project);
  },

  async remove(req: Request, res: Response) {
    const { id } = idParam.parse(req.params);
    await projectsService.remove(id);
    res.status(204).send();
  },
};
