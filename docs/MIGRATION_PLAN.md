# Plano de migração incremental

> Objetivo: transformar o dashboard atual no Business OS sem big bang, mantendo `main` implantável e os fluxos legados funcionais até que cada substituição tenha paridade, dados reconciliados e rollback exercitado.

## 1. Estratégia

Adotar **strangler + fatias verticais + expand/contract**:

- rotas atuais `/api/*` e telas existentes formam o legado temporário;
- novas capacidades entram em `/api/v1/*`, tenant-aware e protegidas;
- o shell pode expor módulos novos por feature flag/organização;
- migrations primeiro expandem schema; código faz backfill/dual operation; constraints e remoções vêm em releases posteriores;
- cada fatia inclui UI, API, dados, autorização, auditoria, testes, observabilidade e rollback;
- nenhuma fase depende de esconder falhas com fixtures.

## 2. Baseline e invariantes

### 2.1 Baseline AS-IS

- `main` aponta para `7bd2e05` no momento desta análise;
- deploy: Vercel frontend, Railway backend/PostgreSQL;
- CRUD funcional: Projects; analytics/health/database e AI Center;
- sem login/tenant; dados atuais são globais;
- duas migrations Prisma commitadas;
- testes cobrem health/readiness, CRUD de projeto e validações básicas de IA.

### 2.2 Invariantes durante a migração

1. Build, migration e smoke tests passam antes de rollout.
2. Nenhuma migration destrutiva no mesmo release que muda leitura.
3. Versão N e N-1 do app toleram o schema expandido.
4. Toda rota v1 exige auth e organização válida, exceto health/callbacks explícitos.
5. Dados novos nunca são gravados sem `organizationId`.
6. Feature flag pode desligar UI e caminho de escrita sem deploy emergencial.
7. Rollback de app não exige rollback destrutivo do banco.
8. Dados financeiros/auditoria não são apagados em rollback.
9. Mocks/fixtures não entram como fallback de produção.

## 3. Feature flags propostas

| Flag | Escopo | Função |
| --- | --- | --- |
| `platform.v1_api` | ambiente | registra novas rotas |
| `identity.auth_required` | organização/ambiente | exige sessão no novo shell |
| `tenancy.enforced` | organização | ativa scoping rigoroso e policies |
| `shell.business_os` | organização/usuário | troca navegação |
| `crm.enabled` | organização | clientes/timeline |
| `sales.enabled` | organização | oportunidades/funil |
| `tasks_v1.enabled` | organização | tarefas de negócio |
| `calendar.enabled` | organização | agenda |
| `finance.enabled` | organização | receitas/despesas/pagamentos |
| `dashboard_v1.enabled` | organização | visão geral Business OS |
| `search.enabled` | organização | command palette/busca |
| `ai_v1.enabled` | organização/plano | tools seguras |
| `legacy.projects_read_only` | ambiente/organização | congela escrita antiga |

Flags de segurança não podem permitir acesso indevido; desligar módulo remove capacidade, não bypassa autorização. Mudança de flag é auditada.

## 4. Preparação operacional — Fase 0

**Objetivo:** criar um ponto de partida mensurável sem alterar comportamento.

Entregas:

- registrar ADRs críticos listados em [TECHNICAL_ARCHITECTURE.md](./TECHNICAL_ARCHITECTURE.md);
- capturar inventário/contagens/checksums do banco atual e owner dos dados;
- confirmar backup e testar restauração em ambiente isolado;
- definir staging separado, seed não destrutivo e smoke automatizado;
- adicionar request ID, logs estruturados e release/version metadata;
- congelar contratos legados conhecidos e gerar baseline de performance;
- classificar dados atuais como demo, internos ou reais;
- runbook de incident/rollback e responsável pelo go/no-go.

Entrada: acesso aos ambientes/owners. Saída: restore testado, baseline documentado e ADRs 001–004 aprovados. Rollback: nenhum efeito funcional; reverter somente instrumentação defeituosa.

## 5. Fundação compatível — Fase 1

### 5.1 API e contratos

