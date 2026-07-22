import { useState, type FormEvent } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "@/contexts/AuthContext";
import { strings } from "@/i18n/strings";
import type { ApiError } from "@/services/api";
import { AuthField, AuthShell, AuthSubmit } from "./AuthShell";

export default function RegisterPage() {
  const { register } = useAuth();
  const navigate = useNavigate();

  const [name, setName] = useState("");
  const [organizationName, setOrganizationName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setBusy(true);
    setError(null);
    try {
      await register({ name, email, password, organizationName });
      navigate("/", { replace: true });
    } catch (err) {
      const status = (err as ApiError).status;
      setError(status === 409 ? strings.auth.emailTaken : strings.auth.genericError);
    } finally {
      setBusy(false);
    }
  }

  return (
    <AuthShell
      title={strings.auth.registerTitle}
      subtitle={strings.auth.registerSubtitle}
      onSubmit={handleSubmit}
      error={error}
      footer={
        <Link to="/login" className="font-medium text-accent hover:underline">
          {strings.auth.switchToLogin}
        </Link>
      }
    >
      <AuthField
        id="name"
        label={strings.auth.name}
        value={name}
        onChange={setName}
        autoComplete="name"
        autoFocus
      />
      <AuthField
        id="organizationName"
        label={strings.auth.organizationName}
        value={organizationName}
        onChange={setOrganizationName}
        autoComplete="organization"
      />
      <AuthField
        id="email"
        label={strings.auth.email}
        type="email"
        value={email}
        onChange={setEmail}
        autoComplete="email"
      />
      <AuthField
        id="password"
        label={strings.auth.password}
        type="password"
        value={password}
        onChange={setPassword}
        autoComplete="new-password"
        hint={strings.auth.passwordHint}
      />
      <AuthSubmit label={strings.auth.submitRegister} busy={busy} />
    </AuthShell>
  );
}
