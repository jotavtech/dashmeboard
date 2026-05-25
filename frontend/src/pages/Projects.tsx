import { useMemo, useState } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { motion, AnimatePresence } from "framer-motion";
import { Plus, Search, Pencil, Trash2, X } from "lucide-react";
import { PageShell } from "@/components/sections/PageShell";
import { Magnetic } from "@/components/primitives/Magnetic";
import { ease } from "@/lib/motion";
import { cn } from "@/lib/cn";
import {
  listProjects,
  createProject,
  updateProject,
  deleteProject,
  type Project,
  type ProjectInput,
  type ProjectStatus,
  type ProjectPriority,
} from "@/services/projects";

const STATUS: ProjectStatus[] = ["PLANNED", "ACTIVE", "PAUSED", "DONE", "ARCHIVED"];
const PRIORITY: ProjectPriority[] = ["LOW", "MEDIUM", "HIGH", "CRITICAL"];

const PRIORITY_TONE: Record<ProjectPriority, string> = {
  LOW: "text-fg-muted",
  MEDIUM: "text-fg-muted",
  HIGH: "text-amber-300",
  CRITICAL: "text-accent",
};

const STATUS_TONE: Record<ProjectStatus, string> = {
  PLANNED: "text-fg-muted",
  ACTIVE: "text-emerald-300",
  PAUSED: "text-amber-300",
  DONE: "text-fg-muted",
  ARCHIVED: "text-fg-subtle",
};

const EMPTY_INPUT: ProjectInput = {
  title: "",
  description: "",
  status: "PLANNED",
  priority: "MEDIUM",
  owner: "jotavtech",
};

export default function ProjectsPage() {
  const [q, setQ] = useState("");
  const [status, setStatus] = useState<ProjectStatus | "">("");
  const [priority, setPriority] = useState<ProjectPriority | "">("");
  const [editing, setEditing] = useState<Project | null>(null);
  const [creating, setCreating] = useState(false);

  const qc = useQueryClient();

  const query = useQuery({
    queryKey: ["projects", { q, status, priority }],
    queryFn: () =>
      listProjects({
        q: q || undefined,
        status: status || undefined,
        priority: priority || undefined,
      }),
    placeholderData: [],
  });

  const createMut = useMutation({
    mutationFn: createProject,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ["projects"] });
      setCreating(false);
    },
  });

  const updateMut = useMutation({
    mutationFn: ({ id, input }: { id: string; input: Partial<ProjectInput> }) =>
      updateProject(id, input),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ["projects"] });
      setEditing(null);
    },
  });

  const deleteMut = useMutation({
    mutationFn: (id: string) => deleteProject(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["projects"] }),
  });

  const rows = useMemo(() => query.data ?? [], [query.data]);

  return (
    <PageShell
      section="section · projects"
      marker="crud"
      title={<>Projects orbiting this node.</>}
      description="Create, search and manage every project under operational control. Status reflects production state."
      actions={
        <Magnetic>
          <button
            type="button"
            onClick={() => setCreating(true)}
            className="group inline-flex items-center gap-2 border border-hairline-strong bg-surface-sunken px-4 py-2 font-mono text-[11px] uppercase tracking-[0.32em] text-fg transition-all duration-300 hover:bg-accent hover:text-surface hover:border-accent"
          >
            <Plus className="h-3.5 w-3.5" />
            new project
          </button>
        </Magnetic>
      }
    >
      <div className="mb-8 grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto_auto]">
        <label className="relative flex items-center">
          <Search className="absolute left-4 h-3.5 w-3.5 text-fg-subtle" />
          <input
            type="text"
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="search by title…"
            className="w-full border border-hairline bg-surface-sunken/60 py-3 pl-11 pr-4 font-mono text-[12px] tracking-[0.05em] text-fg placeholder:text-fg-faint focus:border-hairline-strong focus:outline-none"
          />
        </label>
        <FilterSelect
          value={status}
          onChange={(v) => setStatus(v as ProjectStatus | "")}
          options={["", ...STATUS]}
          label="status"
        />
        <FilterSelect
          value={priority}
          onChange={(v) => setPriority(v as ProjectPriority | "")}
          options={["", ...PRIORITY]}
          label="priority"
        />
      </div>

      <div className="border border-hairline">
        <div className="grid grid-cols-[80px_minmax(0,1.6fr)_120px_120px_140px_120px_80px] items-center gap-4 border-b border-hairline bg-surface-sunken/60 px-5 py-3 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
          <span>index</span>
          <span>title</span>
          <span>status</span>
          <span>priority</span>
          <span>owner</span>
          <span>updated</span>
          <span className="text-right">act</span>
        </div>

        {query.isLoading && (
          <div className="px-5 py-6 font-mono text-[11px] uppercase tracking-[0.32em] text-fg-subtle">
            · loading…
          </div>
        )}

        {!query.isLoading && rows.length === 0 && (
          <div className="px-5 py-10 text-center font-mono text-[11px] uppercase tracking-[0.32em] text-fg-subtle">
            · no projects found
          </div>
        )}

        <ul>
          {rows.map((p, i) => (
            <li
              key={p.id}
              className="grid grid-cols-[80px_minmax(0,1.6fr)_120px_120px_140px_120px_80px] items-center gap-4 border-b border-hairline px-5 py-4 transition-colors last:border-b-0 hover:bg-surface-raised/40"
            >
              <span className="font-mono text-[11px] tabular-nums text-fg-faint">
                {String(i + 1).padStart(2, "0")}
              </span>
              <div className="min-w-0">
                <p className="font-mono text-[13px] uppercase tracking-[0.14em] text-fg truncate">
                  {p.title}
                </p>
                {p.description && (
                  <p className="mt-1 font-mono text-[11px] text-fg-subtle truncate">
                    {p.description}
                  </p>
                )}
              </div>
              <Tag className={STATUS_TONE[p.status]}>{p.status}</Tag>
              <Tag className={PRIORITY_TONE[p.priority]}>{p.priority}</Tag>
              <span className="font-mono text-[11px] tracking-[0.1em] text-fg-muted truncate">
                {p.owner}
              </span>
              <span className="font-mono text-[10px] tabular-nums uppercase tracking-[0.32em] text-fg-subtle">
                {new Date(p.updatedAt).toLocaleDateString(undefined, {
                  month: "short",
                  day: "2-digit",
                })}
              </span>
              <div className="flex items-center justify-end gap-1">
                <IconBtn label="Edit" onClick={() => setEditing(p)}>
                  <Pencil className="h-3.5 w-3.5" />
                </IconBtn>
                <IconBtn
                  label="Delete"
                  intent="rust"
                  onClick={() => {
                    if (confirm(`Delete project "${p.title}"?`)) {
                      deleteMut.mutate(p.id);
                    }
                  }}
                >
                  <Trash2 className="h-3.5 w-3.5" />
                </IconBtn>
              </div>
            </li>
          ))}
        </ul>
      </div>

      <AnimatePresence>
        {(creating || editing) && (
          <ProjectModal
            initial={editing ?? EMPTY_INPUT}
            mode={editing ? "edit" : "create"}
            saving={createMut.isPending || updateMut.isPending}
            onClose={() => {
              setCreating(false);
              setEditing(null);
            }}
            onSubmit={(input) => {
              if (editing) {
                updateMut.mutate({ id: editing.id, input });
              } else {
                createMut.mutate(input);
              }
            }}
          />
        )}
      </AnimatePresence>
    </PageShell>
  );
}

