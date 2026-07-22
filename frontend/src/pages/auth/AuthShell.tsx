import type { FormEvent, ReactNode } from "react";
import { motion } from "framer-motion";
import { strings } from "@/i18n/strings";

type Props = {
  title: string;
  subtitle: string;
  children: ReactNode;
  footer: ReactNode;
  onSubmit: (event: FormEvent<HTMLFormElement>) => void;
  error?: string | null;
};

export function AuthShell({ title, subtitle, children, footer, onSubmit, error }: Props) {
  return (
    <div className="grid min-h-screen place-items-center bg-surface-sunken px-4 py-10 text-fg">
      <motion.div
        initial={{ opacity: 0, y: 14 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5, ease: [0.16, 1, 0.3, 1] }}
        className="w-full max-w-[400px]"
      >
        <div className="mb-8 text-center">
          <span className="inline-flex items-center gap-2">
            <span className="h-2.5 w-2.5 rounded-full bg-accent" aria-hidden />
            <span className="font-display text-xl font-semibold tracking-tight">
              {strings.product.name}
            </span>
          </span>
          <p className="mt-2 text-sm text-fg-subtle">{strings.product.tagline}</p>
        </div>

        <div className="rounded-2xl border border-hairline bg-surface p-8 shadow-pane">
          <h1 className="font-display text-2xl font-semibold tracking-tight">{title}</h1>
          <p className="mt-1 text-sm text-fg-muted">{subtitle}</p>

          <form onSubmit={onSubmit} className="mt-6 space-y-4" noValidate>
            {children}
            {error && (
              <p role="alert" className="rounded-lg border border-accent/30 bg-accent/5 px-3 py-2 text-sm text-accent">
                {error}
              </p>
            )}
          </form>
        </div>

        <div className="mt-6 text-center text-sm text-fg-muted">{footer}</div>
      </motion.div>
    </div>
  );
}

type FieldProps = {
  id: string;
  label: string;
  type?: string;
  value: string;
  onChange: (value: string) => void;
  autoComplete?: string;
  hint?: string;
  autoFocus?: boolean;
};

export function AuthField({ id, label, type = "text", value, onChange, autoComplete, hint, autoFocus }: FieldProps) {
  return (
    <div>
      <label htmlFor={id} className="mb-1.5 block text-sm font-medium text-fg">
        {label}
      </label>
      <input
        id={id}
        type={type}
        value={value}
        onChange={(e) => onChange(e.target.value)}
        autoComplete={autoComplete}
        autoFocus={autoFocus}
        required
        className="w-full rounded-lg border border-hairline bg-surface px-3.5 py-2.5 text-[15px] text-fg outline-none transition-colors placeholder:text-fg-faint focus:border-accent focus:ring-2 focus:ring-accent/20"
      />
      {hint && <p className="mt-1 text-xs text-fg-subtle">{hint}</p>}
    </div>
  );
}

export function AuthSubmit({ label, busy }: { label: string; busy: boolean }) {
  return (
    <button
      type="submit"
      disabled={busy}
      className="w-full rounded-lg bg-accent px-4 py-2.5 text-[15px] font-semibold text-white transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
    >
      {busy ? strings.auth.loading : label}
    </button>
  );
}
