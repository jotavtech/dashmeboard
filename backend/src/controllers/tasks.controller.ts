import type { Request, Response } from "express";
import { z } from "zod";
import { tasksService } from "../services/tasks.service.js";

const TaskStatusEnum = z.enum(["TODO", "DOING", "REVIEW", "DONE"]);

const emptyToNull = (value: unknown) =>
  typeof value === "string" && value.trim() === "" ? null : value;

const createBody = z.object({
  title: z.string().trim().min(1).max(160),
  description: z.preprocess(emptyToNull, z.string().max(2000).nullable().optional()),
  status: TaskStatusEnum.optional(),
  assignee: z.preprocess(emptyToNull, z.string().trim().email().max(120).nullable().optional()),
  dueDate: z.preprocess(emptyToNull, z.coerce.date().nullable().optional()),
});

const updateBody = createBody.partial().extend({
  order: z.coerce.number().int().min(0).optional(),
});

const idParam = z.object({ id: z.string().uuid() });

export const tasksController = {
  async create(req: Request, res: Response) {
    const { id: projectId } = idParam.parse(req.params);
    const data = createBody.parse(req.body);
    const task = await tasksService.create(projectId, data);
    res.status(201).json(task);
  },

  async update(req: Request, res: Response) {
    const { id } = idParam.parse(req.params);
    const data = updateBody.parse(req.body);
    const task = await tasksService.update(id, data);
    res.json(task);
  },

  async remove(req: Request, res: Response) {
    const { id } = idParam.parse(req.params);
    await tasksService.remove(id);
    res.status(204).send();
  },
};
