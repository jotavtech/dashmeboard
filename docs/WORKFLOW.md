# Workflow de colaboração — Sol × Fable

Como os dois modelos trabalham neste repositório sem conflito. O dono do produto (jotavtech)
aprova as decisões entre cada etapa.

## Papéis

| Papel | Modelo | Responsabilidades |
| --- | --- | --- |
| Tech lead / arquiteto / revisor | **GPT-5.6 Sol** | Arquitetura técnica, modelo de dados, multi-tenancy, módulos, contratos de API, permissões e segurança, plano de migração, critérios de aceitação, revisão de diffs |
| Engenheiro de implementação / visual | **Claude Fable 5** | Páginas, componentes, design system, responsividade, animações, grandes blocos de código, testes, iteração visual |

## Fluxo

```text
PRD mestre (PRODUCT_PRD.md)
   ↓
Sol analisa e cria a especificação técnica
   ↓
jotavtech aprova as decisões principais
   ↓
Fable implementa UMA etapa (branch própria, escopo fechado)
   ↓
Sol revisa o diff
   ↓
Fable corrige apenas os pontos aprovados
   ↓
Testes e validação visual
   ↓
Merge
```

Os modelos não conversam diretamente. O repositório, a especificação e os relatórios de
revisão são o canal de comunicação.

## Documentos

```text
/docs
├── PRODUCT_PRD.md              ← verdade oficial do produto (não editar sem decisão do dono)
├── BRIEF_SOL.md                ← Documento 1: brief técnico para o Sol
├── TECHNICAL_ARCHITECTURE.md   ← produzido pelo Sol
├── DATA_MODEL.md               ← produzido pelo Sol
├── DESIGN_SYSTEM.md            ← produzido pelo Sol (tokens/regras) + Fable (implementação)
├── MIGRATION_PLAN.md           ← produzido pelo Sol
├── API_CONTRACTS.md            ← produzido pelo Sol
├── IMPLEMENTATION_ROADMAP.md   ← produzido pelo Sol
└── PRD-V2.md                   ← histórico (superado)
```

## Branches

```text
main
└── develop
    ├── feat/v2-foundation      ← F0+F1 do PRD antigo (hardening + API de tasks) — aguarda revisão do Sol
    ├── feat/design-system-v2
    ├── feat/app-shell
    ├── feat/dashboard-overview
    ├── feat/crm
    ├── feat/finance
    └── feat/tasks
```

## Regras

1. Um modelo por branch. Nunca os dois editando o mesmo código ao mesmo tempo.
2. Cada tarefa de implementação tem: escopo limitado, arquivos permitidos, arquivos proibidos,
   critérios de aceitação, testes exigidos e definição de pronto.
3. Decisões de arquitetura registradas nos docs — nunca só no chat.
4. `PRODUCT_PRD.md` só muda por decisão explícita do dono do produto.
5. Os testes do backend são destrutivos e protegidos por guarda de ambiente
   (`backend/src/test/guard.ts`): rodam apenas contra banco local ou `TEST_DATABASE_URL`.
