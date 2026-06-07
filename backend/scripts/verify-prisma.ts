import { prisma, disconnect } from "../src/lib/prisma.js";

async function main() {
  // One read through the driver adapter to prove the connection works.
  const [users, projects, tasks] = await Promise.all([
    prisma.user.count(),
    prisma.project.count(),
    prisma.task.count(),
  ]);
  console.log(`✅ Connected — users: ${users}, projects: ${projects}, tasks: ${tasks}`);
}

main()
  .catch((error) => {
    console.error("❌ Prisma connection failed:");
    console.error(error);
    process.exit(1);
  })
  .finally(async () => {
    await disconnect();
  });
