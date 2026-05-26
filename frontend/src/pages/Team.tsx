import { PageShell } from "@/components/sections/PageShell";
import { PaneFrame } from "@/components/primitives/PaneFrame";

type Member = {
  initials: string;
  name: string;
  role: string;
  status: "online" | "focus" | "away";
  zone: string;
};

const TEAM: Member[] = [
  { initials: "JV", name: "jotavtech", role: "Founder · Fullstack", status: "online", zone: "BRT" },
  { initials: "RS", name: "rsantos", role: "Backend lead", status: "focus", zone: "BRT" },
  { initials: "AM", name: "amartins", role: "Design systems", status: "online", zone: "LIS" },
  { initials: "KT", name: "ktanaka", role: "Infra & DevOps", status: "away", zone: "JST" },
  { initials: "EN", name: "enasc", role: "QA · Reliability", status: "online", zone: "BRT" },
  { initials: "MO", name: "mokafor", role: "Product", status: "focus", zone: "GMT" },
];

const STATUS_TONE = {
  online: "text-emerald-300 bg-emerald-400",
  focus: "text-accent bg-accent",
  away: "text-fg-subtle bg-fg-subtle",
} as const;

export default function TeamPage() {
  return (
    <PageShell
      section="section · team"
      marker="members"
      title={<>Six operators, one cadence.</>}
      description="People orbiting this node, with role, status and timezone. Status reflects real availability — not vanity presence."
    >
      <div className="grid grid-cols-1 gap-px border border-hairline bg-hairline md:grid-cols-2 lg:grid-cols-3">
        {TEAM.map((m, i) => (
          <div key={m.name} className="relative bg-surface-sunken/70 p-6">
            <span className="absolute right-4 top-4 font-mono text-[10px] tracking-[0.32em] text-fg-faint">
              {String(i + 1).padStart(2, "0")}
            </span>
            <div className="flex items-center gap-4">
              <div className="grid h-12 w-12 place-items-center border border-hairline bg-surface-raised font-mono text-[12px] uppercase tracking-[0.22em] text-fg">
                {m.initials}
              </div>
              <div>
                <p className="font-mono text-[13px] uppercase tracking-[0.18em] text-fg">
                  {m.name}
                </p>
                <p className="mt-0.5 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                  {m.role}
                </p>
              </div>
            </div>
            <div className="mt-5 flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.32em]">
              <span className="inline-flex items-center gap-2 text-fg-muted">
                <span className={`h-1.5 w-1.5 ${STATUS_TONE[m.status].split(" ")[1]}`} />
                <span className={STATUS_TONE[m.status].split(" ")[0]}>{m.status}</span>
              </span>
              <span className="text-fg-subtle">
                tz <span className="text-fg-muted">{m.zone}</span>
              </span>
            </div>
          </div>
        ))}
      </div>

      <div className="mt-12">
        <PaneFrame index="07" label="Roles & permissions" meta="snapshot">
          <table className="w-full">
            <thead>
              <tr className="border-b border-hairline font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                <th className="px-6 py-3 text-left">role</th>
                <th className="px-6 py-3 text-left">scope</th>
                <th className="px-6 py-3 text-left">can deploy</th>
                <th className="px-6 py-3 text-left">can manage projects</th>
              </tr>
            </thead>
            <tbody className="font-mono text-[11px] tracking-[0.05em] text-fg-muted">
              <tr className="border-b border-hairline">
                <td className="px-6 py-4">founder</td>
                <td className="px-6 py-4">full</td>
                <td className="px-6 py-4 text-emerald-300">yes</td>
                <td className="px-6 py-4 text-emerald-300">yes</td>
              </tr>
              <tr className="border-b border-hairline">
                <td className="px-6 py-4">engineer</td>
                <td className="px-6 py-4">code + infra</td>
                <td className="px-6 py-4 text-emerald-300">yes</td>
                <td className="px-6 py-4 text-emerald-300">yes</td>
              </tr>
              <tr className="border-b border-hairline">
                <td className="px-6 py-4">designer</td>
                <td className="px-6 py-4">frontend</td>
                <td className="px-6 py-4 text-amber-300">staging</td>
                <td className="px-6 py-4 text-amber-300">review</td>
              </tr>
              <tr>
                <td className="px-6 py-4">guest</td>
                <td className="px-6 py-4">read</td>
                <td className="px-6 py-4 text-fg-subtle">no</td>
                <td className="px-6 py-4 text-fg-subtle">no</td>
              </tr>
            </tbody>
          </table>
        </PaneFrame>
      </div>
    </PageShell>
  );
}
