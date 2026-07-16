import type { TaskStatus } from "@prisma/client";
import { prisma } from "../lib/prisma.js";
import { HttpError } from "../middlewares/error.js";

const taskInclude = {
  project: { select: { id: true, title: true } },
} as const;

export const tasksService = {
  async create(
    projectId: string,
    data: {
      title: string;
      description?: string | null;
      status?: TaskStatus;
      assignee?: string | null;
      dueDate?: Date | null;
    },
  ) {
    const project = await prisma.project.findUnique({ where: { id: projectId } });
    if (!project) throw new HttpError(404, "Project not found");

    const status = data.status ?? "TODO";
    const lastInColumn = await prisma.task.aggregate({
      where: { projectId, status },
      _max: { order: true },
    });

    return prisma.task.create({
      data: {
        ...data,
        status,
        projectId,
        order: (lastInColumn._max.order ?? -1) + 1,
        completedAt: status === "DONE" ? new Date() : null,
      },
      include: taskInclude,
    });
  },

  async update(
    id: string,
    data: Partial<{
      title: string;
      description: string | null;
      status: TaskStatus;
      assignee: string | null;
      dueDate: Date | null;
      order: number;
    }>,
  ) {
    const existing = await prisma.task.findUnique({ where: { id } });
    if (!existing) throw new HttpError(404, "Task not found");

    // Regra do kanban: entrar em DONE marca a conclusão; sair de DONE a limpa.
    let completedAt = existing.completedAt;
    if (data.status && data.status !== existing.status) {
      completedAt = data.status === "DONE" ? new Date() : null;
    }

    return prisma.task.update({
      where: { id },
      data: { ...data, completedAt },
      include: taskInclude,
    });
  },

  async remove(id: string) {
    const existing = await prisma.task.findUnique({ where: { id } });
    if (!existing) throw new HttpError(404, "Task not found");
    await prisma.task.delete({ where: { id } });
  },
};
