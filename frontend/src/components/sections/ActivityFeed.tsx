import { GitCommit, Rocket, RefreshCw, FolderKanban, Bot } from "lucide-react";
import type { ActivityItem } from "@/services/analytics";
import { PaneFrame } from "@/components/primitives/PaneFrame";

const iconMap = {
  commit: GitCommit,
  deploy: Rocket,
  update: RefreshCw,
  project: FolderKanban,
  agent: Bot,
} as const;

type ActivityFeedProps = {
  items: ActivityItem[];
  loading?: boolean;
};

export function ActivityFeed({ items, loading }: ActivityFeedProps) {
  return (
    <PaneFrame index="03" label="Activity feed" meta="last 24h">
      <ul className="divide-y divide-hairline">
        {loading && (
          <li className="px-6 py-5 font-mono text-[11px] uppercase tracking-[0.22em] text-fg-subtle">
            · syncing…
          </li>
        )}
        {!loading && items.length === 0 && (
          <li className="px-6 py-5 font-mono text-[11px] uppercase tracking-[0.22em] text-fg-subtle">
            · no events
          </li>
        )}
        {items.map((it) => {
          const Icon = iconMap[it.type];
          return (
            <li
              key={it.id}
              className="grid grid-cols-[auto_minmax(0,1fr)_auto] items-start gap-4 px-6 py-4 transition-colors hover:bg-surface-raised/40"
            >
              <span className="grid h-8 w-8 place-items-center border border-hairline bg-surface-sunken text-fg-muted">
                <Icon className="h-3.5 w-3.5" />
              </span>
              <div className="min-w-0">
                <p className="font-mono text-[12px] uppercase tracking-[0.18em] text-fg truncate">
                  {it.title}
                </p>
                <p className="mt-1 font-mono text-[11px] tracking-[0.05em] text-fg-subtle truncate">
                  {it.detail}
                </p>
              </div>
              <span className="font-mono text-[10px] uppercase tracking-[0.32em] text-fg-faint whitespace-nowrap">
                {it.timestamp}
              </span>
            </li>
          );
        })}
      </ul>
    </PaneFrame>
  );
}
