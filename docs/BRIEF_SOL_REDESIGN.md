# Documento 2 — Brief de redesign e expansão comercial para o Sol

**Missão desta fase:** revisar as decisões de produto e design abaixo, formalizá-las como
emendas ao [`PRODUCT_PRD.md`](PRODUCT_PRD.md) e atualizações do
[`DESIGN_SYSTEM.md`](DESIGN_SYSTEM.md) / [`IMPLEMENTATION_ROADMAP.md`](IMPLEMENTATION_ROADMAP.md),
e registrar os ADRs pendentes. **Não implementar ainda.** A implementação segue o fluxo do
[`WORKFLOW.md`](WORKFLOW.md): uma fatia por branch, revisada antes do merge.

Escrito em 2026-07-22, após análise do repositório e da branch `feat/business-os-mvp`.

---

## 1. Contexto

O objetivo desta rodada é tornar o produto **menos nichado** sem perder o foco de go-to-market,
e priorizar funcionalidades que aumentem a viabilidade comercial. Diagnóstico:

1. A UI atual (herdada do cockpit pessoal) é nichada em desenvolvedores: páginas chamadas
   *Database*, *Workflows*, *AI Center*, tipografia mono uppercase, estética de terminal.
   O §5.3 do PRD já proíbe "ferramenta exclusiva para programadores" — a UI atual viola isso.
2. O posicionamento ("PME BR WhatsApp-first, prestadores de serviço") é nicho de marketing —
   é força, não fraqueza. O que não pode ser nichado é o **core** do produto.

Direção: **core horizontal, go-to-market vertical.** O produto é um só; o nicho entra por
template de segmento e por landing page, não por hardcode.

## 2. Estado do repositório (as-is desta data)

- `feat/business-os-mvp` contém: schema Prisma multi-tenant completo (35 models/enums,
  `Decimal(19,4)` para dinheiro, índices compostos por `organizationId`) e dependências de
  auth (`bcryptjs`, `jsonwebtoken`) e UI (`@dnd-kit`, Inter variable).
- **Pendente para a R1:** migration + backfill de `organizationId` (o client gerado do schema
  novo quebra os testes atuais até a migration existir — sequência definida no
  [`MIGRATION_PLAN.md`](MIGRATION_PLAN.md)).
- `npm audit` limpo nas duas branches ativas (`form-data`/`esbuild` resolvidos); quality gate
  do CI desbloqueado.

## 3. Decisões de design para formalizar

### 3.1 Navegação por resultado, não por módulo técnico

Sidebar proposta (ordem fixa):

`Hoje · Clientes · Vendas · Financeiro · Agenda · Tarefas · Relatórios`

