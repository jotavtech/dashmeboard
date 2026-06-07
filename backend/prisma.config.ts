import "dotenv/config";
import { defineConfig } from "prisma/config";

// The datasource URL stays in prisma/schema.prisma (env("DATABASE_URL")).
// This config wires the schema path and the seed command so `prisma db seed`
// works without relying solely on package.json#prisma.seed.
export default defineConfig({
  schema: "prisma/schema.prisma",
  migrations: {
    path: "prisma/migrations",
    seed: "tsx prisma/seed.ts",
  },
});
