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

beforeEach(async () => {
  await resetData();
});

afterAll(async () => {
  await resetData();
  await disconnect();
});

describe("health", () => {
  it("reports API and database status", async () => {
    const res = await request(app).get("/api/health").expect(200);

    expect(res.body.status).toBe("ok");
    expect(res.body.services.database).toBe("up");
  });
});

describe("projects CRUD", () => {
  it("creates, lists, updates and deletes a project", async () => {
    const created = await request(app)
      .post("/api/projects")
      .send({
        title: "Production hardening",
        description: "CI, Docker and SonarCloud cleanup",
        status: "ACTIVE",
        priority: "HIGH",
        owner,
      })
      .expect(201);

    expect(created.body.id).toEqual(expect.any(String));
    expect(created.body.title).toBe("Production hardening");

    const listed = await request(app).get("/api/projects").expect(200);
    expect(listed.body).toHaveLength(1);

    const updated = await request(app)
      .patch(`/api/projects/${created.body.id}`)
      .send({ status: "DONE", priority: "CRITICAL" })
      .expect(200);

    expect(updated.body.status).toBe("DONE");
    expect(updated.body.priority).toBe("CRITICAL");

    await request(app).delete(`/api/projects/${created.body.id}`).expect(204);

    const afterDelete = await request(app).get("/api/projects").expect(200);
    expect(afterDelete.body).toHaveLength(0);
  });

  it("returns validation errors for invalid payloads", async () => {
    const res = await request(app)
      .post("/api/projects")
      .send({ title: "", owner: "not-an-email" })
      .expect(400);

    expect(res.body.message).toBe("Validation failed");
    expect(res.body.issues.title).toBeDefined();
    expect(res.body.issues.owner).toBeDefined();
  });
});
