import { NavLink } from "react-router-dom";
import { motion, useReducedMotion } from "framer-motion";
import {
  LayoutDashboard,
  FolderKanban,
  BarChart3,
  Users,
  Workflow,
  Database,
  Settings,
  Circle,
  LogOut,
} from "lucide-react";
import { cn } from "@/lib/cn";

type NavItem = {
  to: string;
  bracket: string;
  label: string;
  icon: typeof LayoutDashboard;
};

export const NAV: NavItem[] = [
  { to: "/", bracket: "D", label: "Dashboard", icon: LayoutDashboard },
  { to: "/projects", bracket: "P", label: "Projects", icon: FolderKanban },
  { to: "/analytics", bracket: "A", label: "Analytics", icon: BarChart3 },
  { to: "/team", bracket: "T", label: "Team", icon: Users },
  { to: "/workflows", bracket: "W", label: "Workflows", icon: Workflow },
  { to: "/database", bracket: "B", label: "Database", icon: Database },
  { to: "/settings", bracket: "S", label: "Settings", icon: Settings },
];

export function Sidebar() {
  const reduce = useReducedMotion();

  return (
    <aside className="hidden md:flex md:w-[240px] lg:w-[260px] shrink-0 flex-col border-r border-hairline surface-overlay">
      <div className="flex h-16 items-center gap-3 border-b border-hairline px-5">
        <motion.span
          className="h-1.5 w-1.5 bg-accent"
          animate={
            reduce
              ? undefined
              : { opacity: [1, 0.45, 1], boxShadow: ["0 0 0px rgb(var(--accent) / 0)", "0 0 10px rgb(var(--accent) / 0.7)", "0 0 0px rgb(var(--accent) / 0)"] }
          }
          transition={{ duration: 2.4, repeat: Infinity, ease: "easeInOut" }}
        />
        <span className="font-mono text-[11px] uppercase tracking-[0.32em] text-fg-muted">
          dashme<span className="text-fg-faint">/</span>board
        </span>
      </div>

      <nav className="flex-1 overflow-y-auto px-3 py-6">
        <p className="px-3 mb-3 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-faint">
          · Navigation
        </p>
        <ul className="space-y-0.5">
          {NAV.map((item) => (
            <li key={item.to}>
              <NavLink
                to={item.to}
                end={item.to === "/"}
                className={({ isActive }) =>
                  cn(
                    "group relative flex items-center gap-3 px-3 py-2.5",
                    "font-mono text-[12px] uppercase tracking-[0.22em]",
                    "transition-[color,background-color,box-shadow] duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]",
                    isActive
                      ? "bg-surface-sunken text-fg"
                      : "text-fg-subtle hover:bg-surface-sunken/60 hover:text-fg-muted hover:shadow-glow-soft",
                  )
                }
              >
                {({ isActive }) => (
                  <>
                    {isActive && (
                      <motion.span
                        layoutId="sidebar-active-bar"
                        className="absolute left-0 top-0 bottom-0 w-px bg-accent"
                        transition={{ type: "spring", stiffness: 360, damping: 32 }}
                      />
                    )}
                    {isActive && (
                      <motion.span
                        layoutId="sidebar-active-glow"
                        className="pointer-events-none absolute inset-0 bg-accent/[0.04]"
                        transition={{ type: "spring", stiffness: 360, damping: 32 }}
                      />
                    )}
                    <span
                      className={cn(
                        "relative inline-flex items-center justify-center w-6 font-mono text-[10px] tracking-widest",
                        isActive ? "text-accent" : "text-fg-faint group-hover:text-fg-subtle",
                      )}
                    >
                      [{item.bracket}]
                    </span>
                    <item.icon
                      className={cn(
                        "relative h-3.5 w-3.5 shrink-0 transition-transform duration-300 ease-out group-hover:scale-110",
                        isActive ? "text-fg" : "text-fg-subtle",
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
        <div className="flex items-center gap-3 border border-hairline bg-surface-sunken/60 px-3 py-3 transition-colors duration-300 hover:border-hairline-strong">
          <div className="grid h-8 w-8 place-items-center bg-surface-raised font-mono text-[10px] text-fg-muted">
            JV
          </div>
          <div className="flex-1 min-w-0">
            <p className="font-mono text-[11px] uppercase tracking-[0.22em] text-fg truncate">
              jotavtech
            </p>
            <p className="mt-0.5 inline-flex items-center gap-1.5 font-mono text-[9px] uppercase tracking-[0.32em] text-fg-subtle">
              <Circle className="h-1.5 w-1.5 fill-emerald-400 text-emerald-400" />
              online
            </p>
          </div>
          <button
            type="button"
            className="grid h-7 w-7 place-items-center text-fg-subtle transition-colors duration-200 hover:text-accent"
            aria-label="Logout"
          >
            <LogOut className="h-3.5 w-3.5" />
          </button>
        </div>
      </div>
    </aside>
  );
}

export function MobileNav() {
  const items = NAV.filter((item) =>
    ["/", "/projects", "/analytics", "/database", "/settings"].includes(item.to),
  );

  return (
    <nav className="fixed inset-x-3 bottom-3 z-50 border border-hairline-strong bg-surface-sunken/90 shadow-pane backdrop-blur md:hidden">
      <ul className="grid grid-cols-5">
        {items.map((item) => (
          <li key={item.to}>
            <NavLink
              to={item.to}
              end={item.to === "/"}
              aria-label={item.label}
              className={({ isActive }) =>
                cn(
                  "relative flex h-14 flex-col items-center justify-center gap-1 font-mono text-[9px] uppercase tracking-[0.18em]",
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
                      className="absolute inset-x-3 top-0 h-px bg-accent"
                      transition={{ type: "spring", stiffness: 360, damping: 34 }}
                    />
                  )}
                  <item.icon className={cn("h-4 w-4", isActive ? "text-accent" : "text-fg-subtle")} />
                  <span>{item.bracket}</span>
                </>
              )}
            </NavLink>
          </li>
        ))}
      </ul>
    </nav>
  );
}
