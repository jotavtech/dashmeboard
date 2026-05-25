import { PrismaClient, ProjectStatus, ProjectPriority, TaskStatus, UserRole } from "@prisma/client";

const prisma = new PrismaClient();

async function main() {
  console.log("[seed] cleaning…");
  await prisma.task.deleteMany();
  await prisma.project.deleteMany();
  await prisma.user.deleteMany();

  console.log("[seed] users…");
  await prisma.user.createMany({
    data: [
      { email: "jotavtech@dashme.io", name: "jotavtech", role: UserRole.FOUNDER },
      { email: "rsantos@dashme.io", name: "rsantos", role: UserRole.ENGINEER },
      { email: "amartins@dashme.io", name: "amartins", role: UserRole.DESIGNER },
      { email: "ktanaka@dashme.io", name: "ktanaka", role: UserRole.ENGINEER },
    ],
  });

  console.log("[seed] projects…");
  const projects = [
    {
      title: "Dashmeboard core platform",
      description: "Editorial cyber dashboard for projects, analytics and workflows.",
      status: ProjectStatus.ACTIVE,
      priority: ProjectPriority.CRITICAL,
      owner: "jotavtech@dashme.io",
    },
    {
      title: "Agents subsystem (local)",
      description: "Architect, frontend, backend, devops, design, docs, reviewer.",
      status: ProjectStatus.ACTIVE,
      priority: ProjectPriority.HIGH,
      owner: "jotavtech@dashme.io",
    },
    {
      title: "Postgres → analytics pipelines",
      description: "Throughput, error budget and pressure heatmaps.",
      status: ProjectStatus.ACTIVE,
      priority: ProjectPriority.MEDIUM,
      owner: "rsantos@dashme.io",
    },
    {
      title: "Design tokens v2",
      description: "Refine chrome/ink/rust palette and motion specs.",
      status: ProjectStatus.PLANNED,
      priority: ProjectPriority.MEDIUM,
      owner: "amartins@dashme.io",
    },
    {
      title: "CI · SonarCloud pipeline",
      description: "Full CI with frontend, backend and quality scans.",
      status: ProjectStatus.DONE,
      priority: ProjectPriority.HIGH,
      owner: "ktanaka@dashme.io",
    },
    {
      title: "Legacy Trello import",
      description: "One-shot migration tool for old workspace data.",
      status: ProjectStatus.PAUSED,
      priority: ProjectPriority.LOW,
      owner: "jotavtech@dashme.io",
    },
    {
      title: "Public docs site",
      description: "Surface architecture, design system and agent contracts.",
      status: ProjectStatus.PLANNED,
      priority: ProjectPriority.MEDIUM,
      owner: "amartins@dashme.io",
    },
    {
      title: "Auth + roles hardening",
      description: "Founder / engineer / designer / guest scopes.",
      status: ProjectStatus.ACTIVE,
      priority: ProjectPriority.HIGH,
      owner: "rsantos@dashme.io",
    },
  ];

  for (const p of projects) {
    await prisma.project.create({ data: p });
  }

  console.log("[seed] tasks…");
  const allProjects = await prisma.project.findMany();
  const today = new Date();

  for (const proj of allProjects) {
    const n = 3 + Math.floor(Math.random() * 4);
    for (let i = 0; i < n; i++) {
      const done = Math.random() > 0.4;
      const ago = Math.floor(Math.random() * 7);
      const completedAt = new Date(today);
      completedAt.setDate(completedAt.getDate() - ago);

      await prisma.task.create({
        data: {
          title: `Task ${i + 1} · ${proj.title.split(" ").slice(0, 3).join(" ")}`,
          description: null,
          status: done ? TaskStatus.DONE : (Math.random() > 0.5 ? TaskStatus.DOING : TaskStatus.TODO),
          projectId: proj.id,
          assignee: proj.owner,
          completedAt: done ? completedAt : null,
        },
      });
    }
  }

  console.log("[seed] done");
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