- **Hoje** é a home: o que precisa de atenção (atrasos, cobranças vencendo, follow-ups,
  compromissos do dia), não um dashboard de gráficos. Realiza o §3 do PRD ("compreender a
  situação da empresa em menos de cinco minutos").
- *Database/Workflows/AI Center* não sobrevivem como conceitos de navegação. IA (Ask Dash)
  vira superfície transversal (⌘K / botão flutuante), não página.

### 3.2 Tema claro como padrão do produto — proposta de emenda ao §23.4

O PRD define o tema escuro como principal. Proposta: **claro como default do produto,
escuro para marketing/demo e preferência do usuário.**

Racional: o usuário-alvo (dono de clínica, cartório, contabilidade) trabalha de dia em
escritório; escuro como default lê como ferramenta de programador — exatamente o que o
§5.3 proíbe. O tema escuro permanece como asset de demonstração (§23.4 "elemento de
marketing" continua válido). Decisão final é do dono do produto; registrar como emenda
aceita ou rejeitada.

### 3.3 Templates de segmento (realiza o §6.7 — personalização controlada)

No onboarding, a escolha do segmento configura:

- pipeline comercial pré-montado (estágios típicos do segmento);
- categorias financeiras iniciais;
- tipos de evento da agenda;
- **labels renomeáveis** (clínica vê "Pacientes", imobiliária vê "Leads"; entidade interna
  continua `Customer`).

Requisito técnico derivado: dicionário de labels por organização (tabela ou JSON em
`Organization.settings`) e strings do frontend centralizadas (i18n desde o shell novo —
custo marginal agora, opção de mercado não-BR depois).

### 3.4 Comunicação modelada como inbox multicanal

WhatsApp é o primeiro canal, não a identidade do módulo. Modelar `Interaction`/`Channel`
(WhatsApp | e-mail | telefone | presencial) desde o MVP manual (§16.1), para que a evolução
(§16.2) seja plugar canais, não refazer o módulo.

## 4. ADR pendente — autenticação (bloqueia código da R1)

As dependências instaladas (`jsonwebtoken` + `bcryptjs`) permitem dois desenhos. Registrar
ADR antes de qualquer código de auth:

- **Proposta A (recomendada):** sessão via cookie `httpOnly` + `SameSite` + refresh rotation;
  access token de vida curta só em memória. Revogação simples, imune a XSS-token-theft.
- **Proposta B:** JWT no client (localStorage). Rejeitar explicitamente se for o caso —
  é o padrão que o SonarCloud e o checklist de segurança (§30) vão apontar.

Incluir no ADR: hashing (bcrypt vs argon2), expiração, multi-org por usuário (o schema já
suporta via `Membership`), e fluxo de convite (`Invitation.tokenHash` já existe).

## 5. Funcionalidades novas para priorização no roadmap

Candidatas com viés de receita/retenção, ausentes ou fracas no PRD atual. Sugerida a fase
de entrada de cada uma (numeração do [`IMPLEMENTATION_ROADMAP.md`](IMPLEMENTATION_ROADMAP.md)):

| # | Funcionalidade | Valor | Fase sugerida |
|---|---|---|---|
| 1 | **Pix + link de pagamento** na cobrança (gateway: Asaas/Mercado Pago/Stripe) | Fecha o loop cobrança→recebimento; habilita monetização por take rate além da assinatura | R5 (financeiro) |
| 2 | **Portal do cliente final** (2ª via, aprovar proposta, agendar) | Cada fatura vira link com a marca do produto → aquisição viral | R10, mas decidir cedo por afetar o modelo de `Customer` |
| 3 | **Importação assistida por IA** (planilha → mapeamento de colunas) | Remove a maior barreira de adoção do público planilha | R2 (junto do CRM) |
| 4 | **Receitas de automação de 1 clique** (catálogo pronto, sem builder) | Entrega o §19 sem a complexidade do §19.2 | R8 |
| 5 | **Resumo semanal por e-mail/WhatsApp** | Engajamento recorrente do dono | R7 (notificações) |
| 6 | **Ask Dash com ações** (criar tarefa, registrar pagamento — sempre com confirmação) | IA que executa vira hábito; IA que só responde vira demo | R8 |
| 7 | **Aceite digital de propostas** com trilha de auditoria (`AuditLog` já existe) | Encurta ciclo comercial; LGPD-friendly | R3+ |
| 8 | **NFS-e via API** (ex.: Focus NFe) | Dor real de PME BR; decide contratos maiores | R10 |

Critério de corte: nada disso entra antes do fluxo mínimo validável
(`onboarding → cliente → oportunidade → tarefa → cobrança → pagamento → visão geral`).

## 6. Saídas esperadas do Sol

1. Emendas ao PRD (§23.4 tema; §8/§23.8 navegação; §16 inbox; §22 templates de segmento).
2. `DESIGN_SYSTEM.md` atualizado: sidebar/rotas novas, tokens do tema claro como base,
   diretrizes de labels renomeáveis.
3. ADR de autenticação (item 4) — bloqueia o início do código da R1.
4. Roadmap com as funcionalidades do item 5 posicionadas (aceitas, adiadas ou rejeitadas,
   com justificativa).
5. Spec da fatia imediata: migration + backfill do schema multi-tenant já commitado em
   `feat/business-os-mvp`, conforme o gate G2 do roadmap.
