import { useQuery } from "@tanstack/react-query";
import { PageShell } from "@/components/sections/PageShell";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { StatGrid, type Stat } from "@/components/sections/StatGrid";
import { getDatabaseOverview } from "@/services/analytics";

export default function DatabasePage() {
  const database = useQuery({
    queryKey: ["analytics", "database"],
    queryFn: getDatabaseOverview,
  });
  const tables = database.data?.tables ?? [];
  const totalRows = tables.reduce((sum, table) => sum + table.rows, 0);

  const stats: Stat[] = [
    { index: "01", label: "Provider", value: database.data?.provider ?? (database.isLoading ? "…" : "—"), caption: "The official runtime database for Dashmeboard." },
    { index: "02", label: "ORM", value: database.data?.orm ?? (database.isLoading ? "…" : "—"), caption: "Schema and migrations are managed with Prisma." },
    { index: "03", label: "Tables", value: database.isLoading ? "…" : String(tables.length), caption: "Committed schema objects in the public schema." },
    { index: "04", label: "Rows", value: database.isLoading ? "…" : String(totalRows), caption: "Live row count returned by the backend." },
  ];

  return (
    <PageShell
      section="section · database"
      marker="postgres / prisma"
      title={<>Schema, surfaced from the API.</>}
      description="A real-time database snapshot backed by PostgreSQL and Prisma migrations."
    >
      {database.isError && (
        <div className="mb-6 border border-accent/50 bg-accent/5 px-5 py-4 font-mono text-[11px] uppercase tracking-[0.18em] text-accent">
          · unable to load database status
        </div>
      )}
      <StatGrid stats={stats} />

      <div className="mt-12">
        <PaneFrame index="05" label="Tables" meta={database.data?.schema ?? "schema"}>
          <div className="overflow-x-auto">
            <table className="w-full min-w-[680px]">
              <thead>
                <tr className="border-b border-hairline font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                  <th className="px-6 py-3 text-left">name</th>
                  <th className="px-6 py-3 text-left">columns</th>
                  <th className="px-6 py-3 text-left">rows</th>
                  <th className="px-6 py-3 text-left">indexed</th>
                </tr>
              </thead>
              <tbody className="font-mono text-[12px] tracking-[0.05em] text-fg">
                {database.isLoading && (
                  <tr>
                    <td className="px-6 py-6 text-fg-subtle" colSpan={4}>· loading schema…</td>
                  </tr>
                )}
                {!database.isLoading && tables.length === 0 && (
                  <tr>
                    <td className="px-6 py-6 text-fg-subtle" colSpan={4}>· no tables reported</td>
                  </tr>
                )}
                {tables.map((table, i) => (
                  <tr key={table.name} className={i < tables.length - 1 ? "border-b border-hairline" : ""}>
                    <td className="px-6 py-4 uppercase tracking-[0.18em] text-fg">{table.name}</td>
                    <td className="px-6 py-4 tabular-nums text-fg-muted">{table.columns}</td>
                    <td className="px-6 py-4 tabular-nums text-fg-muted">{table.rows.toLocaleString()}</td>
                    <td className="px-6 py-4">
                      <span className={table.indexed ? "font-mono text-[10px] uppercase tracking-[0.32em] text-emerald-300" : "font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle"}>
                        {table.indexed ? "yes" : "no"}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </PaneFrame>
      </div>
    </PageShell>
  );
}
