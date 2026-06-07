import type { ReactNode } from "react";
import { motion, useReducedMotion } from "framer-motion";
import { cn } from "@/lib/cn";

type PaneFrameProps = {
  index?: string;
  label?: string;
  meta?: string;
  children: ReactNode;
  className?: string;
  inner?: string;
  hoverGlow?: boolean;
  delay?: number;
};

function Corner({ pos }: { pos: "tl" | "tr" | "bl" | "br" }) {
  const map = {
    tl: "top-0 left-0",
    tr: "top-0 right-0",
    bl: "bottom-0 left-0",
    br: "bottom-0 right-0",
  } as const;
  return (
    <span className={cn("pointer-events-none absolute z-10 h-3 w-3", map[pos])}>
      <span
        className={cn(
          "absolute h-px w-3 bg-fg-subtle",
          pos.startsWith("t") ? "top-0" : "bottom-0",
          pos.endsWith("l") ? "left-0" : "right-0",
        )}
      />
      <span
        className={cn(
          "absolute h-3 w-px bg-fg-subtle",
          pos.startsWith("t") ? "top-0" : "bottom-0",
          pos.endsWith("l") ? "left-0" : "right-0",
        )}
      />
    </span>
  );
}

export function PaneFrame({
  index,
  label,
  meta,
  children,
  className,
  inner,
  hoverGlow = true,
  delay = 0,
}: PaneFrameProps) {
  const reduce = useReducedMotion();

  return (
    <motion.div
      initial={reduce ? false : { opacity: 0, y: 16 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.6, ease: [0.16, 1, 0.3, 1], delay }}
      whileHover={hoverGlow && !reduce ? { y: -2 } : undefined}
      className={cn("group relative", className)}
    >
      <Corner pos="tl" />
      <Corner pos="tr" />
      <Corner pos="bl" />
      <Corner pos="br" />

      {(label || meta) && (
        <div className="pointer-events-none absolute -top-3 left-3 right-3 z-20 flex items-center justify-between">
          {label && (
            <span className="bg-surface px-2 py-0.5 font-mono text-[10px] uppercase tracking-[0.32em]">
              {index && (
                <span className="text-fg-subtle">
                  {index}
                  <span className="mx-2 text-fg-faint">·</span>
                </span>
              )}
              <span className="text-fg-muted">{label}</span>
            </span>
          )}
          {meta && (
            <span className="bg-surface px-2 py-0.5 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
              {meta}
            </span>
          )}
        </div>
      )}

      <div
        className={cn(
          "relative border border-hairline bg-surface-sunken/60 backdrop-blur-[2px]",
          "transition-[box-shadow,border-color] duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]",
          hoverGlow && "group-hover:border-hairline-strong group-hover:shadow-glow-soft",
          inner,
        )}
      >
        {children}
      </div>
    </motion.div>
  );
}
