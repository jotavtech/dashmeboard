import request from "supertest";
import { afterAll, beforeAll, describe, expect, it } from "vitest";
import { createApp } from "./app.js";
import { disconnect } from "./lib/prisma.js";
import { resetAllData } from "./test/authHelper.js";

// Isolated file: the login limiter is module-level state, so exhausting it
// here must not bleed into the other suites (vitest isolates per file).
const app = createApp();

beforeAll(async () => {
  await resetAllData();
});

afterAll(async () => {
  await disconnect();
});

describe("auth rate limiting", () => {
  it("throttles brute-force logins with 429 after the window budget", async () => {
    let last = 0;
    for (let i = 0; i < 21; i++) {
      const res = await request(app)
        .post("/api/auth/login")
        .send({ email: "brute@dashme.io", password: "wrong-password" });
      last = res.status;
    }
    expect(last).toBe(429);
  });
});
