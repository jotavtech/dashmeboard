import axios from "axios";

const productionApiURL = "https://dashmeboard-api-production.up.railway.app/api";

function resolveBaseURL() {
  const configured = import.meta.env.VITE_API_URL?.trim();
  if (import.meta.env.DEV) return configured || "http://localhost:4000/api";
  if (configured?.startsWith("http://") || configured?.startsWith("https://")) {
    return configured;
  }
  return productionApiURL;
}

const baseURL = resolveBaseURL();

export const api = axios.create({
  baseURL,
  timeout: 10_000,
  // Refresh session travels in an httpOnly cookie (ADR-001).
  withCredentials: true,
});

/** Bare client for the refresh call itself — no interceptors, no recursion. */
const refreshClient = axios.create({ baseURL, timeout: 10_000, withCredentials: true });

// Access token lives in memory only (ADR-001): never localStorage.
let accessToken: string | null = null;
let onSessionExpired: (() => void) | null = null;

export function setAccessToken(token: string | null) {
  accessToken = token;
}

export function getAccessToken() {
  return accessToken;
}

/** AuthContext registers here to react when a silent refresh fails. */
export function registerSessionExpiredHandler(handler: () => void) {
  onSessionExpired = handler;
}

api.interceptors.request.use((config) => {
  if (accessToken) config.headers.Authorization = `Bearer ${accessToken}`;
  return config;
});

export type ApiError = {
  message: string;
  status: number;
};

type RetriableConfig = { _retried?: boolean; url?: string };

let refreshInFlight: Promise<string | null> | null = null;

export async function refreshAccessToken(): Promise<string | null> {
  refreshInFlight ??= refreshClient
    .post<{ accessToken: string }>("/auth/refresh")
    .then((res) => {
      setAccessToken(res.data.accessToken);
      return res.data.accessToken;
    })
    .catch(() => {
      setAccessToken(null);
      onSessionExpired?.();
      return null;
    })
    .finally(() => {
      refreshInFlight = null;
    });
  return refreshInFlight;
}

api.interceptors.response.use(
  (res) => res,
  async (err) => {
    const config = (err?.config ?? {}) as RetriableConfig;
    const status: number = err?.response?.status ?? 0;
    const isAuthRoute = config.url?.includes("/auth/") ?? false;

    // One silent refresh + replay per request; auth routes handle their own 401s.
    if (status === 401 && !config._retried && !isAuthRoute) {
      const token = await refreshAccessToken();
      if (token) {
        config._retried = true;
        return api.request(config);
      }
    }

    const apiErr: ApiError = {
      message: err?.response?.data?.message ?? err.message ?? "Unknown error",
      status,
    };
    return Promise.reject(apiErr);
  },
);
