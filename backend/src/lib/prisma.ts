import "dotenv/config";
import { PrismaClient } from "@prisma/client";
import { PrismaPg } from "@prisma/adapter-pg";

const connectionString = process.env.DATABASE_URL;
if (!connectionString) {
  throw new Error("DATABASE_URL is not set");
}

// Prisma Postgres is reached over a direct TCP connection via the node-postgres
// driver adapter (the pooling/SSL is handled by the connection string itself).
const adapter = new PrismaPg({ connectionString });

export const prisma = new PrismaClient({
  adapter,
  log: process.env.NODE_ENV === "development" ? ["query", "warn", "error"] : ["warn", "error"],
});

export async function disconnect() {
  await prisma.$disconnect();
}
