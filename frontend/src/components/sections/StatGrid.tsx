import { motion } from "framer-motion";
import { fadeUp, stagger } from "@/lib/motion";
import { cn } from "@/lib/cn";

export type Stat = {
  index: string;
  label: string;
  value: string;
  suffix?: string;
  caption: string;
};

type StatGridProps = {
  stats: Stat[];
  className?: string;
};

export function StatGrid({ stats, className }: StatGridProps) {
  return (
    <motion.div
      variants={stagger(0.08)}
      initial="hidden"
      animate="show"
      className={cn(
        "grid grid-cols-1 gap-px border border-hairline bg-hairline md:grid-cols-2 lg:grid-cols-4",
        className,
      )}
    >
      {stats.map((s) => (
        <motion.div
          key={s.index}
          variants={fadeUp}
          whileHover={{ y: -2 }}
          transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
          className="group relative bg-surface-sunken/70 p-6 transition-colors duration-500 hover:bg-surface-sunken md:p-7"
        >
          <span className="pointer-events-none absolute inset-x-0 top-0 h-px origin-left scale-x-0 bg-accent/60 transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-x-100" />
          <span className="absolute right-4 top-4 font-mono text-[10px] tracking-[0.32em] text-fg-faint">
            {s.index}
          </span>

          <p className="font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
            <span className="text-fg-faint">·</span> {s.label}
          </p>

          <p className="mt-5 font-display text-[2.75rem] md:text-[3.5rem] leading-none text-fg">
            {s.value}
            {s.suffix && (
              <span className="text-accent">{s.suffix}</span>
            )}
          </p>

          <p className="mt-5 font-mono text-[11px] leading-relaxed tracking-[0.05em] text-fg-muted">
            {s.caption}
          </p>
        </motion.div>
      ))}
    </motion.div>
  );
}
