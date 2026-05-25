import type { ReactNode } from "react";
import { motion } from "framer-motion";
import { ease } from "@/lib/motion";
import { TerminalLabel } from "@/components/primitives/TerminalLabel";

type PageShellProps = {
  section: string;
  marker: string;
  title: ReactNode;
  description?: ReactNode;
  actions?: ReactNode;
  children: ReactNode;
};

export function PageShell({
  section,
  marker,
  title,
  description,
  actions,
  children,
}: PageShellProps) {
  return (
    <div className="relative mx-auto w-full max-w-[1440px] px-6 py-10 md:px-10 md:py-14 lg:px-14 lg:py-16">
      <div className="engraved-grid pointer-events-none absolute inset-0 -z-0 opacity-60" />

      <motion.header
        initial={{ opacity: 0, y: -12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.7, ease: ease.outExpo }}
        className="relative flex flex-col gap-5 border-b border-hairline pb-8 md:gap-6 md:pb-10"
      >
        <div className="flex items-center justify-between">
          <TerminalLabel>
            · {section} <span className="text-fg-faint">/</span> {marker}
          </TerminalLabel>
          {actions && <div className="flex items-center gap-2">{actions}</div>}
        </div>

        <h1 className="font-display text-display-md text-fg tracking-tightest">
          {title}
        </h1>

        {description && (
          <p className="max-w-2xl text-fg-muted leading-relaxed">
            {description}
          </p>
        )}
      </motion.header>

      <motion.div
        initial={{ opacity: 0, y: 18 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.7, ease: ease.outExpo, delay: 0.1 }}
        className="relative pt-10 md:pt-12"
      >
        {children}
      </motion.div>
    </div>
  );
}
