import { api } from "./api";

export type HealthStatus = {
  status: "ok" | "degraded";
  uptime: number;
  timestamp: string;
  environment: string;
  services: {
    api: "up";
    database: "up" | "down";
  };
};

export async function getHealth() {
  const startedAt = performance.now();
  const { data } = await api.get<HealthStatus>("/health/ready");

  return {
    ...data,
    latencyMs: Math.round(performance.now() - startedAt),
  };
}
