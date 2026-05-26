import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import App from "./App";
import { ToastProvider } from "./components/feedback/ToastProvider";
import { ThemeProvider } from "./contexts/ThemeContext";
import "./styles/globals.css";

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 30_000,
      refetchOnWindowFocus: false,
    },
  },
});

createRoot(document.getElementById("root")!).render(
  <StrictMode>
    <ThemeProvider>
      <BrowserRouter>
        <QueryClientProvider client={queryClient}>
          <ToastProvider>
            <App />
          </ToastProvider>
        </QueryClientProvider>
      </BrowserRouter>
    </ThemeProvider>
  </StrictMode>,
);
