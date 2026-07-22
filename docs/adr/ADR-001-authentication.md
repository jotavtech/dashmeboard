# ADR-001 — Autenticação e sessão

**Status:** aceito · **Data:** 2026-07-22

## Contexto

A API é pública hoje (débito consciente da V1). O Business OS exige auth multi-tenant
(User + Organization + Membership já modelados). As dependências `bcryptjs` e
`jsonwebtoken` já estão instaladas e permitem mais de um desenho.

## Decisão

1. **Access token JWT de vida curta (15 min)**, assinado com `JWT_SECRET` (obrigatório em
   produção), transportado via header `Authorization: Bearer`. **Nunca persistido** no
   cliente (memória apenas). Payload mínimo: `sub` (userId), `org` (organizationId),
   `role` (RoleKey da membership ativa).
2. **Refresh token opaco (não-JWT)** de 30 dias em **cookie `httpOnly` + `Secure` +
   `SameSite=Lax`**, escopo `/api/auth`. Valor aleatório (48 bytes); somente o **SHA-256**
   é armazenado (`refresh_tokens.token_hash`).
3. **Rotation obrigatória:** cada uso do refresh revoga o token e emite um novo
   (`replaced_by_id` encadeia a linhagem). Reuso de token revogado → revoga a cadeia
   inteira da sessão (detecção de roubo).
4. **Hashing de senha: bcryptjs, custo 12.** Argon2 foi considerado e adiado — exigiria
   binário nativo no Alpine do Railway; bcrypt custo 12 é adequado ao perfil de risco atual.
5. Logout revoga o refresh corrente e limpa o cookie. Bloqueio de conta via
   `User.status = BLOCKED` é verificado no login **e** no refresh.
6. Rate limit no login/refresh (middleware in-memory existente; Redis quando houver).

## Alternativa rejeitada

**JWT em `localStorage`** — vulnerável a exfiltração por XSS, sem revogação server-side,
apontado pelo checklist de segurança (§30 do PRD) e pelo SonarCloud. Rejeitado.

## Consequências

- Novo model `RefreshToken` no schema (hash, expiração, revogação, cadeia de rotation).
- Frontend mantém o access em memória e faz refresh silencioso em 401 (interceptor axios).
- Multi-org por usuário já suportado: o access carrega a org ativa; trocar de org = novo
  token via `POST /api/auth/switch-org` (fase 2 — a UI de troca chega com o segundo org).
