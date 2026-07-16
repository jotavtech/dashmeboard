# Roadmap de implementação

> **Natureza:** sequência incremental, não cronograma. Equipe, orçamento e datas não foram informados; estimativas só devem ser produzidas após discovery técnico e decomposição. O plano preserva deployabilidade conforme [MIGRATION_PLAN.md](./MIGRATION_PLAN.md).

## 1. Resultado alvo

Validar com 3–5 empresas o fluxo:

`onboarding → cliente → oportunidade → próxima ação/tarefa → cobrança → pagamento/tentativa → visão geral/histórico → insight/ação assistida`.

Cada incremento precisa gerar uma versão demonstrável, segura e operacional. Navegação vazia ou módulo sustentado por fixture não conta como entrega.

## 2. Dependências macro

```text
R0 Baseline/ADRs
  └─ R1 Plataforma segura (auth + org + RBAC + contracts + observability)
      ├─ R2 CRM
      │   └─ R3 Comercial
      │       ├─ R4 Tarefas + Agenda
      │       └─ R5 Financeiro
      │           └─ R6 Dashboard/atenção
      ├─ R7 Busca + Notificações + Quick Create
      └─ R8 Comunicação + IA segura
          └─ R9 Piloto/Onboarding/Hardening
              └─ R10 Expansão pós-validação
```

R4 e parte de R7 podem avançar em paralelo após R2, mas a sequência de rollout deve manter um fluxo coerente.

## 3. Gates transversais

| Gate | Condição |
| --- | --- |
| G0 Decisão | requisitos/lacunas, owner e ADRs da fatia aprovados |
| G1 Design | fluxo, estados, API/schema, permission matrix e telemetry definidos |
| G2 Build | implementação atrás de flag; migration compatível; testes verdes |
| G3 Staging | E2E, a11y, segurança tenant, observabilidade e rollback exercitados |
| G4 Canary | organização interna/demo estável e reconciliação sem divergência |
| G5 Piloto | métricas/SLO dentro do threshold e suporte/runbook prontos |
| G6 Complete | legado correspondente deprecado ou decisão explícita de coexistência |

Nenhuma fatia financeira/tenancy avança com falha conhecida de isolamento ou reconciliação.

## 4. R0 — Baseline, discovery e decisões

### Objetivo

Transformar o PRD e o repo em baseline executável e reduzir decisões que mudam fundação.

### Entregas

- aprovar documentos deste diretório e registrar ADRs 001–009;
- mapear jornadas com empresas piloto e matriz RBAC por persona;
- classificar dados atuais (demo/interno/real) e destino;
- definir auth, hosting/banco, queue, storage, LGPD/retention, money e SLOs;
- staging, backup/restore drill, request ID/logs estruturados e feature flags;
- baseline de build/test/performance e contratos legados;
- backlog com IDs de requisito do [PRODUCT_PRD.md](./PRODUCT_PRD.md).

### Entrada/saída

- **Entrada:** stakeholders, acesso ao ambiente e pilotos candidatos.
- **Saída:** ADRs críticos aprovados, restore comprovado, riscos com owners, fluxo piloto e permission matrix v1.

### Teste/observabilidade

Smoke atual automatizado; release marker; dashboard mínimo de 5xx/latência/readiness. Confirmar que dados de Team/Workflows são fixtures e não usar como telemetry real.

## 5. R1 — Plataforma segura e multiempresa

### Escopo

- `/api/v1`, contratos Zod/OpenAPI e typed client;
- identidade, `User`, `Organization`, `Membership`, invitation e roles;
- `RequestContext`, auth guards e policy checks;
- schema/backfill de `organizationId` nos dados atuais;
- app shell com organization switcher, guard e perfil real;
- audit log, outbox base, idempotency store e feature flag evaluation;
- logs/Sentry/metrics, staging e CI com integration tests.

### Reaproveitamento

Express/Zod/Prisma/PostgreSQL, npm workspaces, React/Vite/Router/Query, ThemeProvider, health/readiness, CI/Docker. Adaptar Axios, RootLayout e error handling. Não substituir stack de frontend nesta etapa.

### Dependências

R0; auth/database ADR; ambiente do provedor.

### Critérios de saída

