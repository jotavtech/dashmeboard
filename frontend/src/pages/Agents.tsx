import { motion } from "framer-motion";
import { Bot, Shield } from "lucide-react";
import { PageShell } from "@/components/sections/PageShell";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { StatBar } from "@/components/primitives/StatBar";
import { fadeUp, stagger } from "@/lib/motion";

type AgentCard = {
  bracket: string;
  name: string;
  description: string;
  capabilities: string[];
  prompt: string;
  status: "idle" | "armed" | "running";
  proficiency: number;
};

const AGENTS: AgentCard[] = [
  {
    bracket: "AR",
    name: "Architect",
    description: "Analyzes structure, coupling and scalability. Suggests refactors.",
    capabilities: ["structure", "coupling", "scalability", "refactor"],
    prompt: "/prompts/architect.md",
    status: "armed",
    proficiency: 5,
  },
  {
    bracket: "FE",
    name: "Frontend",
    description: "React + Tailwind specialist. Builds components, improves UX, motion.",
    capabilities: ["react", "tailwind", "motion", "a11y"],
    prompt: "/prompts/frontend.md",
    status: "armed",
    proficiency: 5,
  },
  {
    bracket: "BE",
    name: "Backend",
    description: "Express + Prisma. Routes, controllers, services, schema, typing.",
    capabilities: ["express", "prisma", "validation", "typing"],
    prompt: "/prompts/backend.md",
    status: "armed",
    proficiency: 5,
  },
  {
    bracket: "DV",
    name: "DevOps",
    description: "Docker, Compose, CI/CD, SonarCloud, deployments.",
    capabilities: ["docker", "ci-cd", "sonarcloud", "envs"],
    prompt: "/prompts/devops.md",
    status: "armed",
    proficiency: 4,
  },
  {
    bracket: "DS",
    name: "Design",
    description: "Keeps the editorial cyber identity coherent. Reviews spacing, hierarchy, motion.",
    capabilities: ["tokens", "spacing", "hierarchy", "motion"],
    prompt: "/prompts/design.md",
    status: "armed",
    proficiency: 5,
  },
  {
    bracket: "DO",
    name: "Docs",
    description: "Generates README, changelogs, architecture and technical docs.",
    capabilities: ["readme", "changelog", "architecture", "technical"],
    prompt: "/prompts/docs.md",
    status: "idle",
    proficiency: 4,
  },
  {
    bracket: "RV",
    name: "Reviewer",
    description: "General code review: smells, performance, clean code, SOLID.",
    capabilities: ["review", "perf", "clean-code", "solid"],
    prompt: "/prompts/reviewer.md",
    status: "running",
    proficiency: 5,
  },
];

const STATUS_COLOR = {
  idle: "bg-fg-subtle",
  armed: "bg-emerald-400",
  running: "bg-accent",
} as const;

export default function AgentsPage() {
  return (
    <PageShell
      section="section · agents"
      marker="local subsystem"
      title={<>Local-only engineering augmentation.</>}
      description="Seven specialized agents live inside this repository. They are gitignored by design — credentials, memory and runs stay on your machine. Invoke them through the orchestrator CLI."
      actions={
        <span className="inline-flex items-center gap-2 border border-hairline px-3 py-2 font-mono text-[10px] uppercase tracking-[0.32em] text-emerald-300">
          <Shield className="h-3 w-3" />
          local · sandboxed
        </span>
      }
    >
      <div className="mb-10 border border-hairline bg-surface-sunken/60 p-6">
        <div className="mb-4 flex items-center gap-3 font-mono text-[11px] uppercase tracking-[0.32em] text-fg-muted">
          <span className="text-fg-faint">·</span> invoke
        </div>
        <pre className="overflow-x-auto bg-surface/70 p-5 font-mono text-[12px] leading-relaxed text-fg-muted">
{`# interactive orchestrator
npm run agents:start

# direct agent invocation
npm run agent:frontend
npm run agent:backend
npm run agent:reviewer`}
        </pre>
      </div>

      <motion.div
        variants={stagger(0.06)}
        initial="hidden"
        animate="show"
        className="grid grid-cols-1 gap-6 md:grid-cols-2"
      >
        {AGENTS.map((a, i) => (
          <motion.div key={a.name} variants={fadeUp}>
            <PaneFrame
              index={String(i + 1).padStart(2, "0")}
              label={`agent · ${a.name.toLowerCase()}`}
              meta={a.prompt}
            >
              <div className="p-6">
                <div className="flex items-start justify-between gap-4">
                  <div className="flex items-center gap-4">
                    <div className="grid h-12 w-12 place-items-center border border-hairline bg-surface-raised">
                      <Bot className="h-5 w-5 text-fg" />
                    </div>
                    <div>
                      <p className="font-mono text-[10px] uppercase tracking-[0.32em] text-fg-faint">
                        [{a.bracket}]
                      </p>
                      <p className="mt-1 font-display text-[1.4rem] text-fg">
                        {a.name}
                      </p>
                    </div>
                  </div>
                  <span className="inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-muted">
                    <span className={`h-1.5 w-1.5 rounded-full ${STATUS_COLOR[a.status]}`} />
                    {a.status}
                  </span>
                </div>

                <p className="mt-4 text-[13px] leading-relaxed text-fg-muted">
                  {a.description}
                </p>

                <div className="mt-5 flex flex-wrap gap-1.5">
                  {a.capabilities.map((c) => (
                    <span
                      key={c}
                      className="border border-hairline px-2 py-1 font-mono text-[10px] uppercase tracking-[0.22em] text-fg-muted"
                    >
                      {c}
                    </span>
                  ))}
                </div>

                <div className="mt-6 flex items-center justify-between gap-4">
                  <StatBar value={a.proficiency} max={5} className="max-w-[160px]" />
                  <span className="font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                    proficiency {a.proficiency}/5
                  </span>
                </div>
              </div>
            </PaneFrame>
          </motion.div>
        ))}
      </motion.div>
    </PageShell>
  );
}
