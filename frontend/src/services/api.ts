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
});

export type ApiError = {
  message: string;
  status: number;
};

api.interceptors.response.use(
  (res) => res,
  (err) => {
    const apiErr: ApiError = {
      message: err?.response?.data?.message ?? err.message ?? "Unknown error",
      status: err?.response?.status ?? 0,
    };
    return Promise.reject(apiErr);
  },
);
