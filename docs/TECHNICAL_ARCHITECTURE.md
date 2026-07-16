# Arquitetura técnica — Dashmeboard Business OS

> **Status:** proposta evolutiva. **AS-IS** foi confirmado no repositório; **TO-BE** deve ser aprovado por ADR antes da implementação. Requisitos de produto: [PRODUCT_PRD.md](./PRODUCT_PRD.md).

## 1. Objetivos arquiteturais

1. Isolar dados e autorização por organização desde a primeira fatia.
2. Entregar o MVP em fatias verticais sem interromper o dashboard atual.
3. Manter um único fluxo de deploy funcional durante a migração.
4. Favorecer modularidade de domínio sem custo prematuro de microsserviços.
5. Tornar finanças, auditoria, jobs, integrações e IA rastreáveis e idempotentes.
6. Preservar ativos comprovados do repositório quando o custo de troca não gera valor ao cliente.

## 2. Arquitetura AS-IS

```text
Browser
  └─ React 19 + Vite SPA + React Router + TanStack Query
       └─ Axios /api (sem token e sem contexto de organização)
            └─ Express 4
                 ├─ /api/projects       CRUD
                 ├─ /api/analytics      agregações
                 ├─ /api/ai             OpenAI direto
                 └─ /api/health
                      └─ Prisma 6 + PostgreSQL
```

### 2.1 Inventário confirmado

| Área | Implementação atual | Evidência | Limite relevante |
| --- | --- | --- | --- |
| Monorepo | npm workspaces frontend/backend | `package.json` | sem pacote compartilhado de contratos |
| Frontend | React, Vite, TS, Tailwind, Router, Query | `frontend/package.json`, `frontend/src/main.tsx` | SPA sem auth/SSR |
| Shell | sidebar, mobile nav, header, tema | `frontend/src/layouts/RootLayout.tsx` | navegação de produto anterior |
| UI | primitives, toast, motion, dark/light | `frontend/src/components`, `frontend/src/styles/globals.css` | estética cyber/rust, cobertura parcial de acessibilidade |
| Projetos | CRUD real, busca e filtros | `frontend/src/pages/Projects.tsx`, `backend/src/controllers/projects.controller.ts` | sem tenant, paginação ou autorização |
| Analytics | contagens e throughput | `backend/src/services/analytics.service.ts` | consultas globais e N+1 diário no throughput |
| IA | OpenAI Responses e persistência | `backend/src/services/ai.service.ts` | provedor único; prompt/contexto completos; sem auth |
| API | Express + Zod + error middleware | `backend/src/app.ts`, `backend/src/middlewares/error.ts` | sem versão, request ID ou contrato comum |
| Rate limit | mapa em memória por IP para gerações | `backend/src/middlewares/rateLimit.ts` | por processo, inadequado a escala horizontal |
| Dados | 5 modelos Prisma | `backend/prisma/schema.prisma` | `UserRole` técnico; FKs por e-mail; sem org |
| Infra | Vercel → Railway → PostgreSQL | `DEPLOY.md`, `vercel.json`, `railway.json` | sem job worker/cache/object storage |
| Qualidade | CI, lint, typecheck, teste backend, build, audit, Docker | `.github/workflows/ci.yml` | testes estreitos; sem E2E/frontend/segurança tenant |
| Observação | health/readiness e Morgan dev | `backend/src/routes/health.ts`, `backend/src/app.ts` | sem logs estruturados, Sentry, métricas ou tracing |

### 2.2 Riscos AS-IS bloqueadores

- Todos os endpoints de dados e IA são públicos; CORS não é autenticação.
- Todos os dados são globais; qualquer futura organização vazaria sem scoping obrigatório.
- `Project.owner` e `Task.assignee` referenciam e-mail mutável, não ID estável.
- `DELETE /api/projects/:id` remove projeto e tarefas fisicamente, sem auditoria.
- Team/Workflows exibem conteúdo estático que pode ser interpretado como real.
- IA recebe dados amplos e persiste prompt/contexto sem política de minimização.
- Sem paginação, o limite fixo de 200 projetos não oferece contrato de continuação.
- Rate limiting e jobs não resistem a múltiplas instâncias/retries.

## 3. Decisões TO-BE propostas

### 3.1 Manter React/Vite no MVP

Embora o PRD cite Next.js como sugestão, migrar framework antes de validar o fluxo de negócio amplia risco sem resolver tenancy, autorização ou financeiro. A proposta é manter React/Vite, React Router e TanStack Query durante o MVP, modularizando o frontend. Reavaliar Next.js apenas se SSR, marketing unificado, server components ou edge rendering tiverem requisito medido.

### 3.2 Modular monolith

Manter um backend Express modular no início. Cada domínio terá limites explícitos, schemas e serviços de aplicação. Extrair workers/processos apenas para carga assíncrona; microsserviços somente após evidência operacional.

