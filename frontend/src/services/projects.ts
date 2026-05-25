import { api } from "./api";

export type ProjectStatus = "PLANNED" | "ACTIVE" | "PAUSED" | "DONE" | "ARCHIVED";
export type ProjectPriority = "LOW" | "MEDIUM" | "HIGH" | "CRITICAL";

export type Project = {
  id: string;
  title: string;
  description: string | null;
  status: ProjectStatus;
  priority: ProjectPriority;
  owner: string;
  createdAt: string;
  updatedAt: string;
};

export type ProjectInput = {
  title: string;
  description?: string | null;
  status: ProjectStatus;
  priority: ProjectPriority;
  owner: string;
};

export type ProjectFilters = {
  q?: string;
  status?: ProjectStatus;
  priority?: ProjectPriority;
};

export async function listProjects(filters: ProjectFilters = {}) {
  const { data } = await api.get<Project[]>("/projects", { params: filters });
  return data;
}

export async function getProject(id: string) {
  const { data } = await api.get<Project>(`/projects/${id}`);
  return data;
}

export async function createProject(input: ProjectInput) {
  const { data } = await api.post<Project>("/projects", input);
  return data;
}

export async function updateProject(id: string, input: Partial<ProjectInput>) {
  const { data } = await api.patch<Project>(`/projects/${id}`, input);
  return data;
}

export async function deleteProject(id: string) {
  await api.delete(`/projects/${id}`);
}
