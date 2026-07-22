import { api, setAccessToken, refreshAccessToken } from "./api";

export type SessionUser = { id: string; name: string; email: string; avatarUrl?: string | null };
export type SessionOrganization = { id: string; name: string; slug: string; segment?: string | null };

export type Session = {
  user: SessionUser;
  organization: SessionOrganization;
  roleKey: string;
};

type SessionResponse = Session & { accessToken: string };

export type RegisterInput = {
  name: string;
  email: string;
  password: string;
  organizationName: string;
  segment?: string;
};

export async function register(input: RegisterInput): Promise<Session> {
  const { data } = await api.post<SessionResponse>("/auth/register", input);
  setAccessToken(data.accessToken);
  return data;
}

export async function login(email: string, password: string): Promise<Session> {
  const { data } = await api.post<SessionResponse>("/auth/login", { email, password });
  setAccessToken(data.accessToken);
  return data;
}

export async function logout(): Promise<void> {
  try {
    await api.post("/auth/logout");
  } finally {
    setAccessToken(null);
  }
}

export async function fetchMe(): Promise<Session> {
  const { data } = await api.get<Session>("/auth/me");
  return data;
}

/** Silent session restore from the refresh cookie. Null when anonymous. */
export async function restoreSession(): Promise<Session | null> {
  const token = await refreshAccessToken();
  if (!token) return null;
  try {
    return await fetchMe();
  } catch {
    return null;
  }
}