```text
Frontend SPA
  ├─ application shell / design system
  ├─ modules: identity, customers, sales, tasks, calendar, finance, dashboard...
  └─ typed API client
          │ HTTPS + JWT + organization path + request ID
API /api/v1
  ├─ identity & tenancy
  ├─ customers & timeline
  ├─ sales
  ├─ tasks & calendar
  ├─ finance
  ├─ dashboard/search/notifications
  ├─ communications/documents
  ├─ AI orchestration
  └─ audit/outbox
          ├─ PostgreSQL + Prisma
          ├─ Redis-compatible cache/queue [VALIDAR]
          ├─ worker (jobs, imports, notifications, AI)
          ├─ S3-compatible object storage [VALIDAR]
          └─ external providers through adapters
```

### 3.3 Backend como autoridade

- O frontend nunca decide autorização; apenas oculta/desabilita ações por UX.
- JWT identifica o usuário; `organizationId` vem do path e é validado contra membership.
- Toda consulta tenant-aware recebe um `RequestContext` `{userId, organizationId, permissions, requestId}`.
- Prisma não deve ser acessado diretamente por controllers; repositories/serviços exigem `organizationId`.
- RLS PostgreSQL é defesa em profundidade quando suportada pelo desenho operacional, não substituto da autorização da API.

### 3.4 Contratos e versão

Novas rotas vivem em `/api/v1`; as atuais permanecem em `/api` como legado temporário. Schemas Zod compartilhados/OpenAPI gerado evitam duplicação manual. Ver [API_CONTRACTS.md](./API_CONTRACTS.md).

### 3.5 Jobs e efeitos externos

Importações, notificações, automações, webhooks, processamento de documentos e chamadas longas de IA devem usar fila durável. Mutação transacional grava domínio + `OutboxEvent`; worker publica/processa com chave idempotente. Não se envia mensagem ou dispara cobrança dentro da transação HTTP.

## 4. Módulos e dependências

| Módulo | Responsabilidade | Pode depender de | Não deve depender de |
| --- | --- | --- | --- |
| Identity | sessão, perfil, convite | provedor de auth | módulos de negócio |
| Tenancy/RBAC | organizações, memberships, permissões | Identity | UI |
| Customers | cliente, contatos, tags, timeline | Tenancy, Audit | Finance internals |
| Sales | pipeline, oportunidade, proposta | Customers, Tasks, Documents | provider WhatsApp direto |
| Tasks | tarefas, checklist, comentários | Customers, Projects, Identity | Finance internals |
| Calendar | eventos/lembretes | Customers, Identity | Google/Outlook concreto |
| Finance | lançamentos, parcelas, pagamentos | Customers, Audit | UI ou IA direta |
| Projects | entrega simplificada | Customers, Tasks, Documents | funil comercial interno |
| Communications | templates e tentativas | Customers, Consent | SDK WhatsApp direto |
| Documents | metadados e acesso | Storage adapter, RBAC | armazenamento específico |
| Notifications | inbox e canais | Outbox, Preferences | módulos por chamada circular |
| Search | índice unificado/autorizado | eventos de domínio | consultas cross-tenant |
| AI | tools controladas e policy | serviços de aplicação | Prisma/SQL direto pelo modelo |
| Automations | receitas, execuções, retries | Outbox, módulos via commands | mutações sem idempotência |
| Reporting | read models/exportação | dados autorizados | queries livres do browser |
| Audit | evento imutável e diffs seguros | RequestContext | PII irrestrita |

Regra: integração entre módulos usa serviço público, command/query ou evento de domínio; não importar repository interno de outro módulo.

## 5. Estrutura de código proposta

```text
frontend/src/
  app/                 providers, router, guards, feature flags
  design-system/       tokens, primitives, components, patterns
  modules/<domain>/    api, schemas, queries, screens, components
  shared/              erros, formatação, telemetry

backend/src/
  platform/            env, http, auth, tenancy, db, queue, logging, flags
  modules/<domain>/
    domain/             entidades, políticas, eventos
    application/        use cases, DTOs, ports
    infrastructure/     Prisma repositories, provider adapters
    http/               routes, schemas, presenters
  jobs/                 workers
  app.ts

packages/contracts/    Zod/OpenAPI/types sem lógica de runtime sensível
```

Essa estrutura é uma **TO-BE**; não deve ser aplicada em uma refatoração big bang. Cada fatia move apenas o que toca.

## 6. Autenticação, tenancy e autorização

### 6.1 Fluxo

1. Provedor emite token assinado.
2. API valida assinatura, `iss`, `aud`, `exp` e `sub`.
3. API resolve `User` interno pelo `authSubject`.
4. `organizationId` explícito no path é resolvido para membership ativa.
5. Policy engine calcula permissões por role + overrides.
6. Serviço executa consulta sempre filtrada por organização.
7. Mutação crítica grava auditoria na mesma transação.