- registrar `/api/v1`, Problem Details, cursor pagination, idempotência e OpenAPI;
- criar `RequestContext`, middleware auth opcional em shadow e authorization policies;
- manter `/api/projects`, `/api/analytics` e `/api/ai` inalterados.

### 5.2 Schema expand

1. criar `organizations`, `memberships`, invitations/role tables;
2. criar uma organização `legacy-demo`/nome aprovado;
3. mapear usuários atuais a identidade interna/membership;
4. adicionar `organizationId` nullable em Project, Task e AiInsight;
5. backfill em lotes, com checkpoint, contagem e registros órfãos;
6. adicionar índices `CONCURRENTLY` quando necessário e constraints `NOT VALID`/validação posterior;
7. tornar `organizationId` obrigatório apenas depois de escrita compatível.

### 5.3 Estratégia de identidade

Login entra primeiro no novo shell. O legado pode continuar publicamente acessível apenas em ambiente demo temporário; produção com dados reais não pode manter endpoints públicos. Antes do piloto, todas as rotas de dados devem estar protegidas ou desativadas.

**Reconciliação:** total por tabela, zero `organizationId IS NULL`, relações pertencem à mesma org, amostra por ID. **Rollback:** voltar app mantendo colunas/tabelas novas; não remover schema. Desligar `identity.auth_required` apenas em ambiente demo aprovado.

## 6. Primeiro fluxo de valor — Fase 2

### 6.1 Clientes

- Customer, contatos, tags, notas e ActivityEvent;
- lista/busca/filtros, criação/edição e Customer 360 inicial;
- import wizard assíncrono com preview/dedupe/report;
- auditoria e testes cross-tenant.

### 6.2 Comercial

- pipeline/stages default por organização;
- oportunidade e stage history;
- Kanban, próxima ação e alertas de inatividade;
- upload de proposta PDF por adapter de storage.

### 6.3 Rollout

1. equipe interna;
2. organização demo;
3. uma empresa piloto sem import massivo;
4. pilotos restantes após métricas/erros estáveis.

Sem migração automática de `Project` atual para `Opportunity`: semânticas distintas. Rollback desliga `crm.enabled`/`sales.enabled`; dados permanecem, writes são suspensos por policy, export disponível.

## 7. Execução diária — Fase 3

- Task v1, checklist/comentários, minhas/equipe/atrasadas;
- CalendarEvent e views dia/semana/mês;
- Quick Create para cliente/oportunidade/tarefa/evento;
- notificações internas de tarefa, follow-up e evento;
- timeline do cliente recebe eventos desses módulos.

### Migração de `Task` legado

Tarefas atuais pertencem a projetos técnicos. Opções aprovadas por lote:

1. migrar como tarefas internas da organização legado, mantendo `legacyTaskId`;
2. manter somente na tela legado e arquivar depois;
3. exportar e não migrar.

Dual-write só é justificável se a mesma operação for editável nos dois modelos. Caso contrário, tornar legado read-only e usar import one-shot. Verificação: contagem, status mapping, owners, completedAt e checksum de títulos.

## 8. Financeiro e dashboard — Fase 4

### 8.1 Expand financeiro

- categories/cost centers, FinancialEntry, Installment, Payment/Allocation e CollectionAttempt;
- regras de dinheiro em serviço e constraints;
- permissions específicas, idempotência e auditoria;
- importação de planilha em staging com simulação antes de commit.

### 8.2 Dashboard v1

- queries/read models a partir de CRM, sales, tasks, calendar e finance;
- central de atenção com razões/fonte;
- KPIs de período explícito e atividade real;
- rollout separado de cada widget; dashboard não bloqueia se um bloco falhar.

**Entrada:** regras financeiras aprovadas e restore testado. **Saída:** pagamentos parciais/estorno, timezone e cálculos conciliados; E2E crítico verde. **Rollback:** desabilitar criação/pagamento, manter leitura/export; nunca apagar lançamentos.

## 9. Busca, comunicação e IA — Fase 5

### 9.1 Busca e notificações

