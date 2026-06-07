import { api } from "./api";

export type AiInsight = {
  id: string;
  type: string;
  output: string;
  model: string;
  createdAt: string;
};

// OpenAI generations routinely run longer than the global 10s client timeout,
// so the write calls get a generous per-request override. Reads stay on the
// default fast timeout.
const GENERATION_TIMEOUT = 60_000;

export async function listAiInsights() {
  const { data } = await api.get<AiInsight[]>("/ai/insights");
  return data;
}

export async function generateDashboardInsight() {
  const { data } = await api.post<AiInsight>("/ai/insights", undefined, {
    timeout: GENERATION_TIMEOUT,
  });
  return data;
}

export async function generateProjectPlan(projectId: string) {
  const { data } = await api.post<AiInsight>(
    "/ai/project-plan",
    { projectId },
    { timeout: GENERATION_TIMEOUT },
  );
  return data;
}
