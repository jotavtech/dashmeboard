import { useQuery } from "@tanstack/react-query";
import { motion } from "framer-motion";
import { ArrowUpRight } from "lucide-react";
import { ChromeText } from "@/components/primitives/ChromeText";
import { TerminalLabel } from "@/components/primitives/TerminalLabel";
import { StatGrid, type Stat } from "@/components/sections/StatGrid";
import { ActivityFeed } from "@/components/sections/ActivityFeed";
import { ThroughputChart } from "@/components/sections/ThroughputChart";
import { Magnetic } from "@/components/primitives/Magnetic";
import { greeting } from "@/lib/time";
import { ease } from "@/lib/motion";
import {
  getOverview,
  getActivity,
  getThroughput,
  type ActivityItem,
  type Throughput,
} from "@/services/analytics";

const FALLBACK_OVERVIEW = {
  activeProjects: 12,
  completedTasks: 248,
  activeUsers: 36,
  weeklyPerformance: 94,
  uptimePercent: 99.94,
  pipelineRuns: 184,
};

const FALLBACK_ACTIVITY: ActivityItem[] = [
  { id: "1", type: "deploy", title: "Deploy · backend v0.4.2", detail: "dashmeboard-api / production", timestamp: "02m" },
  { id: "2", type: "commit", title: "feat(projects): add filter by priority", detail: "main · jotavtech", timestamp: "11m" },
  { id: "3", type: "agent", title: "reviewer-agent · 2 suggestions", detail: "frontend/src/components/sections/StatGrid.tsx", timestamp: "26m" },
  { id: "4", type: "project", title: "New project · NODE_27.12.05", detail: "owner: jotavtech", timestamp: "1h" },
  { id: "5", type: "update", title: "Postgres maintenance window applied", detail: "no downtime", timestamp: "3h" },
];

const FALLBACK_THROUGHPUT: Throughput[] = [
  { day: "MON", value: 24 },
  { day: "TUE", value: 31 },
  { day: "WED", value: 18 },
  { day: "THU", value: 42 },
  { day: "FRI", value: 38 },
  { day: "SAT", value: 12 },
  { day: "SUN", value: 9 },
];

export default function DashboardPage() {
  const overview = useQuery({
    queryKey: ["analytics", "overview"],
    queryFn: getOverview,
    placeholderData: FALLBACK_OVERVIEW,
  });
  const activity = useQuery({
    queryKey: ["analytics", "activity"],
    queryFn: () => getActivity(8),
    placeholderData: FALLBACK_ACTIVITY,
  });
  const throughput = useQuery({
    queryKey: ["analytics", "throughput"],
    queryFn: getThroughput,
    placeholderData: FALLBACK_THROUGHPUT,
  });

  const o = overview.data ?? FALLBACK_OVERVIEW;

  const stats: Stat[] = [
    { index: "01", label: "Active projects", value: String(o.activeProjects), caption: "Across all owners, including drafts." },
    { index: "02", label: "Tasks completed", value: String(o.completedTasks), caption: "Lifetime, rolling counter." },
    { index: "03", label: "Active users", value: String(o.activeUsers), caption: "Last 7 days, distinct sessions." },
    { index: "04", label: "Performance", value: String(o.weeklyPerformance), suffix: "%", caption: "Median p95 over the week." },
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
          <TerminalLabel variant="rust">live</TerminalLabel>
        </div>

        <h1 className="font-display tracking-tightest text-display-lg">
          <ChromeText variant="bright">{greeting()}.</ChromeText>{" "}
          <ChromeText variant="muted">Operations are nominal.</ChromeText>
        </h1>

        <p className="max-w-2xl text-fg-muted leading-relaxed">
          Real-time snapshot of pipelines, throughput and human signals across
          every project orbiting this node. No prototype, no demo — production
          surface.
        </p>

        <div className="flex flex-wrap gap-3 pt-2">
          <Magnetic>
            <a
              href="/projects"
              className="group inline-flex items-center gap-3 border border-hairline-strong px-5 py-3 font-mono text-[11px] uppercase tracking-[0.32em] text-fg transition-all duration-300 hover:bg-accent hover:text-surface hover:border-accent hover:shadow-glow-soft"
            >
              [ open projects ]
              <ArrowUpRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
            </a>
          </Magnetic>
          <Magnetic>
            <a
              href="/agents"
              className="group inline-flex items-center gap-3 border border-hairline px-5 py-3 font-mono text-[11px] uppercase tracking-[0.32em] text-fg-muted transition-all duration-300 hover:border-hairline-strong hover:text-fg hover:shadow-glow-soft"
            >
              [ inspect agents ]
              <ArrowUpRight className="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
            </a>
          </Magnetic>
        </div>
      </motion.section>

      <section className="relative mt-14 md:mt-16">
        <div className="mb-6 flex items-center justify-between">
          <TerminalLabel>· section 02 / production signals</TerminalLabel>
          <span className="font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
            uptime <span className="text-fg-muted">{o.uptimePercent}%</span>
            <span className="mx-3 text-fg-faint">/</span>
            pipelines <span className="text-fg-muted">{o.pipelineRuns}</span>
          </span>
        </div>
        <StatGrid stats={stats} />
      </section>

      <section className="relative mt-14 grid grid-cols-1 gap-8 md:mt-16 md:gap-10 lg:grid-cols-5">
        <div className="lg:col-span-3">
          <ActivityFeed items={activity.data ?? []} loading={activity.isLoading} />
        </div>
        <div className="lg:col-span-2">
          <ThroughputChart data={throughput.data ?? FALLBACK_THROUGHPUT} />
        </div>
      </section>
    </div>
  );
}
