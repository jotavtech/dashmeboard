import { NavLink, useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import {
  Sun,
  Users,
  TrendingUp,
  Wallet,
  CalendarDays,
  CheckSquare,
  BarChart3,
  Settings,
  LogOut,
} from "lucide-react";
import { cn } from "@/lib/cn";
import { useAuth } from "@/contexts/AuthContext";
import { strings } from "@/i18n/strings";

type NavItem = {
  to: string;
  label: string;
  icon: typeof Sun;
};

export const NAV: NavItem[] = [
  { to: "/", label: strings.nav.today, icon: Sun },
  { to: "/clientes", label: strings.nav.customers, icon: Users },
  { to: "/vendas", label: strings.nav.sales, icon: TrendingUp },
  { to: "/financeiro", label: strings.nav.finance, icon: Wallet },
  { to: "/agenda", label: strings.nav.calendar, icon: CalendarDays },
  { to: "/tarefas", label: strings.nav.tasks, icon: CheckSquare },
  { to: "/relatorios", label: strings.nav.reports, icon: BarChart3 },
  { to: "/configuracoes", label: strings.nav.settings, icon: Settings },
];

function initials(name: string): string {
  return name
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join("");
}

export function Sidebar() {
  const { session, logout } = useAuth();
  const navigate = useNavigate();

  async function handleLogout() {
    await logout();
    navigate("/login", { replace: true });
  }

  return (
    <aside className="hidden md:flex md:w-[240px] lg:w-[260px] shrink-0 flex-col border-r border-hairline surface-overlay">
      <div className="flex h-16 items-center gap-2.5 border-b border-hairline px-5">
        <span className="h-2 w-2 rounded-full bg-accent" aria-hidden />
        <span className="font-display text-[15px] font-semibold tracking-tight text-fg">
          {strings.product.name}
        </span>
      </div>

      <nav className="flex-1 overflow-y-auto px-3 py-6">
        <ul className="space-y-0.5">
          {NAV.map((item) => (
            <li key={item.to}>
              <NavLink
                to={item.to}
                end={item.to === "/"}
                className={({ isActive }) =>
                  cn(
                    "group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13.5px] font-medium",
                    "transition-[color,background-color] duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]",
                    isActive
                      ? "bg-surface-sunken text-fg"
                      : "text-fg-subtle hover:bg-surface-sunken/60 hover:text-fg",
                  )
                }
              >
                {({ isActive }) => (
                  <>
                    {isActive && (
                      <motion.span
                        layoutId="sidebar-active-bar"
                        className="absolute left-0 top-1.5 bottom-1.5 w-[3px] rounded-full bg-accent"
                        transition={{ type: "spring", stiffness: 360, damping: 32 }}
                      />
                    )}
                    <item.icon
                      className={cn(
                        "relative h-4 w-4 shrink-0 transition-transform duration-300 ease-out group-hover:scale-110",
                        isActive ? "text-accent" : "text-fg-subtle",
                      )}
                    />
                    <span className="relative flex-1">{item.label}</span>
                  </>
                )}
              </NavLink>
            </li>
          ))}
        </ul>
      </nav>

      <div className="border-t border-hairline px-3 py-4">
        <div className="flex items-center gap-3 rounded-xl border border-hairline bg-surface-sunken/60 px-3 py-3 transition-colors duration-300 hover:border-hairline-strong">
          <div className="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-accent/10 text-[12px] font-semibold text-accent">
            {session ? initials(session.user.name) : "…"}
          </div>
          <div className="flex-1 min-w-0">
            <p className="truncate text-[13px] font-semibold text-fg">
              {session?.user.name ?? "…"}
            </p>
            <p className="truncate text-[11.5px] text-fg-subtle">
              {session?.organization.name ?? ""}
            </p>
          </div>
          <button
            type="button"
            onClick={handleLogout}
            className="grid h-8 w-8 place-items-center rounded-lg text-fg-subtle transition-colors duration-200 hover:bg-surface-sunken hover:text-accent"
            aria-label={strings.nav.logout}
            title={strings.nav.logout}
          >
            <LogOut className="h-4 w-4" />
          </button>
        </div>
      </div>
    </aside>
  );
}

export function MobileNav() {
  const items = NAV.filter((item) =>
    ["/", "/clientes", "/vendas", "/financeiro", "/tarefas", "/configuracoes"].includes(item.to),
  );

  return (
    <nav className="fixed inset-x-3 bottom-3 z-50 rounded-2xl border border-hairline-strong bg-surface/95 shadow-pane backdrop-blur md:hidden">
      <ul className="grid grid-cols-6">
        {items.map((item) => (
          <li key={item.to}>
            <NavLink
              to={item.to}
              end={item.to === "/"}
              aria-label={item.label}
              className={({ isActive }) =>
                cn(
                  "relative flex h-14 flex-col items-center justify-center gap-1 text-[10px] font-medium",
                  "transition-colors duration-300",
                  isActive ? "text-fg" : "text-fg-subtle",
                )
              }
            >
              {({ isActive }) => (
                <>
                  {isActive && (
                    <motion.span
                      layoutId="mobile-active"
                      className="absolute inset-x-4 top-0 h-0.5 rounded-full bg-accent"
                      transition={{ type: "spring", stiffness: 360, damping: 34 }}
                    />
                  )}
                  <item.icon className={cn("h-4 w-4", isActive ? "text-accent" : "text-fg-subtle")} />
                  <span>{item.label}</span>
                </>
              )}
            </NavLink>
          </li>
        ))}
      </ul>
    </nav>
  );
}
