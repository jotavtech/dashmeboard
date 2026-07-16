# Documento 1 — Brief técnico para o Sol (arquiteto)

**Missão desta fase:** analisar o [`PRODUCT_PRD.md`](PRODUCT_PRD.md) e o repositório atual do
Dashmeboard. **Não implementar ainda.** Criar uma especificação técnica completa, identificar o
que pode ser reaproveitado e produzir um plano incremental que mantenha o sistema funcional
durante a migração.

Escrito em 2026-07-16 por quem executou a auditoria e as fases F0/F1 do PRD anterior.

---

## 1. Arquitetura atual (as-is)

Monorepo npm workspaces com dois pacotes:

```text
dashmeboard/
├── frontend/          SPA — React 19 + Vite 6 + TypeScript strict + Tailwind 3
│   ├── src/pages/     Dashboard, Projects, Analytics, Database, AiCenter, Settings, Team*, Workflows*
│   ├── src/components/  chrome/ (Header, Sidebar), primitives/ (PaneFrame, StatBar…), sections/
│   ├── src/services/  axios + TanStack Query 5 (api, projects, analytics, ai, health)
│   └── src/contexts/  ThemeContext (dark/light via localStorage)
├── backend/           Express 4 + TypeScript ESM + Prisma 6 (driver adapter pg) + Zod 3
│   ├── src/routes/    health, projects, tasks, analytics, ai
│   ├── src/controllers/  validação Zod na borda
│   ├── src/services/  projects, tasks, analytics, ai (OpenAI Responses, opcional)
│   ├── src/middlewares/  error (HttpError/ZodError), async, rateLimit (in-memory)
│   ├── src/test/guard.ts  guarda: testes só rodam contra banco local/TEST_DATABASE_URL
│   └── prisma/        schema, migrations commitadas, seed demo
├── docs/              PRODUCT_PRD.md (mestre), WORKFLOW.md, BRIEF_SOL.md, PRD-V2.md (histórico)
├── docker-compose.yml postgres:16 + backend + frontend (nginx)
├── .github/workflows/ CI (lint, typecheck, test+coverage, build, audit, docker, SonarCloud)
└── vercel.json / railway.json / Dockerfile (raiz p/ Railway)
```

* Páginas marcadas com `*` são 100% estáticas (dados fictícios hardcoded) — candidatas a remoção/reescrita.
* Deploy atual: Vercel (frontend) + Railway (API em `dashmeboard-api-production.up.railway.app`).
* **Sem autenticação** — a API é pública (era um débito consciente do PRD antigo; o PRD novo exige auth+orgs na Fase 1).
* Qualidade: lint/typecheck/build verdes; 15 testes de integração (supertest) passando; cobertura ~81% statements no backend.

## 2. Modelo de dados atual (Prisma / PostgreSQL 16)

```text
User          id, email(unique), name, role(FOUNDER|ENGINEER|DESIGNER|GUEST)
Project       id, title, description?, status(PLANNED|ACTIVE|PAUSED|DONE|ARCHIVED),
              priority(LOW|MEDIUM|HIGH|CRITICAL), owner→User.email,
              deadline?, client?, repoUrl?, deployUrl?, docsUrl?, activeBranch?,
              notes?(text/markdown), tags[]                       ← adicionados em F1
Task          id, title, description?, status(TODO|DOING|REVIEW|DONE), projectId(cascade),
              assignee?→User.email, completedAt?, dueDate?, order  ← dueDate/order em F1
AnalyticsLog  id, kind, payload(json)
AiInsight     id, type, prompt, output, context?(json), model
```

**Single-tenant.** Não há organização/empresa em nenhuma tabela. FKs por e-mail (owner/assignee)
são uma escolha frágil herdada da V1 — repensar no novo modelo.

## 3. Contratos de API atuais

Base `/api`, JSON, validação Zod, erros `{message, issues?}`:

```text
GET  /health · /health/ready
GET|POST /projects · GET|PATCH|DELETE /projects/:id · POST /projects/:id/tasks
PATCH|DELETE /tasks/:id            (mover status DONE seta completedAt; sair limpa)
GET  /analytics/overview · /activity · /throughput · /deadlines · /database
GET|POST /ai/insights · POST /ai/project-plan     (503 sem OPENAI_API_KEY; rate-limited)
```

## 4. Decisões já tomadas (não reabrir sem motivo)

