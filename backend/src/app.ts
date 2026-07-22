import express from "express";
import cookieParser from "cookie-parser";
import cors from "cors";
import helmet from "helmet";
import morgan from "morgan";
import { env } from "./lib/env.js";
import { requireAuth } from "./middlewares/auth.js";
import { errorHandler, notFound } from "./middlewares/error.js";
import { authRouter } from "./routes/auth.js";
import { healthRouter } from "./routes/health.js";
import { projectsRouter } from "./routes/projects.js";
import { tasksRouter } from "./routes/tasks.js";
import { analyticsRouter } from "./routes/analytics.js";
import { aiRouter } from "./routes/ai.js";

export function createApp() {
  const app = express();

  app.use(helmet());
  app.use(
    cors({
      origin: env.CORS_ORIGIN.split(",").map((s) => s.trim()),
      credentials: true,
    }),
  );
  app.use(express.json({ limit: "1mb" }));
  app.use(cookieParser());
  if (env.NODE_ENV !== "test") app.use(morgan("dev"));

  app.use("/api/health", healthRouter);
  app.use("/api/auth", authRouter);
  app.use("/api/projects", requireAuth, projectsRouter);
  app.use("/api/tasks", requireAuth, tasksRouter);
  app.use("/api/analytics", requireAuth, analyticsRouter);
  app.use("/api/ai", requireAuth, aiRouter);

  app.use(notFound);
  app.use(errorHandler);

  return app;
}
