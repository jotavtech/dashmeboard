import type { Prisma, ProjectPriority, ProjectStatus } from "@prisma/client";
import { prisma } from "../lib/prisma.js";
import { HttpError } from "../middlewares/error.js";

export type ListFilters = {
  q?: string;
  status?: ProjectStatus;
  priority?: ProjectPriority;
  tag?: string;
  client?: string;
};

type ProjectContext = Partial<{
  deadline: Date | null;
  client: string | null;
  repoUrl: string | null;
  deployUrl: string | null;
  docsUrl: string | null;
  activeBranch: string | null;
  notes: string | null;
  tags: string[];
}>;

// _count dá o progresso (done/total) na listagem sem carregar as tasks.
const listInclude = {
  _count: { select: { tasks: true } },
  tasks: { where: { status: "DONE" as const }, select: { id: true } },
} as const;

export const projectsService = {
  async list(filters: ListFilters = {}) {
    const where: Prisma.ProjectWhereInput = {};
    if (filters.status) where.status = filters.status;
    if (filters.priority) where.priority = filters.priority;
    if (filters.tag) where.tags = { has: filters.tag };
    if (filters.client) where.client = { contains: filters.client, mode: "insensitive" };
    if (filters.q) {
      where.OR = [
        { title: { contains: filters.q, mode: "insensitive" } },
        { description: { contains: filters.q, mode: "insensitive" } },
      ];
    }
    const projects = await prisma.project.findMany({
      where,
      orderBy: [{ updatedAt: "desc" }],
      take: 200,
      include: listInclude,
    });
    return projects.map(({ _count, tasks, ...project }) => ({
      ...project,
      taskCount: _count.tasks,
      doneTaskCount: tasks.length,
    }));
  },

  async get(id: string) {
    const project = await prisma.project.findUnique({
      where: { id },
      include: {
        tasks: { orderBy: [{ order: "asc" }, { createdAt: "asc" }] },
      },
    });
    if (!project) throw new HttpError(404, "Project not found");
    return project;
  },

  async create(
    data: {
      title: string;
      description?: string | null;
      status: ProjectStatus;
      priority: ProjectPriority;
      owner: string;
    } & ProjectContext,
  ) {
    await ensureOwner(data.owner);
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
    }> &
      ProjectContext,
  ) {
    const existing = await prisma.project.findUnique({ where: { id } });
    if (!existing) throw new HttpError(404, "Project not found");
    if (data.owner) await ensureOwner(data.owner);
    return prisma.project.update({ where: { id }, data });
  },

  async remove(id: string) {
    const existing = await prisma.project.findUnique({ where: { id } });
    if (!existing) throw new HttpError(404, "Project not found");
    await prisma.project.delete({ where: { id } });
  },
};

async function ensureOwner(email: string) {
  const name = email.split("@")[0] || "operator";
  await prisma.user.upsert({
    where: { email },
    update: {},
    create: { email, name },
  });
}
