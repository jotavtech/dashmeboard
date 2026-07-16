// Safety guard for the test suite.
//
// The integration tests are destructive (deleteMany on users/projects/tasks),
// so they must never run against a shared or production database. This file is
// loaded via vitest `setupFiles` before any test file executes.
import "dotenv/config";

const LOCAL_HOSTS = new Set(["localhost", "127.0.0.1", "::1", "postgres"]);

const testUrl = process.env.TEST_DATABASE_URL?.trim();
if (testUrl) {
  // Explicit opt-in (used by CI with a disposable database service).
  process.env.DATABASE_URL = testUrl;
}

const databaseUrl = process.env.DATABASE_URL?.trim();
if (!databaseUrl) {
  throw new Error(
    "[test-guard] DATABASE_URL is not set. Point it at a local database " +
      "(docker compose up -d postgres) or set TEST_DATABASE_URL explicitly.",
  );
}

let hostname: string;
try {
  hostname = new URL(databaseUrl).hostname;
} catch {
  throw new Error("[test-guard] DATABASE_URL is not a valid URL.");
}

if (!testUrl && !LOCAL_HOSTS.has(hostname)) {
  throw new Error(
    `[test-guard] Refusing to run destructive tests against non-local database host "${hostname}". ` +
      "These tests wipe users/projects/tasks. Use the local Docker Postgres " +
      "(docker compose up -d postgres) or set TEST_DATABASE_URL to a disposable database.",
  );
}
