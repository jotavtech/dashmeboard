-- AlterTable
ALTER TABLE "projects" ADD COLUMN     "activeBranch" TEXT,
ADD COLUMN     "client" TEXT,
ADD COLUMN     "deadline" TIMESTAMP(3),
ADD COLUMN     "deployUrl" TEXT,
ADD COLUMN     "docsUrl" TEXT,
ADD COLUMN     "notes" TEXT,
ADD COLUMN     "repoUrl" TEXT,
ADD COLUMN     "tags" TEXT[] DEFAULT ARRAY[]::TEXT[];

-- AlterTable
ALTER TABLE "tasks" ADD COLUMN     "dueDate" TIMESTAMP(3),
ADD COLUMN     "order" INTEGER NOT NULL DEFAULT 0;
