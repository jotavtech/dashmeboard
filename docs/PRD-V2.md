# PRD — Dashmeboard V2

> ⚠️ **SUPERADO em 2026-07-16** pelo [`PRODUCT_PRD.md`](PRODUCT_PRD.md) (Dashmeboard Business OS).
> Este documento fica como registro histórico: as fases **F0 (higiene crítica)** e **F1 (API de tasks)**
> foram executadas e commitadas em `feat/v2-foundation`. As fases F2–F4 foram absorvidas/redefinidas
> pelo PRD mestre do Business OS.

**De demo acadêmica a cockpit pessoal de projetos.**

| | |
| --- | --- |
| Autor | jotavtech |
| Data | 2026-07-16 |
| Status | Superado — ver PRODUCT_PRD.md |
| Repositório | `jotavtech/dashmeboard` |

---

## 1. Contexto

O Dashmeboard V1 foi construído como dashboard acadêmico/demonstrativo: CRUD de projetos, analytics em PostgreSQL, AI Center com OpenAI, CI com GitHub Actions e SonarCloud, deploy em Vercel + Railway. A fundação técnica é sólida — lint, typecheck e build passam, a API de produção está no ar e o CRUD de projetos é real de ponta a ponta.

O V2 muda o propósito: **o Dashmeboard vira a ferramenta real de operação dos projetos do jotavtech** (~8 projetos ativos de clientes e pessoais simultâneos). Cada projeto precisa de tasks, prazos, links (repo/PRD/deploy) e notas — e o dashboard precisa responder "o que está atrasado e o que eu faço agora?".

### 1.1 Diagnóstico da auditoria (2026-07-16)

O que funciona hoje:

- ✅ CRUD de projetos completo (API + UI) com validação Zod
- ✅ Analytics reais (overview, activity, throughput, database snapshot)
- ✅ Health/readiness checks; API produção no ar com banco conectado
- ✅ Lint, typecheck e build verdes; CI configurado; SonarCloud configurado
- ✅ AI Center funcional (insights persistidos, degradação graciosa sem chave)

Pendências encontradas:

| # | Pendência | Severidade |
| --- | --- | --- |
| P1 | `npm test` quebra no Windows: `NODE_ENV=test vitest` não funciona no cmd.exe (falta `cross-env`) | Alta |
| P2 | Testes fazem `deleteMany()` em users/projects/tasks contra **qualquer** `DATABASE_URL` configurada, sem guarda de ambiente. O `.env` local aponta para banco remoto (`db.prisma.io`) — rodar os testes apaga dados reais | **Crítica** |
| P3 | Tasks existem no schema Prisma mas **não têm API nem UI** — só entram via seed. Impossível acompanhar projetos de verdade | **Crítica** |
| P4 | Páginas **Team**, **Workflows** e **Settings** são 100% estáticas (dados fictícios hardcoded) | Alta |
| P5 | Cobertura de testes: 55% geral; `analytics.service` 2,5%; `ai.service` 12,5% | Média |
| P6 | Sem autenticação — a API de produção aceita escrita de qualquer origem | Alta (débito aceito, ver §7) |
| P7 | Banco de produção vazio; seed atual gera apenas dados demo fictícios | Média |
| P8 | Divergência de infra: `DEPLOY.md` documenta Railway PostgreSQL, mas o `.env` usa Prisma Postgres (`db.prisma.io`). Confirmar qual banco a produção realmente usa | Média |

---

## 2. Visão do produto

> Um único lugar onde eu vejo todos os meus projetos, o estado de cada um, o que vence esta semana e o que está travado — e onde registrar uma task leva menos de dez segundos.

**Usuário:** único (jotavtech) — dev fullstack tocando múltiplos projetos de clientes em paralelo.

**Jobs to be done:**

1. *"Cadastrar cada projeto real com contexto completo"* — status, prioridade, prazo, cliente, repo, PRD, URL de deploy, branch ativa, notas.
2. *"Quebrar projetos em tasks e movê-las num kanban"* — TODO → DOING → REVIEW → DONE.
3. *"Abrir o dashboard e saber imediatamente o que está atrasado e o que vem agora"* — prazos vencidos e próximos em destaque.
4. *"Anotar decisões e contexto por projeto"* — notas em markdown que hoje vivem espalhadas em arquivos `docs/PRD-*.md` de cada repo.

**Fora de escopo do V2:** integração GitHub (Workflows com dados reais), multi-usuário/colaboração, auth completa, notificações push, mobile app.

---

## 3. Decisões de produto

| Decisão | Escolha | Consequência |
| --- | --- | --- |
| Direção visual | **Híbrido clean + identidade** | Base limpa e densa (referência: Linear); identidade atual (mono, accent rust `#FF3B1F`, labels técnicos) preservada em detalhes, não na estrutura |
| Escopo de acompanhamento | Tasks + kanban, prazos/milestones, notas e links | Sem integração GitHub no V2; página Workflows sai da navegação |
| Acesso | **Sem login por enquanto** | Débito consciente registrado em §7 com mitigações mínimas |

