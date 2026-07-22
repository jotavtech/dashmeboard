import { useLocation } from "react-router-dom";
import { NAV } from "./Sidebar";
import { ThemeToggle } from "./ThemeToggle";

function todayLabel(): string {
  const formatted = new Intl.DateTimeFormat("pt-BR", {
    weekday: "long",
    day: "numeric",
    month: "long",
  }).format(new Date());
  return formatted.charAt(0).toUpperCase() + formatted.slice(1);
}

export function Header() {
  const { pathname } = useLocation();
  const current =
    NAV.find((item) => (item.to === "/" ? pathname === "/" : pathname.startsWith(item.to)))?.label ??
    "";

  return (
    <header className="sticky top-0 z-30 flex min-h-16 items-center justify-between gap-3 border-b border-hairline surface-overlay px-4 py-3 md:h-16 md:px-6 md:py-0">
      <h2 className="truncate font-display text-[17px] font-semibold tracking-tight text-fg">
        {current}
      </h2>

      <div className="flex shrink-0 items-center gap-3">
        <span className="hidden text-[13px] text-fg-subtle md:inline">{todayLabel()}</span>
        <ThemeToggle />
      </div>
    </header>
  );
}