1. **F0 — higiene (feita, testada):** `cross-env` no script de test; guarda de banco em
   `src/test/guard.ts` (testes destrutivos recusam host não-local); `.env` local aponta para o
   Postgres do Docker (porta **5433** — a 5432 do host pertence a um PostgreSQL nativo do Windows).
2. **F1 — domínio (feita, testada):** campos de contexto no Project, dueDate/order na Task,
   CRUD de tasks com regra de completedAt, `/analytics/deadlines`. Migration
   `20260716205917_v2_project_context_and_task_board` commitada.
3. Conventional commits em inglês; commits e PRs sem rastro de IA.
4. Workflow Sol×Fable e estrutura de branches: ver [`WORKFLOW.md`](WORKFLOW.md).

## 5. Limitações e dívidas conhecidas

* Sem auth/permissões/orgs (bloqueador para o PRD novo).
* Rate limit em memória (não sobrevive a réplicas).
* Páginas Team/Workflows fake; Settings quase toda estática.
* `ai.service` sem testes com mock do client OpenAI (12% de cobertura).
* **P8 — divergência de banco de produção:** `DEPLOY.md` documenta Railway PostgreSQL, mas o
  `.env` local continha uma URL de **Prisma Postgres (`db.prisma.io`)**. O banco de produção
  está vazio (1 user, 0 projects). Confirmar no painel do Railway qual `DATABASE_URL` a API usa
  antes de qualquer migration em produção.
* Frontend com fallback hardcoded da URL do Railway em `src/services/api.ts`.
* Visual atual "editorial-cyber" (mono uppercase, scanlines, accent rust) será substituído pela
  direção do PRD §23 (Business Command Center, roxo elétrico).

## 6. Tensões que a especificação PRECISA resolver

1. **Stack do frontend:** manter Vite SPA vs migrar para Next.js (PRD §29.1 sugere Next). Custo
   de migração × benefícios (SSR? SEO é irrelevante atrás de login). Decidir e justificar.
2. **Backend:** evoluir o Express+Prisma atual vs NestJS vs full-stack Next. Há código testado e
   padrões estabelecidos no Express — descartar tem custo real.
3. **Banco/plataforma:** Postgres gerenciado (Railway) vs Supabase (PRD sugere Supabase p/ auth
   + storage). Se Supabase: o que acontece com Prisma, migrations e o guard de testes?
4. **Multi-tenancy:** `organizationId` em toda tabela + escopo na aplicação vs RLS no banco.
   Definir modelo de vínculo usuário↔organização↔papel (PRD §21).
5. **Migração incremental:** o sistema atual (Projects/Tasks) precisa continuar funcional durante
   a transição (exigência do dono). Mapear Project/Task atuais para os módulos novos
   (§13 Tarefas, §15 Projetos) — o kanban de tasks da F1 é reaproveitável.
6. **Auth:** provedor (Supabase Auth vs Auth.js vs próprio), sessões, convites, recuperação de senha.
7. **IA:** camada multi-provider (PRD §29.1) substituindo o acoplamento direto ao SDK da OpenAI.

## 7. Entregáveis esperados do Sol

Gerar dentro de `/docs`:

1. `TECHNICAL_ARCHITECTURE.md` — arquitetura proposta + módulos + justificativas das decisões §6.
2. `DATA_MODEL.md` — modelo de dados multi-tenant completo (entidades do PRD §8–§21).
3. `API_CONTRACTS.md` — rotas e contratos por módulo.
4. `MIGRATION_PLAN.md` — plano incremental que mantém o sistema funcional; destino do schema atual.
5. `IMPLEMENTATION_ROADMAP.md` — etapas pequenas e sequenciadas p/ o Fable, cada uma com escopo,
   arquivos permitidos/proibidos, critérios de aceitação, testes exigidos e definição de pronto.
6. `DESIGN_SYSTEM.md` — tokens e regras derivadas do PRD §23 (o Fable implementa).
7. Riscos e checklist de revisão que o próprio Sol usará ao revisar os diffs do Fable.

## 8. Critérios de aceitação desta especificação

* Cada decisão do §6 respondida com justificativa e alternativa rejeitada.
* Nenhuma entidade do PRD §35.1 (MVP) sem lugar no modelo de dados.
* Isolamento multi-tenant especificado a ponto de ser testável (quem filtra, onde, como falha).
* Roadmap em etapas de no máximo ~1 branch/PR cada, começando pela Fase 1 do PRD §36.
* O plano preserva: guarda de testes, CI verde, migrations commitadas e o deploy atual
  funcionando até o cutover.
