import { motion } from "framer-motion";
import { Moon, Sun } from "lucide-react";
import { PageShell } from "@/components/sections/PageShell";
import { PaneFrame } from "@/components/primitives/PaneFrame";
import { useTheme, type Theme } from "@/contexts/ThemeContext";
import { cn } from "@/lib/cn";

export default function SettingsPage() {
  const { theme, setTheme } = useTheme();

  return (
    <PageShell
      section="section · settings"
      marker="preferences"
      title={<>Node preferences.</>}
      description="Theme and presentation defaults for this dashboard. Only local UI preferences are exposed here."
    >
      <div className="grid grid-cols-1 gap-10 lg:grid-cols-2">
        <PaneFrame index="01" label="Appearance" meta="theme" delay={0.04}>
          <div className="space-y-5 p-6">
            <div>
              <p className="mb-3 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
                · theme
              </p>
              <div className="grid grid-cols-2 gap-2">
                <ThemeChip
                  value="dark"
                  active={theme === "dark"}
                  label="DARK"
                  caption="editorial cyber"
                  icon={<Moon className="h-3.5 w-3.5" />}
                  onSelect={setTheme}
                />
                <ThemeChip
                  value="light"
                  active={theme === "light"}
                  label="LIGHT"
                  caption="paper editorial"
                  icon={<Sun className="h-3.5 w-3.5" />}
                  onSelect={setTheme}
                />
              </div>
            </div>
            <Row label="Accent" value="rust · #FF3B1F" tone="text-accent" />
            <Row label="Density" value="compact" />
            <Row label="Reduced motion" value="follow system" />
          </div>
        </PaneFrame>

        <PaneFrame index="02" label="Locale" meta="time · language" delay={0.08}>
          <div className="space-y-5 p-6">
            <Row label="Language" value="pt-BR" />
            <Row label="Timezone" value="BRT · UTC-3" />
            <Row label="Date format" value="ISO 8601" />
            <Row label="First day of week" value="Monday" />
          </div>
        </PaneFrame>

        <PaneFrame index="03" label="Runtime" meta="environment" delay={0.12}>
          <div className="space-y-5 p-6">
            <Row label="Frontend" value="Vite · React" />
            <Row label="Backend" value="Express · Prisma" />
            <Row label="Database" value="PostgreSQL" tone="text-emerald-300" />
            <Row label="Quality" value="CI · SonarCloud" />
          </div>
        </PaneFrame>

        <PaneFrame index="04" label="Presentation mode" meta="checklist" delay={0.16}>
          <div className="space-y-5 p-6">
            <Row label="CRUD" value="projects" tone="text-emerald-300" />
            <Row label="Health panel" value="live API" tone="text-emerald-300" />
            <Row label="Docker" value="compose ready" tone="text-emerald-300" />
            <Row label="Docs" value="README checklist" tone="text-emerald-300" />
          </div>
        </PaneFrame>
      </div>
    </PageShell>
  );
}

function ThemeChip({
  value,
  active,
  label,
  caption,
  icon,
  onSelect,
}: {
  value: Theme;
  active: boolean;
  label: string;
  caption: string;
  icon: React.ReactNode;
  onSelect: (v: Theme) => void;
}) {
  return (
    <button
      type="button"
      onClick={() => onSelect(value)}
      className={cn(
        "relative flex items-center gap-3 border px-4 py-3 text-left",
        "transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]",
        active
          ? "border-accent text-fg shadow-glow-soft"
          : "border-hairline text-fg-subtle hover:border-hairline-strong hover:text-fg-muted",
      )}
    >
      {active && (
        <motion.span
          layoutId="theme-chip-active"
          className="absolute inset-0 -z-0 bg-accent/[0.06]"
          transition={{ type: "spring", stiffness: 360, damping: 32 }}
        />
      )}
      <span className={cn("relative", active ? "text-accent" : "text-fg-faint")}>{icon}</span>
      <span className="relative flex flex-col">
        <span className="font-mono text-[11px] uppercase tracking-[0.32em]">{label}</span>
        <span className="font-mono text-[9px] uppercase tracking-[0.22em] text-fg-faint">
          {caption}
        </span>
      </span>
    </button>
  );
}

function Row({ label, value, tone = "text-fg" }: { label: string; value: string; tone?: string }) {
  return (
    <div className="flex items-baseline justify-between border-b border-hairline pb-3 last:border-b-0">
      <span className="font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
        · {label}
      </span>
      <span className={`font-mono text-[12px] uppercase tracking-[0.22em] ${tone}`}>{value}</span>
    </div>
  );
}
