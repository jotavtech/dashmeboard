import { prisma } from "../lib/prisma.js";

export const analyticsService = {
  async overview() {
    const [
      totalProjects,
      activeProjects,
      completedProjects,
      totalTasks,
      completedTasks,
      activeUsers,
      projectsByStatus,
      projectsByPriority,
    ] = await Promise.all([
      prisma.project.count(),
      prisma.project.count({ where: { status: "ACTIVE" } }),
      prisma.project.count({ where: { status: "DONE" } }),
      prisma.task.count(),
      prisma.task.count({ where: { status: "DONE" } }),
      prisma.user.count(),
      prisma.project.groupBy({ by: ["status"], _count: { _all: true } }),
      prisma.project.groupBy({ by: ["priority"], _count: { _all: true } }),
    ]);

    const weeklyPerformance = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;

    return {
      totalProjects,
      activeProjects,
      completedProjects,
      totalTasks,
      completedTasks,
      activeUsers,
      weeklyPerformance,
      projectsByStatus: Object.fromEntries(
        projectsByStatus.map((row) => [row.status, row._count._all]),
      ),
      projectsByPriority: Object.fromEntries(
        projectsByPriority.map((row) => [row.priority, row._count._all]),
      ),
    };
  },

  async activity(limit: number) {
    const [recentProjects, recentTasks] = await Promise.all([
      prisma.project.findMany({
        orderBy: { updatedAt: "desc" },
        take: limit,
      }),
      prisma.task.findMany({
        where: { status: "DONE", completedAt: { not: null } },
        orderBy: { completedAt: "desc" },
        take: limit,
      }),
    ]);

    const items = [
      ...recentProjects.map((p) => ({
        id: `p_${p.id}`,
        type: "project" as const,
        title: `Project · ${p.title}`,
        detail: `${p.status} · ${p.priority} · owner ${p.owner}`,
        timestamp: relative(p.updatedAt),
        ts: p.updatedAt.getTime(),
      })),
      ...recentTasks.map((t) => ({
        id: `t_${t.id}`,
        type: "commit" as const,
        title: `Task done · ${t.title}`,
        detail: `assignee ${t.assignee ?? "—"}`,
        timestamp: relative(t.completedAt ?? t.updatedAt),
        ts: (t.completedAt ?? t.updatedAt).getTime(),
      })),
    ];

    return items.sort((a, b) => b.ts - a.ts).slice(0, limit).map(({ ts: _ts, ...rest }) => rest);
  },

  async throughput() {
    const days = ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"];
    const today = new Date();
    const result: { day: string; value: number }[] = [];

    for (let i = 6; i >= 0; i--) {
      const start = new Date(today);
      start.setHours(0, 0, 0, 0);
      start.setDate(start.getDate() - i);
      const end = new Date(start);
      end.setDate(end.getDate() + 1);

      const count = await prisma.task.count({
        where: { completedAt: { gte: start, lt: end } },
      });

      result.push({ day: days[start.getDay() === 0 ? 6 : start.getDay() - 1], value: count });
    }

    return result;
  },

  async database() {
    const [users, projects, tasks, analyticsLogs] = await Promise.all([
      prisma.user.count(),
      prisma.project.count(),
      prisma.task.count(),
      prisma.analyticsLog.count(),
    ]);

    return {
      provider: "PostgreSQL",
      orm: "Prisma",
      schema: "public",
      tables: [
        { name: "users", columns: 6, rows: users, indexed: true },
        { name: "projects", columns: 8, rows: projects, indexed: true },
        { name: "tasks", columns: 9, rows: tasks, indexed: true },
        { name: "analytics_logs", columns: 4, rows: analyticsLogs, indexed: true },
      ],
    };
  },
};

function relative(d: Date) {
  const diff = Date.now() - d.getTime();
  const mins = Math.floor(diff / 60_000);
  if (mins < 1) return "now";
  if (mins < 60) return `${mins}m`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours}h`;
  const days = Math.floor(hours / 24);
  return `${days}d`;
}