1. login, convite, logout e troca de organização funcionam;
2. duas organizações não conseguem acessar IDs uma da outra em API/UI/jobs;
3. toda rota v1 nega por padrão e audita ação crítica;
4. backfill tem zero `organizationId` ausente e reconciliação assinada;
5. N/N-1 e rollback de app passam com schema expandido;
6. rotas legadas continuam disponíveis somente no contexto aprovado.

### Testes e riscos

Integration auth/JWT, role allow/deny, IDOR, cache/job tenant, invite expiry/replay e migration snapshot. Risco crítico: vazamento cross-tenant; gatilho de stop imediato.

## 6. R2 — CRM e visão 360

### Escopo

- Customer, source/category/tag/contact/note e consent;
- lista, busca/filtro/sort, create/edit/archive e Customer 360 inicial;
- timeline `ActivityEvent` e `AuditLog`;
- importação CSV/XLSX em job com mapping/preview/dedupe/report;
- empty/loading/error/permission e mobile operacional.

### Dependências

R1; regras de CPF/CNPJ, dedupe, soft delete e import aprovadas; storage/queue para import, ou lote piloto limitado com arquitetura compatível.

### Critérios de saída

- CRUD e import são tenant-safe, paginados e auditados;
- Customer 360 liga apenas dados implementados e sinaliza áreas vazias honestamente;
- import repetido com mesma key não duplica;
- lista de  volume piloto atende budget e teclado/mobile/a11y;
- pelo menos uma empresa piloto cria/importa clientes reais com consentimento apropriado.

### Métricas

`customer_created`, sucesso/erro/duração de import, duplicate rate, p95 da lista, erro por organização e ativação “primeiro cliente”.

## 7. R3 — Comercial e follow-up

### Escopo

- Pipeline/Stage defaults configuráveis e Opportunity;
- Kanban/lista, stage transitions, next action e stale alerts;
- proposal PDF/status; activity/timeline;
- tarefa sugerida ao mover; won/lost explícitos;
- dashboard temporário do funil apenas no módulo.

### Dependências

R2; storage para PDF; definição de stages, motivo de perda e regra de stale.

### Critérios de saída

- fluxo lead→ganho/perdido com histórico completo;
- drag tem fallback teclado/menu e conflito de versão tratado;
- valores/quantidades por estágio conciliam com lista;
- alertas explicam regra e deep link;
- ganho não cria cobrança/projeto sem confirmação.

### Testes/observabilidade

State transitions, concorrência, permission scopes, stage reorder, timezone de next action, E2E de follow-up; metricar tempo parado, conversão e erro de drag/rollback.

## 8. R4 — Tarefas, agenda e criação rápida

### Escopo

- Task v1, checklist, comentários, tags, owner e vínculos;
- views minhas/equipe/atrasadas, lista/Kanban;
- CalendarEvent dia/semana/mês, participantes e reminder in-app;
- Quick Create global/contextual para cliente, oportunidade, tarefa e evento;
- decidir/migrar tarefas legadas como interno/read-only/export.

### Dependências

R1/R2; integração com oportunidade após R3; recurrence policy se entrar (recomendação: não bloquear MVP por recorrência avançada).

### Critérios de saída

- tarefa criada em Customer/Opportunity aparece na timeline e views corretas;
- concluir/cancelar registra actor/time e respeita concorrência;
- timezone/DST de agenda testado;
- mobile permite criar, reatribuir e concluir;
- nenhuma tarefa legado é perdida ou convertida sem mapping aprovado.

### Métricas

tasks due/completed/overdue, time-to-complete, quick-create success e reminder job lag.

## 9. R5 — Financeiro operacional

### Escopo

- categorias/centros, receitas/despesas, parcelas;
- pagamentos/alocações/parcial/estorno e comprovante;
- cobrança assistida e CollectionAttempt;
- fluxo realizado/projetado e relatórios básicos;
- import financeiro com preview e reconciliação.

### Dependências

R1/R2; ADR financeiro; storage; idempotência/outbox/queue; RBAC final; revisão LGPD/segurança.

### Critérios de entrada adicionais

Threat model concluído; regras de moeda/arredondamento/competência/vencimento aprovadas; backup/restore e rollback financeiro exercitados.

### Critérios de saída

