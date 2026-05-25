import { cn } from "@/lib/cn";

type StatusDotProps = {
  variant?: "live" | "idle" | "warn" | "rust";
  label?: string;
  className?: string;
};

const variantColor = {
  live: "bg-emerald-400",
  idle: "bg-fg-subtle",
  warn: "bg-amber-400",
  rust: "bg-accent",
} as const;

export function StatusDot({ variant = "live", label, className }: StatusDotProps) {
  return (
    <span
      className={cn(
        "inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-muted",
        className,
      )}
    >
      <span className="relative inline-flex h-1.5 w-1.5">
        <span
          className={cn(
            "absolute inset-0 rounded-full opacity-60 animate-ping",
            variantColor[variant],
          )}
        />
        <span className={cn("relative h-1.5 w-1.5 rounded-full", variantColor[variant])} />
      </span>
      {label}
    </span>
  );
}
