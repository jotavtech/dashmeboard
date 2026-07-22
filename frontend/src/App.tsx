import { lazy, Suspense, type ReactNode } from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import { CalendarDays, TrendingUp, Users, Wallet } from "lucide-react";
import RootLayout from "./layouts/RootLayout";
import { RequireAuth } from "./components/RequireAuth";
import ModulePreview from "./pages/ModulePreview";
import { strings } from "./i18n/strings";

const HojePage = lazy(() => import("./pages/Hoje"));
const LoginPage = lazy(() => import("./pages/auth/Login"));
const RegisterPage = lazy(() => import("./pages/auth/Register"));
const ProjectsPage = lazy(() => import("./pages/Projects"));
const AnalyticsPage = lazy(() => import("./pages/Analytics"));
const SettingsPage = lazy(() => import("./pages/Settings"));

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<RouteFallback><LoginPage /></RouteFallback>} />
      <Route path="/registro" element={<RouteFallback><RegisterPage /></RouteFallback>} />

      <Route
        element={
          <RequireAuth>
            <RootLayout />
          </RequireAuth>
        }
      >
        <Route path="/" element={<RouteFallback><HojePage /></RouteFallback>} />
        <Route
          path="/clientes"
          element={<ModulePreview icon={Users} {...strings.modules.customers} />}
        />
        <Route
          path="/vendas"
          element={<ModulePreview icon={TrendingUp} {...strings.modules.sales} />}
        />
        <Route
          path="/financeiro"
          element={<ModulePreview icon={Wallet} {...strings.modules.finance} />}
        />
        <Route
          path="/agenda"
          element={<ModulePreview icon={CalendarDays} {...strings.modules.calendar} />}
        />
        <Route path="/tarefas" element={<RouteFallback><ProjectsPage /></RouteFallback>} />
        <Route path="/relatorios" element={<RouteFallback><AnalyticsPage /></RouteFallback>} />
        <Route path="/configuracoes" element={<RouteFallback><SettingsPage /></RouteFallback>} />

        {/* Legacy paths from the V1 cockpit shell. */}
        <Route path="/projects" element={<Navigate to="/tarefas" replace />} />
        <Route path="/analytics" element={<Navigate to="/relatorios" replace />} />
        <Route path="/settings" element={<Navigate to="/configuracoes" replace />} />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Route>
    </Routes>
  );
}

function RouteFallback({ children }: { children: ReactNode }) {
  return (
    <Suspense
      fallback={
        <div className="grid min-h-[60vh] place-items-center">
          <span
            className="h-7 w-7 animate-spin rounded-full border-2 border-hairline border-t-accent"
            aria-label={strings.auth.loading}
          />
        </div>
      }
    >
      {children}
    </Suspense>
  );
}
