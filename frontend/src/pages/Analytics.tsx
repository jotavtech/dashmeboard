import { PageShell } from "@/components/sections/PageShell";
import { StatGrid, type Stat } from "@/components/sections/StatGrid";
import { ThroughputChart } from "@/components/sections/ThroughputChart";
import { PaneFrame } from "@/components/primitives/PaneFrame";

const stats: Stat[] = [
  { index: "01", label: "Requests / day", value: "14.2", suffix: "k", caption: "p95 across all services." },
  { index: "02", label: "Median latency", value: "84", suffix: "ms", caption: "Backend → frontend round-trip." },
  { index: "03", label: "Error budget", value: "97", suffix: "%", caption: "Remaining for current SLO window." },
  { index: "04", label: "Build pipelines", value: "184", caption: "Total runs across all workflows." },
];

const throughput = [
  { day: "MON", value: 1240 },
  { day: "TUE", value: 1580 },
  { day: "WED", value: 1420 },
  { day: "THU", value: 1810 },
  { day: "FRI", value: 1650 },
  { day: "SAT", value: 620 },
  { day: "SUN", value: 480 },
];

const heatmapRows = 7;
const heatmapCols = 24;

const heatmapScale = [
  "bg-surface-raised",
  "bg-fg-faint/30",
  "bg-fg-subtle/40",
  "bg-accent/45",
  "bg-accent",
];

function heatmapCellTone(v: number) {
  if (v >= 0.85) return heatmapScale[4];
  if (v >= 0.6) return heatmapScale[3];
  if (v >= 0.35) return heatmapScale[2];
  if (v >= 0.15) return heatmapScale[1];
  return heatmapScale[0];
}

export default function AnalyticsPage() {
  return (
    <PageShell
      section="section · analytics"
      marker="signals"
      title={<>Production telemetry, distilled.</>}
      description="Throughput, latency, error budgets and the heatmap of operational pressure across the past week."
    >
      <StatGrid stats={stats} />

      <div className="mt-12 grid grid-cols-1 gap-10 lg:grid-cols-2">
        <ThroughputChart data={throughput} />

        <PaneFrame index="05" label="Pressure heatmap" meta="7d × 24h">
          <div className="p-6">
            <div className="grid gap-1" style={{ gridTemplateColumns: `repeat(${heatmapCols}, minmax(0, 1fr))` }}>
              {Array.from({ length: heatmapRows * heatmapCols }).map((_, i) => {
                const v = Math.random();
                return (
                  <div
                    key={i}
                    className={`h-3 ${heatmapCellTone(v)} transition-colors`}
                    title={`${v.toFixed(2)}`}
                  />
                );
              })}
            </div>
            <div className="mt-4 flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
              · low
              <div className="flex gap-1">
                {heatmapScale.map((c) => (
                  <span key={c} className={`h-2 w-4 ${c}`} />
                ))}
              </div>
              high
            </div>
          </div>
        </PaneFrame>
      </div>
    </PageShell>
  );
}