- busca PostgreSQL tenant-aware por entidades autorizadas;
- command palette separa navegação/comandos/resultados;
- inbox, leitura e deep links; canais externos posteriores.

### 9.2 WhatsApp assistido

- templates, mensagem gerada, copiar/deep link e ContactAttempt;
- não registrar “enviado” sem callback/provider;
- consentimento/base legal e métricas definidas.

### 9.3 AI v1

- encapsular OpenAI atual em provider adapter;
- tools de leitura autorizada, redaction, fontes e budgets;
- geração de mensagem e resumos;
- action proposals com confirmação para criar tarefa, sem envio/exclusão/pagamento autônomo;
- migrar `AiInsight` apenas após classificação de PII/retenção.

Rollback por provider/feature flag; respostas existentes permanecem consultáveis conforme política, tools de mutação podem ser desligadas independentemente.

## 10. Expansão — Fase 6

- projetos comerciais simplificados e decisão sobre legado;
- documentos, relatórios/export jobs;
- automações prontas usando outbox/worker;
- onboarding completo, entitlements e planos;
- integrações oficiais somente após threat model e contrato do provider.

Módulos fora do MVP (WhatsApp inbox, assinatura, builder avançado, app nativo) não entram até critérios de validação do [PRODUCT_PRD.md](./PRODUCT_PRD.md).

## 11. Padrão expand/contract detalhado

Para qualquer mudança incompatível:

1. **Expand DB:** adicionar tabela/coluna nullable/default seguro/índice.
2. **Deploy writer compatível:** versão nova escreve antigo + novo, ou grava novo com adapter para leitor antigo.
3. **Backfill:** lotes pequenos, retry idempotente, telemetry/checkpoint.
4. **Verify:** contagens, invariantes, checksum e amostra de negócio.
5. **Switch read:** flag/canary para ler novo, comparar shadow responses quando possível.
6. **Stop old writes:** legado read-only após estabilidade.
7. **Enforce:** `NOT NULL`, FK/check depois de validar.
8. **Contract code:** remover caminhos antigos.
9. **Contract DB:** remover coluna/tabela somente em release posterior e após expirar rollback.

Nunca renomear coluna em uma etapa. Adicionar nova, sincronizar, cortar e remover depois.

## 12. Compatibilidade de API e frontend

- `/api` legado permanece congelado; `/api/v1` usa novo envelope/erros;
- frontend deployado antes/depois do backend deve tolerar endpoints disponíveis;
- capability endpoint/flags evita exibir módulo sem backend;
- contratos gerados são checados no CI;
- depreciação inclui `Deprecation`/`Sunset` headers e telemetria de consumidores;
- cache/CDN não armazena resposta privada sem key/headers corretos;
- schema de eventos/outbox tem `eventVersion` e upcasters quando necessário.

## 13. Migração de dados e qualidade

### 13.1 Pipeline

`extract → normalize → validate → stage → preview/reconcile → commit → audit/report`.

### 13.2 Regras

- scripts versionados, dry-run e idempotentes;
- nunca usar `backend/prisma/seed.ts` em produção: ele apaga tabelas;
- rejeitos ficam em relatório seguro, não parcialmente ignorados;
- mapeamento de status/owners documentado;
- timestamps preservados quando confiáveis; `migratedAt/source` separados;
- duplicidade exige regra aprovada, especialmente CPF/CNPJ/telefone;
- cada lote tem job ID e pode ser retomado sem duplicar.

### 13.3 Reconciliação

- contagem origem/destino/erros;
- soma financeira por moeda, status e mês;
- zero FKs órfãs ou cross-tenant;
- amostra de timeline/owners;
- checksum de campos normalizados;
- sign-off de produto/dono do dado.

## 14. Rollout e rollback

### 14.1 Ordem de rollout

Dev → CI migration test → staging → organização interna → demo → piloto 1 → 10/25/50/100% das organizações.

### 14.2 Gatilhos de pausa/rollback

