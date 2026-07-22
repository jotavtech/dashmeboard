import type { LucideIcon } from "lucide-react";
import { strings } from "@/i18n/strings";

type Props = {
  icon: LucideIcon;
  title: string;
  description: string;
};

/**
 * Honest placeholder for modules that are on the roadmap but not built yet —
 * no fake data, just what the module will do (PRD: demo data only in demos).
 */
export default function ModulePreview({ icon: Icon, title, description }: Props) {
  return (
    <div className="mx-auto grid min-h-[60vh] max-w-xl place-items-center px-4 py-10 text-center">
      <div>
        <div className="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-hairline bg-surface shadow-pane">
          <Icon className="h-6 w-6 text-accent" />
        </div>
        <span className="mt-5 inline-block rounded-full border border-hairline px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-fg-subtle">
          {strings.modules.comingSoon}
        </span>
        <h1 className="mt-3 font-display text-2xl font-semibold tracking-tight text-fg">{title}</h1>
        <p className="mt-2 text-[15px] leading-relaxed text-fg-muted">{description}</p>
      </div>
    </div>
  );
}
