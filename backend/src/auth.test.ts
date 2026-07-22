import request from "supertest";
import { afterAll, beforeEach, describe, expect, it } from "vitest";
import { createApp } from "./app.js";
import { prisma, disconnect } from "./lib/prisma.js";
import { registerOwner, resetAllData, TEST_PASSWORD } from "./test/authHelper.js";

const app = createApp();

function refreshCookie(cookies: string[]): string {
  const cookie = cookies.find((c) => c.startsWith("dashme_refresh="));
  expect(cookie).toBeDefined();
  return cookie!;
}

beforeEach(async () => {
  await resetAllData();
});

afterAll(async () => {
  await resetAllData();
  await disconnect();
});

describe("register", () => {
  it("creates user, organization and owner membership, returns session", async () => {
    const session = await registerOwner(app, { organizationName: "Clínica Sorriso" });

    expect(session.accessToken).toEqual(expect.any(String));
    expect(session.organization.slug).toBe("clinica-sorriso");
    expect(refreshCookie(session.cookies)).toContain("HttpOnly");

    const membership = await prisma.membership.findFirstOrThrow({
      where: { userId: session.user.id },
    });
    expect(membership.roleKey).toBe("OWNER");
    expect(membership.organizationId).toBe(session.organization.id);
  });

  it("rejects a duplicate e-mail with 409", async () => {
    await registerOwner(app);
    await request(app)
      .post("/api/auth/register")
      .send({
        name: "Outra Pessoa",
        email: "owner@dashme.io",
        password: TEST_PASSWORD,
        organizationName: "Outra Empresa",
      })
      .expect(409);
  });

  it("rejects weak payloads with 400", async () => {
    const res = await request(app)
      .post("/api/auth/register")
      .send({ name: "A", email: "not-an-email", password: "123", organizationName: "" })
      .expect(400);
    expect(res.body.message).toBe("Validation failed");
  });
});

describe("login and me", () => {
  it("logs in with valid credentials and reads /me", async () => {
    const { email, organization } = await registerOwner(app);

    const login = await request(app)
      .post("/api/auth/login")
      .send({ email, password: TEST_PASSWORD })
      .expect(200);

    const me = await request(app)
      .get("/api/auth/me")
      .set("Authorization", `Bearer ${login.body.accessToken}`)
      .expect(200);

    expect(me.body.organization.id).toBe(organization.id);
    expect(me.body.roleKey).toBe("OWNER");
  });

  it("rejects bad credentials with 401 without leaking which field failed", async () => {
    const { email } = await registerOwner(app);

    const wrongPass = await request(app)
      .post("/api/auth/login")
      .send({ email, password: "senha-errada-123" })
      .expect(401);
    const wrongUser = await request(app)
      .post("/api/auth/login")
      .send({ email: "ghost@dashme.io", password: "senha-errada-123" })
      .expect(401);

    expect(wrongPass.body.message).toBe(wrongUser.body.message);
  });

  it("blocks protected routes without a token", async () => {
    await request(app).get("/api/projects").expect(401);
    await request(app).get("/api/auth/me").expect(401);
  });
});

describe("refresh rotation", () => {
  it("rotates the refresh token and keeps the session alive", async () => {
    const { cookies, user } = await registerOwner(app);

    const first = await request(app)
      .post("/api/auth/refresh")
      .set("Cookie", refreshCookie(cookies))
      .expect(200);

    expect(first.body.accessToken).toEqual(expect.any(String));
    const rotated = refreshCookie(first.headers["set-cookie"] as unknown as string[]);
    expect(rotated).not.toBe(refreshCookie(cookies));

    // New cookie keeps working…
    await request(app).post("/api/auth/refresh").set("Cookie", rotated).expect(200);

    // …and the chain is recorded in the database.
    const tokens = await prisma.refreshToken.findMany({ where: { userId: user.id } });
    expect(tokens.filter((t) => t.revokedAt !== null).length).toBeGreaterThanOrEqual(2);
  });

  it("revokes every session when a rotated token is reused", async () => {
    const { cookies, user } = await registerOwner(app);
    const oldCookie = refreshCookie(cookies);

    const rotatedRes = await request(app)
      .post("/api/auth/refresh")
      .set("Cookie", oldCookie)
      .expect(200);
    const newCookie = refreshCookie(rotatedRes.headers["set-cookie"] as unknown as string[]);

    // Replay of the already-rotated cookie → theft signal.
    await request(app).post("/api/auth/refresh").set("Cookie", oldCookie).expect(401);

    // The freshly issued cookie must be dead too.
    await request(app).post("/api/auth/refresh").set("Cookie", newCookie).expect(401);

    const alive = await prisma.refreshToken.count({
      where: { userId: user.id, revokedAt: null },
    });
    expect(alive).toBe(0);
  });

  it("rejects refresh without a cookie", async () => {
    await request(app).post("/api/auth/refresh").expect(401);
  });
});

describe("logout", () => {
  it("revokes the session and clears the cookie", async () => {
    const { cookies } = await registerOwner(app);
    const cookie = refreshCookie(cookies);

    const res = await request(app).post("/api/auth/logout").set("Cookie", cookie).expect(204);
    const cleared = (res.headers["set-cookie"] as unknown as string[]).find((c) =>
      c.startsWith("dashme_refresh=;"),
    );
    expect(cleared).toBeDefined();

    await request(app).post("/api/auth/refresh").set("Cookie", cookie).expect(401);
  });
});
