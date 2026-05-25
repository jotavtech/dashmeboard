import { Router } from "express";
import { projectsController } from "../controllers/projects.controller.js";
import { asyncHandler } from "../middlewares/async.js";

export const projectsRouter = Router();

projectsRouter.get("/", asyncHandler(projectsController.list));
projectsRouter.get("/:id", asyncHandler(projectsController.get));
projectsRouter.post("/", asyncHandler(projectsController.create));
projectsRouter.patch("/:id", asyncHandler(projectsController.update));
projectsRouter.delete("/:id", asyncHandler(projectsController.remove));
