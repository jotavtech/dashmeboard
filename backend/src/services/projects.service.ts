import type { Prisma, ProjectPriority, ProjectStatus } from "@prisma/client";
import { prisma } from "../lib/prisma.js";
import { HttpError } from "../middlewares/error.js";

export type ListFilters = {
  q?: string;
  status?: ProjectStatus;
  priority?: ProjectPriority;
};

export const projectsService = {
  async list(filters: ListFilters = {}) {
    const where: Prisma.ProjectWhereInput = {};
    if (filters.status) where.status = filters.status;
    if (filters.priority) where.priority = filters.priority;
    if (filters.q) {
      where.OR = [
        { title: { contains: filters.q, mode: "insensitive" } },
        { description: { contains: filters.q, mode: "insensitive" } },
      ];
    }
    return prisma.project.findMany({
      where,
      orderBy: [{ updatedAt: "desc" }],
      take: 200,
    });
  },

  async get(id: string) {
    const project = await prisma.project.findUnique({ where: { id } });
    if (!project) throw new HttpError(404, "Project not found");
    return project;
  },

  async create(data: {
    title: string;
    description?: string | null;
    status: ProjectStatus;
    priority: ProjectPriority;
    owner: string;
  }) {
    return prisma.project.create({ data });
  },

  async update(
    id: string,
    data: Partial<{
      title: string;
      description: string | null;
      status: ProjectStatus;
      priority: ProjectPriority;
      owner: string;
    }>,
  ) {
    const existing = await prisma.project.findUnique({ where: { id } });
    if (!existing) throw new HttpError(404, "Project not found");
    return prisma.project.update({ where: { id }, data });
  },

  async remove(id: string) {
    const existing = await prisma.project.findUnique({ where: { id } });
    if (!existing) throw new HttpError(404, "Project not found");
    await prisma.project.delete({ where: { id } });
  },
};
