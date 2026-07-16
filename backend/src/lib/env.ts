import "dotenv/config";
import { z } from "zod";

const emptyToUndefined = (value: unknown) => (value === "" ? undefined : value);

const optionalPort = z.preprocess(
  emptyToUndefined,
  z.coerce.number().int().positive().optional(),
);

const schema = z.object({
  NODE_ENV: z.enum(["development", "production", "test"]).default("development"),
  // Railway (and most PaaS) inject PORT and route traffic to it. Prefer it when
  // present; fall back to BACKEND_PORT for local development.
  PORT: optionalPort,
  BACKEND_PORT: optionalPort.default(4000),
  // Optional at boot so the platform liveness check can pass even while the
  // database is being configured. DB-backed routes and readiness still fail
  // clearly until the variable is set.
  DATABASE_URL: z.preprocess(emptyToUndefined, z.string().url().optional()),
  CORS_ORIGIN: z.string().default("http://localhost:5173"),
  // Optional on purpose: the app (and the test suite) must boot without an
  // OpenAI key. AI routes degrade to a 503 at call time instead of crashing
  // the whole process at startup. `.env.example` ships the key as an empty
  // string, so empty must behave the same as unset.
  OPENAI_API_KEY: z.preprocess(emptyToUndefined, z.string().min(1).optional()),
  OPENAI_MODEL: z.preprocess(emptyToUndefined, z.string().min(1).default("gpt-4o")),
});

const parsed = schema.safeParse(process.env);
if (!parsed.success) {
  console.error("[env] invalid environment", parsed.error.flatten().fieldErrors);
  process.exit(1);
}

export const env = parsed.data;