### 6.2 RBAC inicial

Roles do PRD mapeiam para permission keys como `customers.read`, `customers.write`, `finance.read`, `finance.record_payment`, `members.manage`, `exports.create`. A matriz final está pendente; negar por padrão. Escopo `own/team/all` pode ser adicionado sem mudar permission keys.

### 6.3 Defesa em profundidade

- índices e constraints incluem `organizationId`;
- FKs compostas ou validação transacional impedem vínculos entre tenants;
- testes automatizados tentam ler/alterar IDs de outra organização;
- cache key inclui organização + usuário quando necessário;
- jobs carregam `organizationId` e revalidam autorização/política temporal;
- logs nunca usam token, documento ou PII completa.

## 7. Persistência e consistência

O modelo detalhado está em [DATA_MODEL.md](./DATA_MODEL.md). Regras arquiteturais:

- UUID/ULID como IDs estáveis; timestamps UTC; timezone por organização/evento;
- dinheiro em `numeric(19,4)` + moeda ISO, nunca float;
- finanças usam registros de pagamento, não simples troca destrutiva de status;
- `AuditLog` é append-only; `ActivityEvent` é feed de produto e pode ser materializado;
- soft delete apenas onde restauração/retention fizer sentido; auditoria não é excluída junto;
- importações e webhooks têm chave externa/idempotente;
- agregações de dashboard evoluem de queries para read models conforme volume medido.

## 8. IA segura e agnóstica a provedor

```text
Pergunta
  → AI policy (plano, consentimento, limites)
  → planner/provider adapter
  → tool request estruturado
  → auth + RBAC + tenancy
  → serviço de aplicação
  → resposta com fontes
  → confirmação humana se houver efeito
  → auditoria + custo/latência
```

TO-BE:

- interface `AIProvider` para OpenAI/Gemini/Groq/Anthropic/Ollama;
- catálogo explícito de tools; sem SQL livre;
- redaction/minimização antes do provedor;
- prompt, output e contexto com política de retenção; evitar salvar PII bruta por padrão;
- timeouts, circuit breaker, budget por organização/plano e fila para tarefas longas;
- respostas exibem janela temporal e registros-fonte;
- action token de curta duração para confirmação de mutações propostas.

O `ai.service.ts` atual pode ser encapsulado como primeiro adapter, mas não é arquitetura multi-provider.

## 9. Integrações e arquivos

- `StoragePort`: criar upload assinado, confirmar objeto, obter download temporário, excluir/quarentenar.
- `MessagingPort`: preparar/entregar/status; no MVP apenas deep link/copiar e registro manual.
- `CalendarPort`, `PaymentPort` e `BillingPort`: só entram quando o respectivo escopo for aprovado.
- credenciais ficam criptografadas e referenciadas por organização; nunca no frontend.
- webhooks validam assinatura, persistem payload mínimo, deduplicam por provider event ID e respondem rápido.
- uploads têm allowlist MIME, limite, checksum, scan antimalware **VALIDAR**, metadata e autorização de download.

## 10. Performance e escalabilidade

### 10.1 Budgets propostos

| Operação | Meta inicial |
| --- | --- |
| API de leitura comum | p95 < 500 ms servidor, p99 < 1 s |
| mutação comum | p95 < 800 ms sem efeito externo |
| dashboard | payload crítico p95 < 1 s; progressive loading |
| busca | p95 < 700 ms no volume piloto |
| job | fila e progresso; sem manter HTTP longo |

Valores e cenário de carga são **VALIDAR**. O PRD pede ações comuns abaixo de 1 s e páginas principais abaixo de 2 s.

### 10.2 Controles

- cursor pagination e seleção de campos;
- índices compostos iniciando por `organizationId` e filtros dominantes;
- evitar `count`/groupBy globais em cada render;
- cache somente após medir, com invalidação por evento e chave tenant-aware;
- code splitting já existente no `frontend/src/App.tsx` deve ser preservado;
- virtualização para tabelas/kanban grandes e uploads diretos ao storage.

## 11. Observabilidade e operação

### 11.1 Sinais mínimos

- logs JSON: timestamp, level, service, environment, requestId, route template, status, duration, userId/organizationId pseudonimizados;
- métricas: RPS, erro, latência, pool DB, queries lentas, fila, retries, dead letters, custo/latência IA, uploads;
- Sentry frontend/backend com release e sourcemaps protegidos;
- health: liveness, readiness e dependências; o split atual é reaproveitável;
- audit alerts: falhas de auth, cross-tenant denied, exportações e ações financeiras.

### 11.2 SLOs pendentes

Disponibilidade, erro budget, retenção de logs, RPO/RTO e on-call são **VALIDAR** antes do primeiro cliente pagante. Backups precisam de teste de restauração, não apenas configuração.