---

## 4. Escopo funcional

### E0 — Higiene crítica *(pré-requisito de tudo)*

- **E0.1** Adicionar `cross-env` e corrigir o script `test` para funcionar em Windows/Linux/CI.
- **E0.2** Guarda de segurança nos testes: `app.test.ts` (e qualquer suíte destrutiva) **recusa rodar** a menos que `DATABASE_URL` aponte para localhost/127.0.0.1 **ou** exista `TEST_DATABASE_URL` explícita. Falha com mensagem clara em qualquer outro caso.
- **E0.3** `backend/.env` local volta a apontar para o Postgres do Docker Compose (como documentado no README). A URL remota sai do arquivo local.
- **E0.4** Resolver P8: confirmar no painel do Railway qual `DATABASE_URL` a produção usa e alinhar `DEPLOY.md` com a realidade.

**Aceite:** `npm test` roda verde no Windows; rodar testes com URL remota no `.env` aborta com erro explicativo; `docker compose up -d postgres && npm test` funciona do zero.

### E1 — Domínio ampliado + API de tasks

Alterações de schema (nova migration):

```prisma
model Project {
  // ... campos existentes
  deadline   DateTime?
  client     String?
  repoUrl    String?
  deployUrl  String?
  docsUrl    String?          // PRD, Notion, etc.
  activeBranch String?
  notes      String?  @db.Text // markdown
  tags       String[] @default([])
}

model Task {
  // ... campos existentes
  dueDate  DateTime?
  order    Int       @default(0)   // posição na coluna do kanban
}
```

Nova API:

| Método | Endpoint | Descrição |
| --- | --- | --- |
| `GET` | `/projects/:id` | Passa a incluir tasks ordenadas por coluna/order |
| `POST` | `/projects/:id/tasks` | Cria task no projeto |
| `PATCH` | `/tasks/:id` | Atualiza título, descrição, status, dueDate, order (move no kanban) |
| `DELETE` | `/tasks/:id` | Remove task |
| `GET` | `/analytics/deadlines` | Projetos e tasks com prazo vencido ou nos próximos 7 dias |

Regras:

- Mover task para `DONE` seta `completedAt`; sair de `DONE` limpa.
- Validação Zod em todos os payloads, no padrão já existente.
- `/analytics/overview` e `/analytics/activity` passam a refletir tasks criadas pela API (já refletem, validar com testes).

**Aceite:** testes de integração cobrindo CRUD de tasks + movimentação de status + regra de `completedAt`; cobertura de `projects.service` e novas rotas ≥ 80%.

### E2 — UI de acompanhamento

- **E2.1 Página de detalhe do projeto** (`/projects/:id`) — a tela mais importante do V2:
  - Cabeçalho: título, status, prioridade, cliente, deadline (com badge de atraso), tags.
  - Painel de links: repo, deploy, PRD/docs, branch ativa — um clique abre.
  - **Kanban de tasks** com 4 colunas (TODO/DOING/REVIEW/DONE), drag-and-drop (`@dnd-kit`), criação inline (input no topo da coluna), edição rápida, due date por task.
  - Barra de progresso do projeto (tasks done / total).
  - **Notas em markdown** com edição e preview (`react-markdown` já é dependência).
- **E2.2 Dashboard renovado** — de vitrine para cockpit:
  - Bloco "Atenção" no topo: prazos vencidos e vencendo em 7 dias (projetos e tasks).
  - Grid de projetos ativos com progresso, deadline e última atividade.
  - Throughput e activity feed mantidos (já são reais).
  - Health panel mantido, porém compacto/secundário.
- **E2.3 Lista de projetos**: filtros existentes mantidos + filtro por tag e por cliente; clique na linha navega para o detalhe.
- **E2.4 Criação rápida**: command palette (`Ctrl+K`) para navegar e criar projeto/task de qualquer tela.

**Aceite:** fluxo completo sem tocar no banco manualmente — criar projeto com prazo e links → abrir detalhe → criar tasks → arrastar no kanban → dashboard mostra progresso e prazos corretamente.

### E3 — Renovação visual (design system híbrido)

Princípio: **estrutura clean, personalidade nos detalhes.** O que é decorativo hoje não pode custar legibilidade.

Mantém (identidade):

- Dark-first com light mode real (tema já existe, será refinado).
- Accent rust `#FF3B1F` como cor de ação/destaque única.
- Fonte mono para labels técnicos, números tabulares, badges de status.
- `PaneFrame` como assinatura visual — versão simplificada (menos ornamento, mesmos cantos/label).

Muda (usabilidade):

