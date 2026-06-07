import { useMemo, useState } from "react";
import type { ComponentPropsWithoutRef, ReactNode } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { motion } from "framer-motion";
import ReactMarkdown from "react-markdown";
import { Sparkles, Loader2 } from "lucide-react";
import { PageShell } from "@/components/sections/PageShell";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { listAiInsights, generateDashboardInsight, type AiInsight } from "@/services/ai";
import type { ApiError } from "@/services/api";
import { cn } from "@/lib/cn";

const TYPE_LABEL: Record<string, string> = {
  dashboard_insight: "dashboard insight",
  project_plan: "project plan",
};

function formatStamp(iso: string) {
  return new Date(iso).toLocaleString(undefined, {
    month: "short",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}

export default function AiCenterPage() {
  const queryClient = useQueryClient();
  const [activeId, setActiveId] = useState<string | null>(null);

  const insights = useQuery({
    queryKey: ["ai", "insights"],
    queryFn: listAiInsights,
  });

  const generate = useMutation({
    mutationFn: generateDashboardInsight,
    onSuccess: (created) => {
      setActiveId(created.id);
      queryClient.invalidateQueries({ queryKey: ["ai", "insights"] });
    },
  });

  const list = useMemo(() => insights.data ?? [], [insights.data]);
  const active = useMemo<AiInsight | undefined>(() => {
    if (activeId) return list.find((item) => item.id === activeId);
    return list[0];
  }, [activeId, list]);

  const mutationError = generate.error as ApiError | null;

  return (
    <PageShell
      section="section · ai"
      marker="copilot / openai"
      title={<>AI Command Center</>}
      description="Strategic intelligence layer for project operations — generated from live data, persisted for review."
      actions={
        <GenerateButton
          pending={generate.isPending}
          onClick={() => generate.mutate()}
        />
      }
    >
      {mutationError && (
        <div className="mb-6 border border-accent/50 bg-accent/5 px-5 py-4 font-mono text-[11px] uppercase tracking-[0.18em] text-accent">
          · {mutationError.status === 503
            ? "ai is not configured — set openai_api_key on the backend"
            : mutationError.message}
        </div>
      )}

      <div className="grid grid-cols-1 gap-10 lg:grid-cols-[1fr_320px]">
        {/* Primary insight */}
        <PaneFrame
          index="01"
          label="Latest insight"
          meta={active?.model ?? "—"}
          inner="p-6 md:p-8 min-h-[420px]"
        >
          {generate.isPending ? (
            <LoadingState />
          ) : active ? (
            <article key={active.id}>
              <div className="mb-6 flex items-center justify-between border-b border-hairline pb-4">
                <span className="font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                  {TYPE_LABEL[active.type] ?? active.type}
                </span>
                <span className="font-mono text-[10px] uppercase tracking-[0.32em] text-fg-faint">
                  {formatStamp(active.createdAt)}
                </span>
              </div>
              <Markdown content={active.output} />
            </article>
          ) : insights.isLoading ? (
            <LoadingState label="· loading history" />
          ) : (
            <EmptyState onGenerate={() => generate.mutate()} pending={generate.isPending} />
          )}
        </PaneFrame>

        {/* History */}
        <PaneFrame index="02" label="History" meta={String(list.length)} inner="p-3" hoverGlow={false}>
          {insights.isError ? (
            <p className="px-3 py-6 font-mono text-[11px] uppercase tracking-[0.18em] text-accent">
              · unable to load history
            </p>
          ) : list.length === 0 ? (
            <p className="px-3 py-6 font-mono text-[11px] uppercase tracking-[0.22em] text-fg-subtle">
              · no insights yet
            </p>
          ) : (
            <ul className="space-y-0.5">
              {list.map((item) => {
                const isActive = item.id === active?.id;
                return (
                  <li key={item.id}>
                    <button
                      type="button"
                      onClick={() => setActiveId(item.id)}
                      className={cn(
                        "group flex w-full flex-col gap-1 px-3 py-3 text-left transition-colors duration-200",
                        isActive
                          ? "bg-surface-sunken text-fg"
                          : "text-fg-subtle hover:bg-surface-sunken/60 hover:text-fg-muted",
                      )}
                    >
                      <span className="flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.28em]">
                        <span className={isActive ? "text-accent" : "text-fg-faint group-hover:text-fg-subtle"}>
                          {TYPE_LABEL[item.type] ?? item.type}
                        </span>
                        <span className="text-fg-faint">{item.model}</span>
                      </span>
                      <span className="font-mono text-[10px] tracking-[0.18em] text-fg-faint">
                        {formatStamp(item.createdAt)}
                      </span>
                    </button>
                  </li>
                );
              })}
            </ul>
          )}
        </PaneFrame>
      </div>
    </PageShell>
  );
}

function GenerateButton({ pending, onClick }: { pending: boolean; onClick: () => void }) {
  return (
    <button
      type="button"
      onClick={onClick}
      disabled={pending}
      className={cn(
        "group inline-flex items-center gap-2.5 border border-hairline-strong bg-surface-sunken/60 px-4 py-2.5",
        "font-mono text-[11px] uppercase tracking-[0.22em] text-fg-muted",
        "transition-[color,border-color,box-shadow] duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]",
        "hover:border-accent/60 hover:text-fg hover:shadow-glow-soft",
        "disabled:cursor-not-allowed disabled:opacity-60",
      )}
    >
      {pending ? (
        <Loader2 className="h-3.5 w-3.5 animate-spin text-accent" />
      ) : (
        <Sparkles className="h-3.5 w-3.5 text-accent transition-transform duration-300 group-hover:scale-110" />
      )}
      {pending ? "generating" : "generate insight"}
    </button>
  );
}

function LoadingState({ label = "· synthesizing insight" }: { label?: string }) {
  return (
    <div className="flex flex-col gap-4">
      <div className="flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.28em] text-fg-subtle">
        <Loader2 className="h-3.5 w-3.5 animate-spin text-accent" />
        {label}
      </div>
      <div className="space-y-3">
        {[0, 1, 2, 3, 4].map((i) => (
          <motion.div
            key={i}
            className="h-3 bg-surface-sunken"
            style={{ width: `${90 - i * 11}%` }}
            animate={{ opacity: [0.35, 0.7, 0.35] }}
            transition={{ duration: 1.4, repeat: Infinity, delay: i * 0.12, ease: "easeInOut" }}
          />
        ))}
      </div>
    </div>
  );
}

function EmptyState({ onGenerate, pending }: { onGenerate: () => void; pending: boolean }) {
  return (
    <div className="flex min-h-[340px] flex-col items-center justify-center gap-5 text-center">
      <span className="grid h-12 w-12 place-items-center border border-hairline text-accent">
        <Sparkles className="h-5 w-5" />
      </span>
      <div className="space-y-2">
        <p className="font-mono text-[12px] uppercase tracking-[0.28em] text-fg-muted">
          no insights yet
        </p>
        <p className="max-w-sm text-[13px] leading-relaxed text-fg-subtle">
          Generate a strategic read of your projects, tasks and momentum — analyzed from live data and saved here.
        </p>
      </div>
      <GenerateButton pending={pending} onClick={onGenerate} />
    </div>
  );
}

const mdComponents = {
  h1: (p: ComponentPropsWithoutRef<"h1">) => (
    <h2 className="mb-3 mt-6 font-display text-lg text-fg tracking-tight first:mt-0" {...p} />
  ),
  h2: (p: ComponentPropsWithoutRef<"h2">) => (
    <h3 className="mb-3 mt-6 font-mono text-[12px] uppercase tracking-[0.22em] text-fg first:mt-0" {...p} />
  ),
  h3: (p: ComponentPropsWithoutRef<"h3">) => (
    <h4 className="mb-2 mt-5 font-mono text-[11px] uppercase tracking-[0.22em] text-fg-muted first:mt-0" {...p} />
  ),
  p: (p: ComponentPropsWithoutRef<"p">) => (
    <p className="mb-4 text-[14px] leading-relaxed text-fg-muted" {...p} />
  ),
  ul: (p: ComponentPropsWithoutRef<"ul">) => (
    <ul className="mb-4 space-y-2 pl-1" {...p} />
  ),
  ol: (p: ComponentPropsWithoutRef<"ol">) => (
    <ol className="mb-4 list-decimal space-y-2 pl-5 marker:font-mono marker:text-[11px] marker:text-accent" {...p} />
  ),
  li: ({ children, ...rest }: ComponentPropsWithoutRef<"li">) => (
    <li className="text-[14px] leading-relaxed text-fg-muted" {...rest}>
      {children}
    </li>
  ),
  strong: (p: ComponentPropsWithoutRef<"strong">) => (
    <strong className="font-semibold text-fg" {...p} />
  ),
  code: (p: ComponentPropsWithoutRef<"code">) => (
    <code className="bg-surface-sunken px-1.5 py-0.5 font-mono text-[12px] text-accent" {...p} />
  ),
  a: (p: ComponentPropsWithoutRef<"a">) => (
    <a className="text-accent underline underline-offset-2" {...p} />
  ),
};

function Markdown({ content }: { content: string }): ReactNode {
  return (
    <div className="ai-markdown">
      <ReactMarkdown components={mdComponents}>{content}</ReactMarkdown>
    </div>
  );
}