## 12. Segurança e LGPD

- TLS; cookies/token conforme provedor, proteção CSRF se cookie; CSP e headers via Helmet;
- rate limit distribuído por usuário/organização/IP e custo da operação;
- Zod em entrada e serialização explícita em saída;
- secrets gerenciados por plataforma e rotação;
- princípio do menor privilégio no banco/storage;
- exportação do titular/organização em job auditado;
- exclusão/anomização conforme base legal e retenção;
- consentimento e preferência de comunicação versionados;
- threat modeling antes de finanças, upload, IA tools e WhatsApp oficial;
- pentest/revisão independente antes de escala comercial **VALIDAR**.

## 13. Deploy, ambientes e migrações

Manter a topologia Vercel/Railway/PostgreSQL inicialmente. Criar ambientes development, preview/staging e production com dados/segredos separados. Migrations Prisma são forward-only e seguem expand/contract; aplicação antiga e nova devem coexistir durante rollout. Estratégia completa: [MIGRATION_PLAN.md](./MIGRATION_PLAN.md).

CI deve evoluir com:

- teste unitário e integração por módulo;
- teste de contrato/OpenAPI;
- E2E dos fluxos críticos;
- suíte de isolamento multi-tenant;
- checagem de migração em snapshot representativo;
- SAST/dependency/container scan e secret scan;
- smoke pós-deploy e canary por organização.

## 14. Matriz de reaproveitamento

| Ativo atual | Decisão | Uso proposto | Condição |
| --- | --- | --- | --- |
| npm workspaces e TypeScript strict | **Reaproveitar** | monorepo e contratos | criar pacote compartilhado incrementalmente |
| React/Vite/Router/Query | **Reaproveitar** | frontend MVP | reavaliar Next só por requisito comprovado |
| Axios client | **Adaptar** | auth, request ID, problem errors, retries seguros | não retry mutação sem idempotência |
| ThemeProvider/ToastProvider | **Reaproveitar** | preferências e feedback | internacionalizar textos; a11y/ARIA live |
| RootLayout/Sidebar/MobileNav | **Adaptar** | shell Business OS | organization switcher, busca, quick create, RBAC |
| PageShell/PaneFrame/StatGrid | **Adaptar** | primitives/patterns | novos tokens, radius e densidade |
| Grain/Scanlines/ChromeText/Magnetic | **Restringir/substituir** | marketing ou momentos especiais | evitar ruído, custo e baixa legibilidade no core |
| Projects CRUD | **Adaptar** | padrão de slice e módulo Projects | v1 tenant-aware; owner por ID; paginação/auditoria |
| Analytics service | **Substituir progressivamente** | read models de negócio | manter endpoints legados até corte |
| AI OpenAI service | **Adaptar** | primeiro provider adapter | policy, tools, redaction e budgets |
| Express/Zod/error handler | **Reaproveitar/adaptar** | API modular/versionada | contexto, RFC Problem, schemas de saída |
| Prisma/PostgreSQL/migrations | **Reaproveitar** | persistência principal | expand/contract, tenancy e índices |
| rate limiter em memória | **Substituir** | limite distribuído | antes de escalar ou cobrar IA |
| health/readiness | **Reaproveitar** | operação | incluir queue/storage e métricas |
| Docker/CI/Vercel/Railway | **Reaproveitar/adaptar** | entrega contínua | staging, worker, smoke e rollback |
| Team/Workflows fixtures | **Substituir** | módulos reais ou remover da navegação | nunca apresentar fixture como dado real |
| esquema `UserRole` técnico | **Substituir** | memberships/roles de negócio | migração compatível e backfill |

## 15. ADRs obrigatórios antes da fundação

1. ADR-001: manter React/Vite no MVP.
2. ADR-002: provedor de identidade e estratégia de sessão.
3. ADR-003: Railway PostgreSQL vs Supabase PostgreSQL e uso de RLS.
4. ADR-004: modelagem RBAC e escopos `own/team/all`.
5. ADR-005: queue/cache provider e padrão outbox.
6. ADR-006: storage provider e política de arquivos.
7. ADR-007: retenção/auditoria/LGPD.
8. ADR-008: abstração de IA, provedores aprovados e budget.
9. ADR-009: dinheiro, moeda, competência e pagamentos parciais.

## 16. Assunções a validar

- usuário pode participar de mais de uma organização;
- backend permanece único ponto de acesso a dados;
- moeda padrão BRL, mas modelo guarda currency;
- organização define timezone e locale;
- pilotos toleram SPA sem SEO/SSR;
- integrações oficiais e billing não bloqueiam o primeiro piloto;
- PostgreSQL atual pode continuar durante o MVP;
- equipe aceita migração por feature flags e tenants piloto.
