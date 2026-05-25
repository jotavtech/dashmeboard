import { Routes, Route, Navigate } from "react-router-dom";
import RootLayout from "./layouts/RootLayout";
import DashboardPage from "./pages/Dashboard";
import ProjectsPage from "./pages/Projects";
import AnalyticsPage from "./pages/Analytics";
import TeamPage from "./pages/Team";
import WorkflowsPage from "./pages/Workflows";
import DatabasePage from "./pages/Database";
import AgentsPage from "./pages/Agents";
import SettingsPage from "./pages/Settings";

export default function App() {
  return (
    <Routes>
      <Route element={<RootLayout />}>
        <Route path="/" element={<DashboardPage />} />
        <Route path="/projects" element={<ProjectsPage />} />
        <Route path="/analytics" element={<AnalyticsPage />} />
        <Route path="/team" element={<TeamPage />} />
        <Route path="/workflows" element={<WorkflowsPage />} />
        <Route path="/database" element={<DatabasePage />} />
        <Route path="/agents" element={<AgentsPage />} />
        <Route path="/settings" element={<SettingsPage />} />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Route>
    </Routes>
  );
}
