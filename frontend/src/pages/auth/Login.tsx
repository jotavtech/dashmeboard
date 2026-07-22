import { useState, type FormEvent } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useAuth } from "@/contexts/AuthContext";
import { strings } from "@/i18n/strings";
import type { ApiError } from "@/services/api";
import { AuthField, AuthShell, AuthSubmit } from "./AuthShell";

export default function LoginPage() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const location = useLocation() as { state?: { from?: string } };

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setBusy(true);
    setError(null);
    try {
      await login(email, password);
      navigate(location.state?.from ?? "/", { replace: true });
    } catch (err) {
      const status = (err as ApiError).status;
      setError(status === 401 ? strings.auth.invalidCredentials : strings.auth.genericError);
    } finally {
      setBusy(false);
    }
  }

  return (
    <AuthShell
      title={strings.auth.loginTitle}
      subtitle={strings.auth.loginSubtitle}
      onSubmit={handleSubmit}
      error={error}
      footer={
        <Link to="/registro" className="font-medium text-accent hover:underline">
          {strings.auth.switchToRegister}
        </Link>
      }
    >
      <AuthField
        id="email"
        label={strings.auth.email}
        type="email"
        value={email}
        onChange={setEmail}
        autoComplete="email"
        autoFocus
      />
      <AuthField
        id="password"
        label={strings.auth.password}
        type="password"
        value={password}
        onChange={setPassword}
        autoComplete="current-password"
      />
      <AuthSubmit label={strings.auth.submitLogin} busy={busy} />
    </AuthShell>
  );
}