1. soma de parcelas = entrada e allocations = payment dentro das regras;
2. pagamento parcial, total e estorno têm auditoria imutável;
3. fluxo de caixa reconcilia com dados fonte;
4. idempotency replay não duplica pagamento;
5. role sem finance não lê valor por API, export, dashboard, busca ou IA;
6. piloto valida casos reais e assina reconciliação.

### Observabilidade/risco

Métricas de mismatch, duplicate prevention, reversal, overdue job e export. Divergência financeira é severidade crítica e pausa rollout.

## 10. R6 — Visão Geral e central de atenção

### Escopo

- read model/dashboard com operação, comercial, financeiro e agenda;
- AttentionCenter priorizado e explicável;
- KPIs com período/drill-down; activity recente;
- health empresarial por fatores, não nota opaca;
- partial failure e progressive loading.

### Dependências

R2–R5 em níveis suficientes; definição de prioridade e “perda evitada”.

### Critérios de saída

- proprietário responde às perguntas centrais em teste moderado < 5 minutos;
- todo item de atenção tem regra/fonte/CTA/responsável;
- KPIs conciliam com consultas de origem e timezone;
- dashboard mantém utilidade quando um widget falha;
- performance dentro do budget acordado no volume piloto.

## 11. R7 — Busca, notificações e command center

### Escopo

- busca PostgreSQL tenant-aware por clientes/oportunidades/tarefas/eventos/finanças autorizadas;
- command palette, páginas e comandos;
- inbox de notificações, prioridades, leitura/deep link e preferences básicas;
- Quick Create expande para cobrança/despesa/projeto/documento conforme módulos.

### Dependências

R1 e eventos dos módulos; permission-aware indexing; outbox/worker.

### Critérios de saída

- busca nunca revela entidade/campo sem permissão;
- relevância/latência aceitas pelos pilotos;
- notificações deduplicadas, rastreáveis e deep links válidos;
- `Ctrl/Cmd+K`, foco e screen reader testados;
- falha de index/notification não bloqueia transação de negócio.

## 12. R8 — Comunicação assistida e Ask Dash

### Escopo

- templates e geração/copiar/deep link WhatsApp;
- registro manual de tentativa e próximo contato;
- AI provider abstraction, policies, redaction, usage/budget;
- perguntas do PRD, resumos e fontes;
- action proposal inicialmente para criar tarefa, sempre confirmada;
- migrar/reter `AiInsight` atual conforme política.

### Dependências

R1, R2–R7 conforme tool; decisões de provedores/retention/consent; rate limit distribuído.

### Critérios de saída

1. modelo não acessa banco/provedor diretamente; todas tools passam por RBAC/tenant;
2. resposta cita registros/período e não afirma entrega de WhatsApp;
3. PII minimizada e logs/prompts seguem retenção;
4. ação proposta expira, exige confirmação e é idempotente;
5. provider timeout/429 degrada sem afetar módulos principais;
6. custo/latência/feedback por organização são observáveis.

### Riscos

Prompt injection, PII leakage, hallucination, custo e ação indevida. Mitigar com tool allowlist, content boundaries, output schemas, confirmation e red team.

## 13. R9 — Onboarding, piloto e commercial readiness

### Escopo

- onboarding de 6 etapas e checklist;
- demo data rotulada e removível;
- entitlement/plan foundation sem billing automático se ainda fora do escopo;
- help/support, termos/privacidade, export/delete workflows;
- hardening de performance, segurança, a11y, restore e suporte;
- rollout 3–5 pilotos e coleta de métricas.

### Critérios de saída

- onboarding sem ajuda técnica em teste/piloto;
- mais de um membro por organização e uso semanal;
- 3 empresas aceitam pagar e há evidência de valor evitado;
- SLO, security review, backup restore e incident drill aprovados;
- zero incidentes cross-tenant e riscos críticos abertos;
- suporte possui runbooks e trace IDs.

Se critérios falharem, iterar no fluxo central; não compensar adicionando módulos.

## 14. R10 — Pós-validação

Ordem orientada por evidência:

1. Projects de clientes e documentos avançados;
2. automações prontas e depois configuráveis;
3. relatórios/export PDF/CSV;
4. WhatsApp oficial/inbox compartilhada;
5. Google/Outlook Calendar;
6. propostas editáveis/aceite/assinatura;
7. integrações de pagamento/billing;
8. app/PWA/nativo se uso mobile justificar.