- qualquer indício cross-tenant;
- erro em pagamento/alocação ou divergência financeira;
- aumento sustentado de 5xx/latência acima do SLO acordado;
- fila crescendo sem recuperação/dead letters;
- taxa de falha do fluxo crítico acima do threshold **VALIDAR**;
- perda de evento/auditoria/reconciliação.

### 14.3 Procedimento

1. pausar flag e writes do módulo;
2. preservar evidência, request/job IDs e janela do incidente;
3. reverter app para versão anterior compatível;
4. deixar schema expandido intacto;
5. reconciliar operações em trânsito/idempotency/outbox;
6. restaurar banco apenas em corrupção comprovada e com decisão de incident commander;
7. comunicar pilotos e publicar postmortem sem culpa.

## 15. Testes por fase

| Camada | Cobertura mínima |
| --- | --- |
| Unit | regras de domínio, dinheiro, status, permission policies |
| Repository | filtros obrigatórios de organização, constraints e concorrência |
| Integration | API+Postgres, outbox, idempotência, storage/queue adapters fake |
| Contract | OpenAPI/schema, backward compatibility e event versions |
| E2E | onboarding; cliente→oportunidade→tarefa→cobrança→pagamento |
| Security | IDOR/cross-tenant, role matrix, upload, injection, auth expiry |
| Migration | up em snapshot, backfill repetido, N/N-1, reconciliação |
| UI | dark/light, desktop/mobile, keyboard, axe, error/loading/empty |
| Resilience | provider timeout, duplicate webhook, worker retry/dead letter |

## 16. Observabilidade de migração

Dashboard de rollout deve exibir por release/flag/organização:

- erro e latência de rotas v1 vs legado;
- auth denied por motivo e tentativa cross-tenant;
- discrepância shadow read;
- progresso/erro/backlog de backfill;
- jobs, retries, dead letters e outbox age;
- import counts e reconciliação;
- erro frontend por rota;
- ações financeiras e IA com idempotência/custo.

Logs de migration têm IDs e contagens, nunca PII/conteúdo bruto.

## 17. Matriz de riscos

| Risco | Prob. | Impacto | Mitigação |
| --- | --- | --- | --- |
| vazamento entre organizações | média | crítico | context obrigatório, RLS/defesa, suíte cross-tenant, canary |
| semântica Project atual ≠ projeto comercial | alta | alto | não converter automaticamente; decisão e mapping explícitos |
| migração por e-mail falha | média | alto | IDs novos, relatório de órfãos, backfill verificado |
| divergência financeira | média | crítico | ledger simples, constraints, sums e dual control |
| big bang de UI/stack | média | alto | manter React/Vite e migrar por módulos |
| IA expõe PII ou executa ação | média | crítico | tools, redaction, policy, confirmação e audit |
| rate limit/job local falha ao escalar | alta | médio/alto | queue/limiter distribuído antes de scale-out |
| fixture confundida com real | alta | médio | rotular demo, remover páginas estáticas do shell novo |
| rollback depende de downgrade DB | média | alto | expand/contract e N/N-1 compatibility |
| escopo do MVP explode | alta | alto | fluxo vertical e gates de validação |

## 18. Critério global para desligar o legado

Todos devem ser verdadeiros:

1. 100% dos dados escolhidos migrados/reconciliados e sign-off;
2. nenhum tráfego legítimo no endpoint/tela legado por janela acordada;
3. paridade dos fluxos necessários, não de todas as telas antigas;
4. E2E, segurança tenant e restore drill verdes;
5. rollback da versão nova testado sem depender do legado;
6. documentação/suporte/pilotos notificados;
7. colunas/tabelas legadas mantidas por uma release adicional antes do contract.

## 19. Decisões pendentes

- classificação/destino dos dados atuais;
- provedor de auth, banco, queue e storage;
- janela de compatibilidade/depreciação;
- thresholds de rollout/SLO e incident owners;
- prazo de retenção e restore;
- estratégia RLS;
- regras financeiras e deduplicação de import;
- organizações piloto e ordem de exposição;
- capacidade da equipe e datas, que não foram fornecidas.
