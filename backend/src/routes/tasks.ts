import { Router } from "express";
import { tasksController } from "../controllers/tasks.controller.js";
import { asyncHandler } from "../middlewares/async.js";

export const tasksRouter = Router();

tasksRouter.patch("/:id", asyncHandler(tasksController.update));
tasksRouter.delete("/:id", asyncHandler(tasksController.remove));