function FilterSelect({
  value,
  onChange,
  options,
  label,
}: {
  value: string;
  onChange: (v: string) => void;
  options: string[];
  label: string;
}) {
  return (
    <label className="relative inline-flex items-center">
      <span className="absolute left-4 font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
        {label}
      </span>
      <select
        value={value}
        onChange={(e) => onChange(e.target.value)}
        className="appearance-none border border-hairline bg-surface-sunken/60 py-3 pl-24 pr-10 font-mono text-[11px] uppercase tracking-[0.22em] text-fg focus:border-hairline-strong focus:outline-none"
      >
        <option value="">all</option>
        {options.filter(Boolean).map((opt) => (
          <option key={opt} value={opt}>
            {opt}
          </option>
        ))}
      </select>
    </label>
  );
}

function Tag({ children, className }: { children: React.ReactNode; className?: string }) {
  return (
    <span
      className={cn(
        "inline-flex items-center justify-center border border-hairline px-2 py-1 font-mono text-[10px] uppercase tracking-[0.32em] w-fit",
        className,
      )}
    >
      {children}
    </span>
  );
}

function IconBtn({
  children,
  onClick,
  intent = "default",
  label,
}: {
  children: React.ReactNode;
  onClick: () => void;
  intent?: "default" | "rust";
  label: string;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      aria-label={label}
      className={cn(
        "grid h-8 w-8 place-items-center border border-hairline transition-colors",
        intent === "rust"
          ? "text-fg-muted hover:bg-accent hover:text-surface hover:border-accent"
          : "text-fg-muted hover:bg-surface-raised hover:text-fg",
      )}
    >
      {children}
    </button>
  );
}