- **Tipografia de leitura**: sans humanista (Inter ou Geist) em caixa normal para títulos, descrições e formulários. Uppercase + letter-spacing largo fica restrito a micro-labels. Hoje praticamente todo texto é mono uppercase com tracking `0.32em` — ilegível em densidade.
- **Escala de espaçamento 4/8px consistente**; alvos de clique ≥ 40px; formulários com label visível (não placeholder).
- **Scanlines e grain viram opcional** (toggle em Settings, default off) — mantidos como easter egg da identidade, não como base.
- **Densidade de dados**: tabelas e cards com hierarquia clara (título > meta > detalhe), estados vazios com CTA ("nenhum projeto — criar primeiro"), skeletons de loading consistentes.
- **Navegação**: sidebar enxuta refletindo o novo escopo — Dashboard, Projects, Analytics, Database, AI Center, Settings. **Team e Workflows saem da navegação** (código removido; se quiser manter referência, mover para branch de arquivo).
- **Settings real**: só o que funciona — tema, scanlines/grain toggle, densidade. Preferências persistidas em `localStorage`. Linhas fake removidas.
- Acessibilidade: contraste AA no light mode, foco visível, kanban operável por teclado.

**Aceite:** navegação pelas telas principais sem texto ilegível em uppercase-tracking; light mode utilizável; zero dados fictícios renderizados em qualquer página.

### E4 — Dados reais + qualidade

- **E4.1 Seed pessoal**: `db:seed` continua gerando demo para dev; novo script `db:seed:real` importa de um JSON local **não commitado** (`backend/prisma/real-projects.json`, no `.gitignore`) com os projetos reais — Cynthia Makes, Playoff, Lume Odonto, Dinah Corrêa, Saúde Gileno, Kelvin, Atribuição V2, GeoService, etc.
- **E4.2 Cobertura**: `analytics.service` e rotas de tasks testadas; meta geral ≥ 75%, services críticos ≥ 80%. `ai.service` testado com client OpenAI mockado (nunca chamada paga em teste).
- **E4.3 CI**: pipeline atualizado (testes com Postgres de serviço + `TEST_DATABASE_URL`), badge no README.
- **E4.4 Docs**: README e DEPLOY.md atualizados para o novo escopo, endpoints e variáveis.

**Aceite:** produção populada com os projetos reais; CI verde; SonarCloud sem novos code smells críticos.

---

## 5. Fases de entrega

| Fase | Conteúdo | Estimativa | Critério de saída |
| --- | --- | --- | --- |
| **F0** | E0 completo (higiene crítica) | 0,5–1 dia | `npm test` verde no Windows; guarda de banco ativa; `.env` local seguro |
| **F1** | E1 (schema + API tasks + deadlines) | 1–2 dias | API de tasks testada; migration aplicada; cobertura ≥ 80% nas rotas novas |
| **F2** | E2 (detalhe do projeto, kanban, dashboard cockpit) | 2–4 dias | Fluxo de aceite E2 completo de ponta a ponta |
| **F3** | E3 (design system híbrido, navegação, limpeza de páginas fake) | 2–4 dias | Aceite E3; screenshots antes/depois |
| **F4** | E4 (seed real, cobertura, CI, docs, deploy) | 1–2 dias | Produção com dados reais; CI e Sonar verdes |

Ordem fixa: F0 antes de tudo (o risco de P2 é inaceitável). F1→F2 em sequência. F3 pode intercalar com F2 se conveniente.

---

## 6. Métricas de sucesso

1. Os ~8 projetos reais cadastrados em produção com links e prazos.
2. Uso semanal real: tasks criadas/movidas toda semana (verificável em `analytics_logs`/activity).
3. Registrar uma task nova leva < 10 segundos a partir de qualquer tela (via `Ctrl+K`).
4. Zero dados fictícios renderizados no app.
5. `npm test` verde em Windows e CI; cobertura geral ≥ 75%.

---

## 7. Riscos e débitos conscientes

| Risco | Decisão | Mitigação |
| --- | --- | --- |
| **API pública sem auth** (P6) | Aceito no V2 ("sem login por enquanto") | Rate limiting já existente mantido; CORS restrito ao domínio do frontend; URL não divulgada. **Revisar na primeira sprint pós-V2** — com dados reais de clientes no banco, o custo de um vândalo deixa de ser zero. Candidato: chave única via header, ~1 dia de esforço |
| Testes destrutivos contra banco errado (P2) | Corrigido em F0 | Guarda de ambiente + `.env` local apontando para Docker |
| Divergência de banco em produção (P8) | Investigar em F0 | Confirmar no Railway; alinhar docs; se produção usa `db.prisma.io`, decidir se migra para Railway PG ou atualiza DEPLOY.md |
| Drag-and-drop com bugs sutis | — | `@dnd-kit` (mantido e acessível); persistir `order` de forma idempotente; testes de API para movimentação |
| Escopo visual crescer sem fim | — | E3 limitado às telas do fluxo principal; refinamentos extras viram backlog pós-V2 |

## 8. Questões em aberto

1. **P8**: qual banco a produção do Railway realmente usa? (verificar variáveis no painel antes da F1 — as migrations novas precisam rodar no banco certo).
2. Página Team: removida no V2. Se um dia houver colaboradores/clientes com acesso, volta como "Pessoas" ligada ao model `User` real.
3. AI Center: mantido como está no V2 (funcional). Melhorias (insight semanal automático sobre prazos, plano de execução por projeto real) ficam para pós-V2.
