import { motion } from "framer-motion";
import { Moon, Sun } from "lucide-react";
import { useTheme } from "@/contexts/ThemeContext";
import { cn } from "@/lib/cn";

export function ThemeToggle({ className }: { className?: string }) {
  const { theme, toggle } = useTheme();
  const isDark = theme === "dark";

  return (
    <button
      type="button"
      onClick={toggle}
      aria-label={isDark ? "Activate light mode" : "Activate dark mode"}
      title={isDark ? "LIGHT MODE" : "DARK MODE"}
      className={cn(
        "group relative inline-flex h-7 items-center gap-2 border border-hairline px-2.5",
        "font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle",
        "transition-all duration-300 ease-out hover:border-hairline-strong hover:text-fg",
        "hover:shadow-glow-soft",
        className,
      )}
    >
      <span className="relative flex h-3 w-3 items-center justify-center">
        <motion.span
          key={isDark ? "moon" : "sun"}
          initial={{ opacity: 0, rotate: -45, scale: 0.6 }}
          animate={{ opacity: 1, rotate: 0, scale: 1 }}
          exit={{ opacity: 0, rotate: 45, scale: 0.6 }}
          transition={{ duration: 0.32, ease: [0.16, 1, 0.3, 1] }}
          className="absolute"
        >
          {isDark ? <Moon className="h-3 w-3" /> : <Sun className="h-3 w-3" />}
        </motion.span>
      </span>
      <span>{isDark ? "DARK" : "LIGHT"}</span>
    </button>
  );
}