function ProjectModal({
  initial,
  mode,
  saving,
  onClose,
  onSubmit,
}: {
  initial: ProjectInput | Project;
  mode: "create" | "edit";
  saving: boolean;
  onClose: () => void;
  onSubmit: (input: ProjectInput) => void;
}) {
  const [form, setForm] = useState<ProjectInput>({
    title: initial.title,
    description: initial.description ?? "",
    status: initial.status,
    priority: initial.priority,
    owner: initial.owner,
  });

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      transition={{ duration: 0.25 }}
      className="fixed inset-0 z-50 grid place-items-center bg-surface/70 backdrop-blur-sm p-6"
      onClick={onClose}
    >
      <motion.form
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        exit={{ opacity: 0, y: 20 }}
        transition={{ duration: 0.4, ease: ease.outExpo }}
        onClick={(e) => e.stopPropagation()}
        onSubmit={(e) => {
          e.preventDefault();
          onSubmit(form);
        }}
        className="w-full max-w-xl border border-hairline-strong bg-surface-sunken/95 shadow-pane"
      >
        <div className="flex items-center justify-between border-b border-hairline px-6 py-4">
          <span className="font-mono text-[11px] uppercase tracking-[0.32em] text-fg-muted">
            <span className="text-fg-faint">·</span> {mode === "create" ? "new project" : "edit project"}
          </span>
          <button
            type="button"
            onClick={onClose}
            aria-label="Close"
            className="grid h-7 w-7 place-items-center text-fg-muted transition-colors hover:text-fg"
          >
            <X className="h-3.5 w-3.5" />
          </button>
        </div>

        <div className="space-y-5 px-6 py-6">
          <Field label="Title">
            <input
              required
              value={form.title}
              onChange={(e) => setForm({ ...form, title: e.target.value })}
              className="w-full border border-hairline bg-surface-raised/60 px-4 py-3 font-mono text-[13px] text-fg focus:border-hairline-strong focus:outline-none"
            />
          </Field>

          <Field label="Description">
            <textarea
              rows={3}
              value={form.description ?? ""}
              onChange={(e) => setForm({ ...form, description: e.target.value })}
              className="w-full border border-hairline bg-surface-raised/60 px-4 py-3 font-mono text-[12px] text-fg focus:border-hairline-strong focus:outline-none resize-none"
            />
          </Field>

          <div className="grid grid-cols-2 gap-4">
            <Field label="Status">
              <select
                value={form.status}
                onChange={(e) => setForm({ ...form, status: e.target.value as ProjectStatus })}
                className="w-full appearance-none border border-hairline bg-surface-raised/60 px-4 py-3 font-mono text-[11px] uppercase tracking-[0.22em] text-fg focus:border-hairline-strong focus:outline-none"
              >
                {STATUS.map((s) => (
                  <option key={s} value={s}>
                    {s}
                  </option>
                ))}
              </select>
            </Field>

            <Field label="Priority">
              <select
                value={form.priority}
                onChange={(e) => setForm({ ...form, priority: e.target.value as ProjectPriority })}
                className="w-full appearance-none border border-hairline bg-surface-raised/60 px-4 py-3 font-mono text-[11px] uppercase tracking-[0.22em] text-fg focus:border-hairline-strong focus:outline-none"
              >
                {PRIORITY.map((p) => (
                  <option key={p} value={p}>
                    {p}
                  </option>
                ))}
              </select>
            </Field>
          </div>

          <Field label="Owner">
            <input
              required
              value={form.owner}
              onChange={(e) => setForm({ ...form, owner: e.target.value })}
              className="w-full border border-hairline bg-surface-raised/60 px-4 py-3 font-mono text-[12px] text-fg focus:border-hairline-strong focus:outline-none"
            />
          </Field>
        </div>

        <div className="flex items-center justify-end gap-3 border-t border-hairline px-6 py-4">
          <button
            type="button"
            onClick={onClose}
            className="border border-hairline px-4 py-2 font-mono text-[11px] uppercase tracking-[0.32em] text-fg-muted transition-colors hover:text-fg"
          >
            cancel
          </button>
          <button
            type="submit"
            disabled={saving}
            className="inline-flex items-center gap-2 border border-accent bg-accent px-4 py-2 font-mono text-[11px] uppercase tracking-[0.32em] text-ink transition-all duration-300 hover:bg-accent disabled:opacity-60"
          >
            {saving ? "saving…" : mode === "create" ? "create" : "save"}
          </button>
        </div>
      </motion.form>
    </motion.div>
  );
}

function Field({ label, children }: { label: string; children: React.ReactNode }) {
  return (
    <label className="block">
      <span className="mb-2 block font-mono text-[10px] uppercase tracking-[0.32em] text-fg-subtle">
        · {label}
      </span>
      {children}
    </label>
  );
}
