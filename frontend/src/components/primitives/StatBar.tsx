import { cn } from "@/lib/cn";

type StatBarProps = {
  value: number;
  max?: number;
  className?: string;
};

export function StatBar({ value, max = 5, className }: StatBarProps) {
  return (
    <div className={cn("flex items-center gap-0.5", className)}>
      {Array.from({ length: max }).map((_, i) => (
        <div
          key={i}
          className={cn(
            "h-[3px] w-full transition-colors duration-300",
            i < value ? "bg-fg-muted" : "bg-hairline",
          )}
        />
      ))}
    </div>
  );
}
