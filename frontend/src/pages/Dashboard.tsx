import { useQuery } from "@tanstack/react-query";
import { motion } from "framer-motion";
import { ArrowUpRight, Database, Server, Wifi } from "lucide-react";
import { ChromeText } from "@/components/primitives/ChromeText";
import { TerminalLabel } from "@/components/primitives/TerminalLabel";
import { StatGrid, type Stat } from "@/components/sections/StatGrid";
import { ActivityFeed } from "@/components/sections/ActivityFeed";
import { ThroughputChart } from "@/components/sections/ThroughputChart";
import { Magnetic } from "@/components/primitives/Magnetic";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { greeting } from "@/lib/time";
import { ease } from "@/lib/motion";
import { getOverview, getActivity, getThroughput } from "@/services/analytics";
import { getHealth } from "@/services/health";

export default function DashboardPage() {
  const overview = useQuery({
    queryKey: ["analytics", "overview"],
    queryFn: getOverview,
  });
  const activity = useQuery({
    queryKey: ["analytics", "activity"],
    queryFn: () => getActivity(8),
  });
  const throughput = useQuery({
    queryKey: ["analytics", "throughput"],
    queryFn: getThroughput,
  });
  const health = useQuery({
    queryKey: ["health"],
    queryFn: getHealth,
    refetchInterval: 30_000,
  });

  const o = overview.data;
  const stats: Stat[] = [
    {
      index: "01",
      label: "Total projects",
      value: formatMetric(o?.totalProjects, overview.isLoading),
      caption: "Database-backed records managed by the CRUD.",
    },
    {
      index: "02",
      label: "Active projects",
      value: formatMetric(o?.activeProjects, overview.isLoading),
      caption: "Projects currently in execution.",
    },
    {
      index: "03",
      label: "Tasks completed",
      value: formatMetric(o?.completedTasks, overview.isLoading),
      caption: "Completed task records across all projects.",
    },
    {
      index: "04",
      label: "Completion rate",
      value: formatMetric(o?.weeklyPerformance, overview.isLoading),
      suffix: o || overview.isLoading ? "%" : undefined,
      caption: "Calculated from completed tasks over total tasks.",
    },
  ];

  return (
    <div className="relative mx-auto w-full max-w-[1440px] px-6 py-10 md:px-10 md:py-14 lg:px-14 lg:py-16">
      <div className="engraved-grid pointer-events-none absolute inset-0 -z-0 opacity-60" />

      <motion.section
        initial={{ opacity: 0, y: -16 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.7, ease: ease.outExpo }}
        className="relative flex flex-col gap-6"
      >
        <div className="flex items-center justify-between">
          <TerminalLabel>· section 01 / production overview</TerminalLabel>
          <TerminalLabel variant={health.data?.status === "ok" ? "rust" : "default"}>
            {health.isError ? "degraded" : health.data?.status ?? "checking"}
          </TerminalLabel>
        </div>

        <h1 className="font-display tracking-tightest text-display-lg">
          <ChromeText variant="bright">{greeting()}.</ChromeText>{" "}
          <ChromeText variant="muted">Dashmeboard is on stage.</ChromeText>
        </h1>

        <p className="max-w-2xl text-fg-muted leading-relaxed">
          Project operations, database health and presentation-critical signals
          in one dark, cinematic control surface.
        </p>

        <div className="flex flex-wrap gap-3 pt-2">
          <Cta href="/projects" label="[ manage projects ]" />
          <Cta href="/database" label="[ inspect database ]" muted />
        </div>
      </motion.section>

      <section className="relative mt-14 md:mt-16">
        <div className="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
          <TerminalLabel>· section 02 / production signals</TerminalLabel>
          {overview.isError && (
            <span className="font-mono text-[10px] uppercase tracking-[0.24em] text-accent">
              analytics API unavailable
            </span>
          )}
        </div>
        <StatGrid stats={stats} />
      </section>

      <section className="relative mt-14 grid grid-cols-1 gap-8 md:mt-16 md:gap-10 lg:grid-cols-5">
        <div className="lg:col-span-3">
          <ActivityFeed
            items={activity.data ?? []}
            loading={activity.isLoading}
            error={activity.isError}
          />
        </div>
        <div className="lg:col-span-2">
          <HealthPanel loading={health.isLoading} error={health.isError} data={health.data} />
        </div>
      </section>

      <section className="relative mt-8 md:mt-10">
        <ThroughputChart data={throughput.data ?? []} />
      </section>
    </div>
  );
}

function Cta({ href, label, muted = false }: { href: string; label: string; muted?: boolean }) {
  return (
    <Magnetic>
      <a
        href={href}
        className={
          muted
            ? "group inline-flex items-center gap-3 border border-hairline px-5 py-3 font-mono text-[11px] uppercase tracking-[0.32em] text-fg-muted transition-all duration-300 hover:border-hairline-strong hover:text-fg hover:shadow-glow-soft"
            : "group inline-flex items-center gap-3 border border-hairline-strong bg-surface-sunken px-5 py-3 font-mono text-[11px] uppercase tracking-[0.32em] text-fg transition-all duration-300 hover:bg-accent hover:text-surface hover:border-accent hover:shadow-glow-soft"
        }
      >
        {label}
        <ArrowUpRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
      </a>
    </Magnetic>
  );
}

function HealthPanel({
  loading,
  error,
  data,
}: {
  loading: boolean;
  error: boolean;
  data?: Awaited<ReturnType<typeof getHealth>>;
}) {
  const rows = [
    { icon: Wifi, label: "frontend", value: "online", tone: "text-emerald-300" },
    {
      icon: Server,
      label: "backend",
      value: loading ? "checking" : error ? "offline" : data?.services.api ?? "unknown",
      tone: error ? "text-accent" : "text-emerald-300",
    },
    {
      icon: Database,
      label: "database",
      value: loading ? "checking" : error ? "unknown" : data?.services.database ?? "unknown",
      tone: data?.services.database === "down" || error ? "text-accent" : "text-emerald-300",
    },
  ];

  return (
    <PaneFrame index="05" label="System health" meta="live">
      <div className="space-y-4 p-6">
        {rows.map((row) => (
          <div key={row.label} className="flex items-center justify-between border-b border-hairline pb-3 last:border-b-0">
            <span className="inline-flex items-center gap-3 font-mono text-[10px] uppercase tracking-[0.28em] text-fg-subtle">
              <row.icon className="h-3.5 w-3.5" />
              {row.label}
            </span>
            <span className={`font-mono text-[11px] uppercase tracking-[0.24em] ${row.tone}`}>
              {row.value}
            </span>
          </div>
        ))}
        <div className="grid grid-cols-2 gap-3 pt-2">
          <MiniMetric label="latency" value={data ? `${data.latencyMs}ms` : "—"} />
          <MiniMetric label="env" value={data?.environment ?? "—"} />
        </div>
      </div>
    </PaneFrame>
  );
}

function MiniMetric({ label, value }: { label: string; value: string }) {
  return (
    <div className="border border-hairline bg-surface-raised/40 p-3">
      <p className="font-mono text-[9px] uppercase tracking-[0.28em] text-fg-faint">· {label}</p>
      <p className="mt-2 font-mono text-[12px] uppercase tracking-[0.16em] text-fg-muted">{value}</p>
    </div>
  );
}

function formatMetric(value: number | undefined, loading: boolean) {
  if (loading) return "…";
  if (typeof value === "number") return String(value);
  return "—";
}
