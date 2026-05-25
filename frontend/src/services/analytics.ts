import { api } from "./api";

export type Overview = {
  activeProjects: number;
  completedTasks: number;
  activeUsers: number;
  weeklyPerformance: number;
  uptimePercent: number;
  pipelineRuns: number;
};

export type ActivityItem = {
  id: string;
  type: "commit" | "deploy" | "update" | "project" | "agent";
  title: string;
  detail: string;
  timestamp: string;
};

export type Throughput = { day: string; value: number };

export async function getOverview() {
  const { data } = await api.get<Overview>("/analytics/overview");
  return data;
}

export async function getActivity(limit = 12) {
  const { data } = await api.get<ActivityItem[]>("/analytics/activity", {
    params: { limit },
  });
  return data;
}

export async function getThroughput() {
  const { data } = await api.get<Throughput[]>("/analytics/throughput");
  return data;
}
