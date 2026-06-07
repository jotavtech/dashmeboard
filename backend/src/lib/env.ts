import "dotenv/config";
import { z } from "zod";

const schema = z.object({
  NODE_ENV: z.enum(["development", "production", "test"]).default("development"),
  BACKEND_PORT: z.coerce.number().default(4000),
  DATABASE_URL: z.string().url(),
  CORS_ORIGIN: z.string().default("http://localhost:5173"),
  // Optional on purpose: the app (and the test suite) must boot without an
  // OpenAI key. AI routes degrade to a 503 at call time instead of crashing
  // the whole process at startup.
  OPENAI_API_KEY: z.string().min(1).optional(),
  OPENAI_MODEL: z.string().min(1).default("gpt-4o"),
});

const parsed = schema.safeParse(process.env);
if (!parsed.success) {
  console.error("[env] invalid environment", parsed.error.flatten().fieldErrors);
  process.exit(1);
}

export const env = parsed.data;
