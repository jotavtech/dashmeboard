import type { Throughput } from "@/services/analytics";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { cn } from "@/lib/cn";

type ThroughputChartProps = {
  data: Throughput[];
};

export function ThroughputChart({ data }: ThroughputChartProps) {
  const max = Math.max(1, ...data.map((d) => d.value));

  return (
    <PaneFrame index="04" label="Throughput · 7d" meta="ops / day">
      <div className="px-6 pt-10 pb-6">
        <div className="grid grid-cols-7 gap-3 items-end h-[180px]">
          {data.map((d, i) => {
            const h = Math.max(4, (d.value / max) * 160);
            const peak = d.value === max;
            return (
              <div key={d.day} className="flex flex-col items-stretch gap-2">
                <div className="relative flex-1 flex items-end">
                  <div
                    className={cn(
                      "w-full transition-all duration-500 ease-out-expo",
                      peak ? "bg-accent" : "bg-fg-muted",
                    )}
                    style={{ height: `${h}px`, transitionDelay: `${i * 60}ms` }}
                  />
                </div>
                <span className="text-center font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                  {d.day}
                </span>
                <span className="text-center font-mono text-[10px] tabular-nums text-fg-muted">
                  {d.value}
                </span>
              </div>
            );
          })}
        </div>
      </div>
    </PaneFrame>
  );
}
