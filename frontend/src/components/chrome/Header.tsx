import { useEffect, useState } from "react";
import { Volume2, Film } from "lucide-react";
import { formatClock, nodeId } from "@/lib/time";
import { ThemeToggle } from "./ThemeToggle";

export function Header() {
  const [now, setNow] = useState(() => new Date());

  useEffect(() => {
    const id = setInterval(() => setNow(new Date()), 1000);
    return () => clearInterval(id);
  }, []);

  return (
    <header className="sticky top-0 z-30 flex min-h-16 items-center justify-between gap-3 border-b border-hairline surface-overlay px-4 py-3 md:h-16 md:px-6 md:py-0">
      <div className="flex min-w-0 items-center gap-3 md:gap-6">
        <span className="truncate font-mono text-[10px] uppercase tracking-[0.22em] text-fg-muted md:text-[11px] md:tracking-[0.32em]">
          <span className="text-fg-faint">·</span> jotavtech
          <span className="mx-2 text-fg-faint">/</span>
          <span className="text-fg-subtle">{nodeId(now)}</span>
        </span>

        <span className="hidden md:inline-flex items-center gap-2 border border-hairline px-2 py-1 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-muted">
          <span className="h-1.5 w-1.5 bg-emerald-400" />
          sys · online
        </span>
      </div>

      <div className="flex shrink-0 items-center gap-2 md:gap-3">
        <span className="hidden md:inline-flex items-center gap-2 border border-hairline px-2 py-1 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
          <Volume2 className="h-3 w-3" />
          snd
        </span>
        <span className="hidden md:inline-flex items-center gap-2 border border-hairline px-2 py-1 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
          <Film className="h-3 w-3" />
          cinema
        </span>
        <ThemeToggle className="hidden md:inline-flex" />
        <span className="font-mono text-[10px] uppercase tracking-[0.22em] text-fg-muted md:text-[11px] md:tracking-[0.32em]">
          <span className="text-fg-faint">brt</span>
          <span className="mx-2 text-fg-faint">/</span>
          <span className="tabular-nums">{formatClock(now)}</span>
        </span>
      </div>
    </header>
  );
}
