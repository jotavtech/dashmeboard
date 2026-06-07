import { lazy, Suspense, type ReactNode } from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import RootLayout from "./layouts/RootLayout";

const DashboardPage = lazy(() => import("./pages/Dashboard"));
const ProjectsPage = lazy(() => import("./pages/Projects"));
const AnalyticsPage = lazy(() => import("./pages/Analytics"));
const TeamPage = lazy(() => import("./pages/Team"));
const WorkflowsPage = lazy(() => import("./pages/Workflows"));
const AiCenterPage = lazy(() => import("./pages/AiCenter"));
const DatabasePage = lazy(() => import("./pages/Database"));
const SettingsPage = lazy(() => import("./pages/Settings"));

export default function App() {
  return (
    <Routes>
      <Route element={<RootLayout />}>
        <Route path="/" element={<RouteFallback><DashboardPage /></RouteFallback>} />
        <Route path="/projects" element={<RouteFallback><ProjectsPage /></RouteFallback>} />
        <Route path="/analytics" element={<RouteFallback><AnalyticsPage /></RouteFallback>} />
        <Route path="/team" element={<RouteFallback><TeamPage /></RouteFallback>} />
        <Route path="/workflows" element={<RouteFallback><WorkflowsPage /></RouteFallback>} />
        <Route path="/ai" element={<RouteFallback><AiCenterPage /></RouteFallback>} />
        <Route path="/database" element={<RouteFallback><DatabasePage /></RouteFallback>} />
        <Route path="/settings" element={<RouteFallback><SettingsPage /></RouteFallback>} />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Route>
    </Routes>
  );
}

function RouteFallback({ children }: { children: ReactNode }) {
  return (
    <Suspense
      fallback={
        <div className="grid min-h-[60vh] place-items-center font-mono text-[11px] uppercase tracking-[0.32em] text-fg-subtle">
          · loading interface
        </div>
      }
    >
      {children}
    </Suspense>
  );
}
