import { motion } from "framer-motion";
import { PageShell } from "@/components/sections/PageShell";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { CheckCircle2, AlertCircle, Loader2 } from "lucide-react";
import { stagger, fadeUp } from "@/lib/motion";

type WorkflowRun = {
  id: string;
  name: string;
  branch: string;
  status: "passed" | "failed" | "running";
  duration: string;
  timestamp: string;
};

const RUNS: WorkflowRun[] = [
  { id: "1", name: "ci · frontend", branch: "main", status: "passed", duration: "1m 42s", timestamp: "08m" },
  { id: "2", name: "ci · backend", branch: "main", status: "passed", duration: "2m 18s", timestamp: "08m" },
  { id: "3", name: "sonarcloud", branch: "main", status: "running", duration: "—", timestamp: "07m" },
  { id: "4", name: "ci · frontend", branch: "feature/agents", status: "passed", duration: "1m 50s", timestamp: "1h" },
  { id: "5", name: "deploy · production", branch: "main", status: "passed", duration: "3m 04s", timestamp: "2h" },
  { id: "6", name: "ci · backend", branch: "feature/agents", status: "failed", duration: "1m 02s", timestamp: "3h" },
];

const STATUS = {
  passed: { tone: "text-emerald-300", icon: CheckCircle2 },
  failed: { tone: "text-accent", icon: AlertCircle },
  running: { tone: "text-amber-300", icon: Loader2 },
} as const;

export default function WorkflowsPage() {
  return (
    <PageShell
      section="section · workflows"
      marker="ci · cd"
      title={<>Every pipeline, in one orbit.</>}
      description="GitHub Actions runs, SonarCloud scans and production deploys aggregated into a single operational view."
    >
      <PaneFrame index="08" label="Recent runs" meta="last 24h">
        <motion.ul
          variants={stagger(0.05)}
          initial="hidden"
          animate="show"
          className="divide-y divide-hairline"
        >
          {RUNS.map((run) => {
            const meta = STATUS[run.status];
            const Icon = meta.icon;
            return (
              <motion.li
                key={run.id}
                variants={fadeUp}
                className="group grid grid-cols-[40px_minmax(0,1fr)_100px_88px_56px] items-center gap-5 px-5 py-4 transition-colors duration-300 hover:bg-surface-raised/40 md:px-6"
              >
                <span className={`grid h-8 w-8 place-items-center border border-hairline bg-surface-sunken ${meta.tone} transition-colors duration-300 group-hover:border-hairline-strong`}>
                  <Icon className={`h-3.5 w-3.5 ${run.status === "running" ? "animate-spin" : ""}`} />
                </span>
                <div className="min-w-0">
                  <p className="font-mono text-[12px] uppercase tracking-[0.18em] text-fg truncate">
                    {run.name}
                  </p>
                  <p className="mt-1 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                    branch <span className="text-fg-muted">{run.branch}</span>
                  </p>
                </div>
                <span className={`font-mono text-[10px] uppercase tracking-[0.32em] ${meta.tone}`}>
                  {run.status}
                </span>
                <span className="font-mono text-[11px] tabular-nums text-fg-muted">{run.duration}</span>
                <span className="text-right font-mono text-[10px] uppercase tracking-[0.32em] text-fg-faint">
                  {run.timestamp}
                </span>
              </motion.li>
            );
          })}
        </motion.ul>
      </PaneFrame>
    </PageShell>
  );
}
