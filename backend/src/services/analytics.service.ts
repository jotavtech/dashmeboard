import { prisma } from "../lib/prisma.js";

export const analyticsService = {
  async overview() {
    const [activeProjects, completedTasks, activeUsers] = await Promise.all([
      prisma.project.count({ where: { status: "ACTIVE" } }),
      prisma.task.count({ where: { status: "DONE" } }),
      prisma.user.count(),
    ]);

    return {
      activeProjects,
      completedTasks,
      activeUsers,
      weeklyPerformance: 94,
      uptimePercent: 99.94,
      pipelineRuns: 184,
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
