import axios from "axios";

const baseURL =
  import.meta.env.VITE_API_URL ?? "http://localhost:4000/api";

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
