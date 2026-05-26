import { useQuery } from "@tanstack/react-query";
import { PageShell } from "@/components/sections/PageShell";
import { StatGrid, type Stat } from "@/components/sections/StatGrid";
import { ThroughputChart } from "@/components/sections/ThroughputChart";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { getOverview, getThroughput } from "@/services/analytics";

export default function AnalyticsPage() {
  const overview = useQuery({ queryKey: ["analytics", "overview"], queryFn: getOverview });
  const throughput = useQuery({ queryKey: ["analytics", "throughput"], queryFn: getThroughput });
  const data = overview.data;

  const stats: Stat[] = [
    { index: "01", label: "Total projects", value: metric(data?.totalProjects, overview.isLoading), caption: "All project records in PostgreSQL." },
    { index: "02", label: "Completed projects", value: metric(data?.completedProjects, overview.isLoading), caption: "Projects marked as done." },
    { index: "03", label: "Total tasks", value: metric(data?.totalTasks, overview.isLoading), caption: "Task records across all projects." },
    { index: "04", label: "Completion rate", value: metric(data?.weeklyPerformance, overview.isLoading), suffix: data || overview.isLoading ? "%" : undefined, caption: "Completed tasks divided by total tasks." },
  ];

  return (
    <PageShell
      section="section · analytics"
      marker="database signals"
      title={<>Operational telemetry, grounded in data.</>}
      description="Project status, priority distribution and completed-task throughput calculated from the backend API."
    >
      {overview.isError && <ErrorState message="Unable to load analytics overview." />}
      <StatGrid stats={stats} />

      <div className="mt-12 grid grid-cols-1 gap-10 lg:grid-cols-2">
        <ThroughputChart data={throughput.data ?? []} />
        <DistributionPanel
          status={data?.projectsByStatus ?? {}}
          priority={data?.projectsByPriority ?? {}}
          loading={overview.isLoading}
        />
      </div>
    </PageShell>
  );
}

function DistributionPanel({
  status,
  priority,
  loading,
}: {
  status: Record<string, number>;
  priority: Record<string, number>;
  loading: boolean;
}) {
  const statusRows = normalize(status, ["PLANNED", "ACTIVE", "PAUSED", "DONE", "ARCHIVED"]);
  const priorityRows = normalize(priority, ["LOW", "MEDIUM", "HIGH", "CRITICAL"]);

  return (
    <PaneFrame index="05" label="Distribution" meta="projects">
      <div className="grid gap-8 p-6 md:grid-cols-2">
        <DistributionColumn title="status" rows={statusRows} loading={loading} />
        <DistributionColumn title="priority" rows={priorityRows} loading={loading} accent />
      </div>
    </PaneFrame>
  );
}

function DistributionColumn({
  title,
  rows,
  loading,
  accent = false,
}: {
  title: string;
  rows: Array<{ label: string; value: number }>;
  loading: boolean;
  accent?: boolean;
}) {
  const max = Math.max(1, ...rows.map((row) => row.value));

  return (
    <div>
      <p className="mb-4 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">· {title}</p>
      <div className="space-y-3">
        {rows.map((row) => (
          <div key={row.label}>
            <div className="mb-1 flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.22em]">
              <span className="text-fg-muted">{row.label}</span>
              <span className="tabular-nums text-fg-subtle">{loading ? "…" : row.value}</span>
            </div>
            <div className="h-2 bg-surface-raised">
              <div
                className={accent ? "h-full bg-accent" : "h-full bg-fg-muted"}
                style={{ width: `${loading ? 18 : Math.max(4, (row.value / max) * 100)}%` }}
              />
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

function ErrorState({ message }: { message: string }) {
  return (
    <div className="mb-6 border border-accent/50 bg-accent/5 px-5 py-4 font-mono text-[11px] uppercase tracking-[0.18em] text-accent">
      · {message}
    </div>
  );
}

function normalize(values: Record<string, number>, labels: string[]) {
  return labels.map((label) => ({ label, value: values[label] ?? 0 }));
}

function metric(value: number | undefined, loading: boolean) {
  if (loading) return "…";
  if (typeof value === "number") return String(value);
  return "—";
}
