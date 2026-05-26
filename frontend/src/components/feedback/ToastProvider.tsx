import { createContext, useCallback, useContext, useMemo, useState } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { AlertCircle, CheckCircle2, Info, X } from "lucide-react";
import { cn } from "@/lib/cn";

type ToastType = "success" | "error" | "info";

type Toast = {
  id: string;
  type: ToastType;
  title: string;
  description?: string;
};

type ToastInput = Omit<Toast, "id">;

type ToastContextValue = {
  notify: (toast: ToastInput) => void;
};

const ToastContext = createContext<ToastContextValue | null>(null);

const tone = {
  success: { icon: CheckCircle2, className: "border-emerald-400/40 text-emerald-200" },
  error: { icon: AlertCircle, className: "border-accent/60 text-accent" },
  info: { icon: Info, className: "border-hairline-strong text-fg-muted" },
} as const;

export function ToastProvider({ children }: { children: React.ReactNode }) {
  const [toasts, setToasts] = useState<Toast[]>([]);

  const remove = useCallback((id: string) => {
    setToasts((current) => current.filter((toast) => toast.id !== id));
  }, []);

  const notify = useCallback(
    (toast: ToastInput) => {
      const id = crypto.randomUUID();
      setToasts((current) => [...current.slice(-3), { ...toast, id }]);
      window.setTimeout(() => remove(id), 4200);
    },
    [remove],
  );

  const value = useMemo(() => ({ notify }), [notify]);

  return (
    <ToastContext.Provider value={value}>
      {children}
      <div className="pointer-events-none fixed right-4 top-20 z-[70] flex w-[min(420px,calc(100vw-2rem))] flex-col gap-3">
        <AnimatePresence>
          {toasts.map((toast) => {
            const meta = tone[toast.type];
            const Icon = meta.icon;
            return (
              <motion.div
                key={toast.id}
                initial={{ opacity: 0, x: 24, filter: "blur(8px)" }}
                animate={{ opacity: 1, x: 0, filter: "blur(0px)" }}
                exit={{ opacity: 0, x: 24, filter: "blur(8px)" }}
                className={cn(
                  "pointer-events-auto border bg-surface-sunken/95 p-4 shadow-pane backdrop-blur",
                  meta.className,
                )}
              >
                <div className="flex items-start gap-3">
                  <Icon className="mt-0.5 h-4 w-4 shrink-0" />
                  <div className="min-w-0 flex-1">
                    <p className="font-mono text-[11px] uppercase tracking-[0.22em] text-fg">
                      {toast.title}
                    </p>
                    {toast.description && (
                      <p className="mt-1 text-sm leading-relaxed text-fg-muted">
                        {toast.description}
                      </p>
                    )}
                  </div>
                  <button
                    type="button"
                    aria-label="Dismiss notification"
                    onClick={() => remove(toast.id)}
                    className="grid h-6 w-6 place-items-center text-fg-subtle transition-colors hover:text-fg"
                  >
                    <X className="h-3.5 w-3.5" />
                  </button>
                </div>
              </motion.div>
            );
          })}
        </AnimatePresence>
      </div>
    </ToastContext.Provider>
  );
}

export function useToast() {
  const context = useContext(ToastContext);
  if (!context) {
    throw new Error("useToast must be used inside ToastProvider");
  }

  return context;
}
