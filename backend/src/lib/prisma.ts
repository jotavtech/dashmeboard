import "dotenv/config";
import { PrismaClient } from "@prisma/client";
import { PrismaPg } from "@prisma/adapter-pg";

let client: PrismaClient | null = null;

function createClient() {
  const connectionString = process.env.DATABASE_URL;
  if (!connectionString) {
    throw new Error("DATABASE_URL is not set");
  }

  // Prisma Postgres is reached over a direct TCP connection via the node-postgres
  // driver adapter (the pooling/SSL is handled by the connection string itself).
  const adapter = new PrismaPg({ connectionString });

  return new PrismaClient({
    adapter,
    log: process.env.NODE_ENV === "development" ? ["query", "warn", "error"] : ["warn", "error"],
  });
}

function getClient() {
  client ??= createClient();
  return client;
}

export const prisma = new Proxy({} as PrismaClient, {
  get(_target, prop) {
    const activeClient = getClient();
    const value = activeClient[prop as keyof PrismaClient];
    return typeof value === "function" ? value.bind(activeClient) : value;
  },
});

export async function disconnect() {
  if (client) await client.$disconnect();
}