Cada item requer novo discovery, threat model e análise make/buy. Não herda automaticamente prioridade do PRD amplo.

## 15. Estratégia de testes acumulativa

| Marco | Novas suites obrigatórias |
| --- | --- |
| R1 | auth, tenant isolation, role matrix, migration N/N-1 |
| R2 | customer/import/dedupe/timeline e a11y lista/form |
| R3 | pipeline state/concurrency/Kanban E2E |
| R4 | due/timezone/reminder/mobile actions |
| R5 | property tests de dinheiro, idempotência, reconciliação/restore |
| R6 | read model parity, partial failure e performance |
| R7 | permission-aware search e notification dedupe |
| R8 | tool policy, prompt injection, redaction, provider failure/cost |
| R9 | jornada completa, load, security/a11y audit e disaster recovery |

CI atual é reaproveitado, mas cobertura atual não é suficiente: só há testes backend concentrados em health, Projects e AI validation (`backend/src/app.test.ts`).

## 16. Observabilidade e KPIs de entrega

### Técnicos

- deploy frequency, lead time, change failure, MTTR;
- p50/p95/p99 e 4xx/5xx por route/version/flag;
- query slow rate, DB pool, queue lag/retries/dead letters;
- frontend error/web vitals por rota;
- auth denies, cross-tenant probes e audit write failures;
- migration/backfill progress/reconciliation;
- IA latency/tokens/cost/error/action confirmation.

### Produto

- onboarding completion/drop-off;
- time-to-first-customer/opportunity/task/charge;
- WAU/organization, multi-user adoption;
- overdue resolved, follow-up completed, payment attempt;
- “attention item resolved” e evidência de perda evitada;
- AI useful rating e action acceptance.

Toda métrica precisa de definição, owner, source e proteção contra PII.

## 17. Definition of Ready

Uma história/fatia está pronta quando:

1. requisito e resultado de usuário têm ID/owner;
2. in/out of scope e dependências estão explícitos;
3. schema/API/erro/permission/tenancy estão definidos;
4. estados UI e acessibilidade foram desenhados;
5. migration/backfill/rollback e feature flag estão planejados;
6. eventos/métricas/logs e riscos estão definidos;
7. critérios de aceitação são testáveis e lacunas resolvidas ou marcadas.

## 18. Definition of Done global

1. código revisado e arquitetura modular respeitada;
2. lint, typecheck, unit, integration, contract, E2E e build pertinentes verdes;
3. tenant/RBAC negative tests e threat controls passam;
4. migration expand/contract é segura, repetível e reconciliada;
5. feature flag, canary, rollback e runbook foram testados;
6. logs/metrics/traces/alerts existem sem PII;
7. UI cobre loading/empty/error/permission/success, mobile, dark/light e WCAG AA;
8. performance dentro do budget medido;
9. documentação/API/changelog/support atualizados;
10. aceite de produto/dono de dados e nenhum risco crítico aberto;
11. nenhuma fixture ou capability futura é apresentada como real;
12. `main` continua implantável e smoke pós-deploy passa.

## 19. Registro de riscos e owners necessários

| Risco | Owner necessário | Gate |
| --- | --- | --- |
| isolamento de tenant | Tech lead + Security | R1/G3 |
| semântica/migração legado | Product + Data owner | R0/R1 |
| regras e reconciliação financeira | Product Finance + Backend | R5/G4 |
| LGPD/consent/retention | Legal/DPO + Product | R0/R8/R9 |
| IA/PII/ação indevida | AI lead + Security | R8/G3 |
| escopo excessivo | Product owner | todos |
| a11y/mobile operacional | Design + Frontend + QA | cada slice |
| SLO/backup/incident | Platform/Operations | R0/R9 |

Nomes dos owners são **VALIDAR**.

## 20. Próximas decisões concretas

1. aprovar a recomendação de manter React/Vite no MVP;
2. escolher auth provider e estratégia PostgreSQL/RLS;
3. classificar dados atuais e organização legado;
4. aprovar permission matrix e regras de CPF/CNPJ/financeiro;
5. confirmar 3–5 pilotos e fluxo de corte;
6. definir SLO, RPO/RTO, retention e budgets IA;
7. transformar R0/R1 em backlog estimável por capacidade real da equipe.
