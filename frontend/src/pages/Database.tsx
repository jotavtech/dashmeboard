import { PageShell } from "@/components/sections/PageShell";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { StatGrid, type Stat } from "@/components/sections/StatGrid";

const dbStats: Stat[] = [
  { index: "01", label: "Tables", value: "4", caption: "users, projects, tasks, analytics_logs." },
  { index: "02", label: "Rows", value: "12.4", suffix: "k", caption: "Across all tables." },
  { index: "03", label: "Size", value: "284", suffix: "MB", caption: "Postgres data dir." },
  { index: "04", label: "Connections", value: "8", caption: "Active pool size." },
];

const TABLES = [
  { name: "users", columns: 8, rows: 36, indexed: true },
  { name: "projects", columns: 9, rows: 248, indexed: true },
  { name: "tasks", columns: 11, rows: 1842, indexed: true },
  { name: "analytics_logs", columns: 6, rows: 10240, indexed: false },
];

export default function DatabasePage() {
  return (
    <PageShell
      section="section · database"
      marker="postgres / prisma"
      title={<>Schema, surfaced.</>}
      description="The live shape of Postgres backing this node. Schema managed via Prisma migrations."
    >
      <StatGrid stats={dbStats} />

      <div className="mt-12">
        <PaneFrame index="05" label="Tables" meta="schema · public">
          <table className="w-full">
            <thead>
              <tr className="border-b border-hairline font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                <th className="px-6 py-3 text-left">name</th>
                <th className="px-6 py-3 text-left">columns</th>
                <th className="px-6 py-3 text-left">rows</th>
                <th className="px-6 py-3 text-left">indexed</th>
              </tr>
            </thead>
            <tbody className="font-mono text-[12px] tracking-[0.05em] text-fg">
              {TABLES.map((t, i) => (
                <tr key={t.name} className={i < TABLES.length - 1 ? "border-b border-hairline" : ""}>
                  <td className="px-6 py-4 uppercase tracking-[0.18em] text-fg">{t.name}</td>
                  <td className="px-6 py-4 tabular-nums text-fg-muted">{t.columns}</td>
                  <td className="px-6 py-4 tabular-nums text-fg-muted">{t.rows.toLocaleString()}</td>
                  <td className="px-6 py-4">
                    <span
                      className={`font-mono text-[10px] uppercase tracking-[0.32em] ${
                        t.indexed ? "text-emerald-300" : "text-fg-subtle"
                      }`}
                    >
                      {t.indexed ? "yes" : "no"}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </PaneFrame>
      </div>
    </PageShell>
  );
}
