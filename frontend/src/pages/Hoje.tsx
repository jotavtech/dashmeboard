import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { AlertTriangle, ArrowRight, CalendarClock, CheckCircle2, FolderOpen } from "lucide-react";
import { useAuth } from "@/contexts/AuthContext";
import { strings } from "@/i18n/strings";
import { api } from "@/services/api";
import { getOverview } from "@/services/analytics";
import { cn } from "@/lib/cn";

type DeadlineTask = {
  id: string;
  title: string;
  dueDate: string | null;
  project: { id: string; title: string };
};

type Deadlines = {
  overdueTasks: DeadlineTask[];
  upcomingTasks: DeadlineTask[];
};

async function getDeadlines(): Promise<Deadlines> {
  const { data } = await api.get<Deadlines>("/analytics/deadlines");
  return data;
}

function greeting(): string {
  const hour = new Date().getHours();
  if (hour < 12) return strings.today.greetingMorning;
  if (hour < 18) return strings.today.greetingAfternoon;
  return strings.today.greetingEvening;
}

function isToday(iso: string | null): boolean {
  if (!iso) return false;
  const due = new Date(iso);
  const now = new Date();
  return (
    due.getDate() === now.getDate() &&
    due.getMonth() === now.getMonth() &&
    due.getFullYear() === now.getFullYear()
  );
}

function formatDue(iso: string | null): string {
  if (!iso) return "";
  return new Intl.DateTimeFormat("pt-BR", { day: "2-digit", month: "short" }).format(new Date(iso));
}

export default function HojePage() {
  const { session } = useAuth();
  const overview = useQuery({ queryKey: ["analytics", "overview"], queryFn: getOverview });
  const deadlines = useQuery({ queryKey: ["analytics", "deadlines"], queryFn: getDeadlines });

  const overdue = deadlines.data?.overdueTasks ?? [];
  const dueToday = (deadlines.data?.upcomingTasks ?? []).filter((t) => isToday(t.dueDate));
  const firstName = session?.user.name.split(" ")[0] ?? "";

  const stats = [
    {
      label: strings.today.overdueTasks,
      value: overdue.length,
      icon: AlertTriangle,
      tone: overdue.length > 0 ? "text-accent" : "text-fg-muted",
    },
    {
      label: strings.today.dueToday,
      value: dueToday.length,
      icon: CalendarClock,
      tone: "text-fg-muted",
    },
    {
      label: strings.today.activeProjects,
      value: overview.data?.activeProjects ?? "—",
      icon: FolderOpen,
      tone: "text-fg-muted",
    },
    {
      label: strings.today.weeklyDone,
      value: overview.data?.completedTasks ?? "—",
      icon: CheckCircle2,
      tone: "text-emerald-500",
    },
  ];

  const modules = [
    { to: "/clientes", ...strings.modules.customers },
    { to: "/vendas", ...strings.modules.sales },
    { to: "/financeiro", ...strings.modules.finance },
    { to: "/agenda", ...strings.modules.calendar },
  ];

  return (
    <div className="mx-auto max-w-6xl px-4 py-8 md:px-8">
      <header>
        <h1 className="font-display text-2xl font-semibold tracking-tight text-fg md:text-3xl">
          {greeting()}
          {firstName ? `, ${firstName}` : ""}.
        </h1>
        <p className="mt-1 text-[15px] text-fg-muted">{strings.today.subtitle}</p>
      </header>

      <section className="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
        {stats.map((stat) => (
          <div
            key={stat.label}
            className="rounded-2xl border border-hairline bg-surface p-5 shadow-pane"
          >
            <stat.icon className={cn("h-4.5 w-4.5 h-5 w-5", stat.tone)} />
            <p className="mt-3 font-display text-3xl font-semibold tabular-nums text-fg">
              {stat.value}
            </p>
            <p className="mt-1 text-[13px] text-fg-subtle">{stat.label}</p>
          </div>
        ))}
      </section>

      <div className="mt-8 grid gap-6 lg:grid-cols-[3fr_2fr]">
        <section className="rounded-2xl border border-hairline bg-surface p-6 shadow-pane">
          <div className="flex items-center justify-between">
            <h2 className="font-display text-lg font-semibold text-fg">
              {strings.today.overdueTasks}
            </h2>
            <Link
              to="/tarefas"
              className="inline-flex items-center gap-1 text-[13px] font-medium text-accent hover:underline"
            >
              {strings.today.seeAll}
              <ArrowRight className="h-3.5 w-3.5" />
            </Link>
          </div>

          {deadlines.isLoading ? (
            <p className="mt-6 text-sm text-fg-subtle">…</p>
          ) : overdue.length === 0 ? (
            <p className="mt-6 flex items-center gap-2 text-sm text-fg-muted">
              <CheckCircle2 className="h-4 w-4 text-emerald-500" />
              {strings.today.emptyAttention}
            </p>
          ) : (
            <ul className="mt-4 divide-y divide-hairline">
              {overdue.slice(0, 6).map((task) => (
                <li key={task.id} className="flex items-center gap-3 py-3">
                  <span className="h-1.5 w-1.5 shrink-0 rounded-full bg-accent" aria-hidden />
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-[14px] font-medium text-fg">{task.title}</p>
                    <p className="truncate text-[12.5px] text-fg-subtle">{task.project.title}</p>
                  </div>
                  <span className="shrink-0 text-[12.5px] font-medium text-accent">
                    {formatDue(task.dueDate)}
                  </span>
                </li>
              ))}
            </ul>
          )}
        </section>

        <section className="rounded-2xl border border-hairline bg-surface p-6 shadow-pane">
          <h2 className="font-display text-lg font-semibold text-fg">{strings.today.quickStart}</h2>
          <p className="mt-1 text-[13px] text-fg-subtle">{strings.today.quickStartHint}</p>
          <ul className="mt-4 space-y-2">
            {modules.map((module) => (
              <li key={module.to}>
                <Link
                  to={module.to}
                  className="group flex items-center gap-3 rounded-xl border border-hairline px-4 py-3 transition-colors hover:border-hairline-strong hover:bg-surface-sunken/50"
                >
                  <div className="min-w-0 flex-1">
                    <p className="text-[13.5px] font-semibold text-fg">{module.title}</p>
                  </div>
                  <span className="rounded-full border border-hairline px-2 py-0.5 text-[10.5px] font-medium text-fg-subtle">
                    {strings.modules.comingSoon}
                  </span>
                  <ArrowRight className="h-3.5 w-3.5 text-fg-faint transition-transform group-hover:translate-x-0.5" />
                </Link>
              </li>
            ))}
          </ul>
        </section>
      </div>
    </div>
  );
}
