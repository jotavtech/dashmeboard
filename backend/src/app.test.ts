import request from "supertest";
import { afterAll, beforeEach, describe, expect, it } from "vitest";
import { createApp } from "./app.js";
import { disconnect } from "./lib/prisma.js";
import { registerOwner, resetAllData } from "./test/authHelper.js";

const app = createApp();

let owner: string;
let bearer: string;

beforeEach(async () => {
  await resetAllData();
  const session = await registerOwner(app, { email: "quality@dashme.io" });
  owner = session.email;
  bearer = `Bearer ${session.accessToken}`;
});

afterAll(async () => {
  await resetAllData();
  await disconnect();
});

describe("health", () => {
  it("reports API liveness", async () => {
    const res = await request(app).get("/api/health").expect(200);

    expect(res.body.status).toBe("ok");
    expect(res.body.services.api).toBe("up");
  });

  it("reports API and database readiness", async () => {
    const res = await request(app).get("/api/health/ready").expect(200);

    expect(res.body.status).toBe("ok");
    expect(res.body.services.database).toBe("up");
  });
});

describe("projects CRUD", () => {
  it("creates, lists, updates and deletes a project", async () => {
    const created = await request(app)
      .post("/api/projects")
      .set("Authorization", bearer)
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

    const listed = await request(app)
      .get("/api/projects")
      .set("Authorization", bearer)
      .expect(200);
    expect(listed.body).toHaveLength(1);

    const updated = await request(app)
      .patch(`/api/projects/${created.body.id}`)
      .set("Authorization", bearer)
      .send({ status: "DONE", priority: "CRITICAL" })
      .expect(200);

    expect(updated.body.status).toBe("DONE");
    expect(updated.body.priority).toBe("CRITICAL");

    await request(app)
      .delete(`/api/projects/${created.body.id}`)
      .set("Authorization", bearer)
      .expect(204);

    const afterDelete = await request(app)
      .get("/api/projects")
      .set("Authorization", bearer)
      .expect(200);
    expect(afterDelete.body).toHaveLength(0);
  });

  it("returns validation errors for invalid payloads", async () => {
    const res = await request(app)
      .post("/api/projects")
      .set("Authorization", bearer)
      .send({ title: "", owner: "not-an-email" })
      .expect(400);

    expect(res.body.message).toBe("Validation failed");
    expect(res.body.issues.title).toBeDefined();
    expect(res.body.issues.owner).toBeDefined();
  });

  it("rejects unauthenticated access", async () => {
    await request(app).get("/api/projects").expect(401);
    await request(app).post("/api/projects").send({ title: "x", owner }).expect(401);
  });
});

describe("ai insights", () => {
  // Read-only: never triggers a paid OpenAI call.
  it("lists persisted insights as an array", async () => {
    const res = await request(app)
      .get("/api/ai/insights")
      .set("Authorization", bearer)
      .expect(200);
    expect(Array.isArray(res.body)).toBe(true);
  });

  it("rejects an invalid project-plan payload", async () => {
    const res = await request(app)
      .post("/api/ai/project-plan")
      .set("Authorization", bearer)
      .send({ projectId: "not-a-uuid" })
      .expect(400);

    expect(res.body.message).toBe("Validation failed");
  });
});
