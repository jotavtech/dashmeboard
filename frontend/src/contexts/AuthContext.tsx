import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { registerSessionExpiredHandler } from "@/services/api";
import * as authApi from "@/services/auth";
import type { RegisterInput, Session } from "@/services/auth";

type AuthStatus = "loading" | "authenticated" | "anonymous";

type AuthContextValue = {
  status: AuthStatus;
  session: Session | null;
  login: (email: string, password: string) => Promise<void>;
  register: (input: RegisterInput) => Promise<void>;
  logout: () => Promise<void>;
};

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [status, setStatus] = useState<AuthStatus>("loading");
  const [session, setSession] = useState<Session | null>(null);

  useEffect(() => {
    registerSessionExpiredHandler(() => {
      setSession(null);
      setStatus("anonymous");
    });
    let cancelled = false;
    authApi.restoreSession().then((restored) => {
      if (cancelled) return;
      setSession(restored);
      setStatus(restored ? "authenticated" : "anonymous");
    });
    return () => {
      cancelled = true;
    };
  }, []);

  const login = useCallback(async (email: string, password: string) => {
    const next = await authApi.login(email, password);
    setSession(next);
    setStatus("authenticated");
  }, []);

  const register = useCallback(async (input: RegisterInput) => {
    const next = await authApi.register(input);
    setSession(next);
    setStatus("authenticated");
  }, []);

  const logout = useCallback(async () => {
    await authApi.logout();
    setSession(null);
    setStatus("anonymous");
  }, []);

  const value = useMemo(
    () => ({ status, session, login, register, logout }),
    [status, session, login, register, logout],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error("useAuth must be used within AuthProvider");
  return ctx;
}
