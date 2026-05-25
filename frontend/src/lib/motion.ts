import type { Transition, Variants } from "framer-motion";

export const ease = {
  outExpo: [0.16, 1, 0.3, 1] as const,
  inOutExpo: [0.87, 0, 0.13, 1] as const,
};

export const spring = {
  soft: { type: "spring" as const, stiffness: 180, damping: 28, mass: 0.9 },
  precise: { type: "spring" as const, stiffness: 240, damping: 30 },
  snap: { type: "spring" as const, stiffness: 360, damping: 32 },
} satisfies Record<string, Transition>;

export const fadeUp: Variants = {
  hidden: { opacity: 0, y: 24 },
  show: {
    opacity: 1,
    y: 0,
    transition: { duration: 0.7, ease: ease.outExpo },
  },
};

export const stagger = (delay = 0.08): Variants => ({
  hidden: {},
  show: {
    transition: { staggerChildren: delay },
  },
});
