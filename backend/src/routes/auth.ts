import { Router } from "express";
import * as authController from "../controllers/auth.controller.js";
import { asyncHandler } from "../middlewares/async.js";
import { requireAuth } from "../middlewares/auth.js";
import { rateLimit } from "../middlewares/rateLimit.js";

export const authRouter = Router();

// Credential endpoints are brute-force targets; refresh spins on every tab.
const credentialLimiter = rateLimit({ windowMs: 15 * 60 * 1000, max: 20 });
const refreshLimiter = rateLimit({ windowMs: 60 * 1000, max: 30 });

authRouter.post("/register", credentialLimiter, asyncHandler(authController.register));
authRouter.post("/login", credentialLimiter, asyncHandler(authController.login));
authRouter.post("/refresh", refreshLimiter, asyncHandler(authController.refresh));
authRouter.post("/logout", asyncHandler(authController.logout));
authRouter.get("/me", requireAuth, asyncHandler(authController.me));
