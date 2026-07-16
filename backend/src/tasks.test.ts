import request from "supertest";
import { afterAll, beforeEach, describe, expect, it } from "vitest";
import { createApp } from "./app.js";
import { prisma, disconnect } from "./lib/prisma.js";

const app = createApp();
const owner = "quality@dashme.io";

async function resetData() {
  await prisma.task.deleteMany();
  await prisma.project.deleteMany();
  await prisma.user.deleteMany();
  await prisma.user.create({
    data: { email: owner, name: "Quality Operator" },
  });
}

async function createProject(overrides: Record<string, unknown> = {}) {
  const res = await request(app)
    .post("/api/projects")
    .send({ title: "Board project", owner, ...overrides })
    .expect(201);
  return res.body as { id: string };
}

beforeEach(async () => {
  await resetData();
});

afterAll(async () => {
  await resetData();
  await disconnect();
});

describe("project V2 context fields", () => {
  it("stores deadline, links, tags and notes", async () => {
    const res = await request(app)
      .post("/api/projects")
      .send({
        title: "Client project",
        owner,
        deadline: "2026-08-01T12:00:00.000Z",
        client: "Cartório Dinah",
        repoUrl: "https://github.com/jotavtech/dinah-correa",
        deployUrl: "https://dinah.example.com",
        docsUrl: "https://github.com/jotavtech/dinah-correa/blob/main/docs/PRD.md",
        activeBranch: "main",
        notes: "## Notas\n\ncontexto do projeto",
        tags: ["landing", "next"],
      })
      .expect(201);

    expect(res.body.deadline).toBe("2026-08-01T12:00:00.000Z");
    expect(res.body.tags).toEqual(["landing", "next"]);
    expect(res.body.client).toBe("Cartório Dinah");

    const listed = await request(app).get("/api/projects?tag=landing").expect(200);
    expect(listed.body).toHaveLength(1);
    expect(listed.body[0].taskCount).toBe(0);

    const empty = await request(app).get("/api/projects?tag=nope").expect(200);
    expect(empty.body).toHaveLength(0);
  });

  it("treats empty strings as null for optional urls", async () => {
    const res = await request(app)
      .post("/api/projects")
      .send({ title: "No links", owner, repoUrl: "", notes: "" })
      .expect(201);

    expect(res.body.repoUrl).toBeNull();
    expect(res.body.notes).toBeNull();
  });

  it("rejects an invalid repoUrl", async () => {
    const res = await request(app)
      .post("/api/projects")
      .send({ title: "Bad link", owner, repoUrl: "not-a-url" })
      .expect(400);
    expect(res.body.message).toBe("Validation failed");
  });
});

describe("tasks CRUD", () => {
  it("creates, updates, moves and deletes a task", async () => {
    const project = await createProject();

    const created = await request(app)
      .post(`/api/projects/${project.id}/tasks`)
      .send({ title: "Wire kanban", dueDate: "2026-07-20T00:00:00.000Z" })
      .expect(201);

    expect(created.body.status).toBe("TODO");
    expect(created.body.order).toBe(0);
    expect(created.body.completedAt).toBeNull();
    expect(created.body.project.title).toBe("Board project");

    // segunda task na mesma coluna entra no fim
    const second = await request(app)
      .post(`/api/projects/${project.id}/tasks`)
      .send({ title: "Second" })
      .expect(201);
    expect(second.body.order).toBe(1);

    const moved = await request(app)
      .patch(`/api/tasks/${created.body.id}`)
      .send({ status: "DONE", order: 0 })
      .expect(200);
    expect(moved.body.completedAt).not.toBeNull();

    // sair de DONE limpa completedAt
    const reopened = await request(app)
      .patch(`/api/tasks/${created.body.id}`)
      .send({ status: "DOING" })
      .expect(200);
    expect(reopened.body.completedAt).toBeNull();

    // detalhe do projeto traz as tasks ordenadas
    const detail = await request(app).get(`/api/projects/${project.id}`).expect(200);
    expect(detail.body.tasks).toHaveLength(2);

    await request(app).delete(`/api/tasks/${created.body.id}`).expect(204);
    const after = await request(app).get(`/api/projects/${project.id}`).expect(200);
    expect(after.body.tasks).toHaveLength(1);
  });

  it("404s for a task on a missing project and for a missing task", async () => {
    await request(app)
      .post("/api/projects/00000000-0000-0000-0000-000000000000/tasks")
      .send({ title: "Orphan" })
      .expect(404);

    await request(app)
      .patch("/api/tasks/00000000-0000-0000-0000-000000000000")
      .send({ title: "Ghost" })
      .expect(404);
  });

  it("validates the task payload", async () => {
    const project = await createProject();
    const res = await request(app)
      .post(`/api/projects/${project.id}/tasks`)
      .send({ title: "", status: "INVALID" })
      .expect(400);
    expect(res.body.message).toBe("Validation failed");
  });
});

describe("analytics", () => {
  it("reflects projects and tasks in the overview", async () => {
    const project = await createProject({ status: "ACTIVE" });
    await request(app)
      .post(`/api/projects/${project.id}/tasks`)
      .send({ title: "Done task", status: "DONE" })
      .expect(201);

    const res = await request(app).get("/api/analytics/overview").expect(200);
    expect(res.body.totalProjects).toBe(1);
    expect(res.body.activeProjects).toBe(1);
    expect(res.body.totalTasks).toBe(1);
    expect(res.body.completedTasks).toBe(1);
    expect(res.body.projectsByStatus.ACTIVE).toBe(1);
  });

  it("splits deadlines into overdue and upcoming", async () => {
    const past = new Date(Date.now() - 86_400_000).toISOString();
    const nextWeek = new Date(Date.now() + 3 * 86_400_000).toISOString();
    const farFuture = new Date(Date.now() + 30 * 86_400_000).toISOString();

    await createProject({ title: "Late project", deadline: past });
    const soon = await createProject({ title: "Soon project", deadline: nextWeek });
    await createProject({ title: "Far project", deadline: farFuture });
    // projeto DONE atrasado não aparece
    await createProject({ title: "Done late", deadline: past, status: "DONE" });

    await request(app)
      .post(`/api/projects/${soon.id}/tasks`)
      .send({ title: "Late task", dueDate: past })
      .expect(201);

    const res = await request(app).get("/api/analytics/deadlines").expect(200);
    expect(res.body.overdueProjects.map((p: { title: string }) => p.title)).toEqual([
      "Late project",
    ]);
    expect(res.body.upcomingProjects.map((p: { title: string }) => p.title)).toEqual([
      "Soon project",
    ]);
    expect(res.body.overdueTasks).toHaveLength(1);
    expect(res.body.overdueTasks[0].project.title).toBe("Soon project");
    expect(res.body.upcomingTasks).toHaveLength(0);
  });

  it("reports activity and throughput", async () => {
    const project = await createProject({ status: "ACTIVE" });
    await request(app)
      .post(`/api/projects/${project.id}/tasks`)
      .send({ title: "Shipped", status: "DONE" })
      .expect(201);

    const activity = await request(app).get("/api/analytics/activity").expect(200);
    expect(activity.body.length).toBeGreaterThanOrEqual(2);

    const throughput = await request(app).get("/api/analytics/throughput").expect(200);
    expect(throughput.body).toHaveLength(7);
    const total = throughput.body.reduce((acc: number, d: { value: number }) => acc + d.value, 0);
    expect(total).toBe(1);

    const database = await request(app).get("/api/analytics/database").expect(200);
    expect(database.body.tables.find((t: { name: string }) => t.name === "tasks").rows).toBe(1);
  });
});
