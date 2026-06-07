-- CreateTable
CREATE TABLE "ai_insights" (
    "id" TEXT NOT NULL,
    "type" TEXT NOT NULL,
    "prompt" TEXT NOT NULL,
    "output" TEXT NOT NULL,
    "context" JSONB,
    "model" TEXT NOT NULL,
    "createdAt" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT "ai_insights_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE INDEX "ai_insights_type_createdAt_idx" ON "ai_insights"("type", "createdAt");
