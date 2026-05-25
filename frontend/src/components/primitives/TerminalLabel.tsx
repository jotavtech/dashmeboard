import type { ReactNode } from "react";
import { cn } from "@/lib/cn";

type TerminalLabelProps = {
  children: ReactNode;
  variant?: "default" | "rust";
  className?: string;
};

export function TerminalLabel({
  children,
  variant = "default",
  className,
}: TerminalLabelProps) {
  return (
    <span
      className={cn(
        "inline-flex items-center gap-2 font-mono uppercase",
        "text-eyebrow tracking-[0.32em]",
        variant === "rust" ? "text-accent" : "text-fg-muted",
        className,
      )}
    >
      <span
        className={cn(
          "h-1.5 w-1.5 rounded-full",
          variant === "rust" ? "bg-accent" : "bg-fg-muted",
        )}
      />
      {children}
    </span>
  );
}
