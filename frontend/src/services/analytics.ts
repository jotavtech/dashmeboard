import { api } from "./api";

export type Overview = {
  totalProjects: number;
  activeProjects: number;
  completedProjects: number;
  totalTasks: number;
  completedTasks: number;
  activeUsers: number;
  weeklyPerformance: number;
  projectsByStatus: Record<string, number>;
  projectsByPriority: Record<string, number>;
};

export type ActivityItem = {
  id: string;
  type: "commit" | "deploy" | "update" | "project" | "system";
  title: string;
  detail: string;
  timestamp: string;
};

export type Throughput = { day: string; value: number };

export type DatabaseOverview = {
  provider: string;
  orm: string;
  schema: string;
  tables: Array<{
    name: string;
    columns: number;
    rows: number;
    indexed: boolean;
  }>;
};

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

export async function getDatabaseOverview() {
  const { data } = await api.get<DatabaseOverview>("/analytics/database");
  return data;
}
