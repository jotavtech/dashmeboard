# Design system — Dashmeboard Business OS

> **Direção TO-BE:** “Business Command Center”: premium, tecnológica e acionável sem sacrificar leitura. O sistema atual fornece uma base visual reaproveitável, mas não é o design final do PRD.

## 1. Auditoria AS-IS

### 1.1 Ativos confirmados

- dark/light com preferência local (`frontend/src/contexts/ThemeContext.tsx`);
- tokens CSS semânticos de surface/foreground/accent (`frontend/src/styles/globals.css`);
- Tailwind com cores, tipografia, motion e shadows (`frontend/tailwind.config.ts`);
- shell responsivo, sidebar e mobile nav (`frontend/src/layouts/RootLayout.tsx`, `frontend/src/components/chrome/Sidebar.tsx`);
- primitives e patterns: `PaneFrame`, `TerminalLabel`, `StatusDot`, `StatGrid`, `PageShell`, `ToastProvider`;
- lazy routes, reduced-motion e feedback de CRUD;
- Projects transforma linhas em blocos no layout menor, mas não possui tabela mobile semântica completa.

### 1.2 Lacunas e conflitos

- paleta atual usa preto + vermelho ferrugem; o PRD propõe grafite + roxo elétrico e cores semânticas;
- linguagem “editorial cyber”, scanlines, grain, chrome text e labels monoespaçados dominam a interface e podem prejudicar legibilidade/credibilidade comercial;
- cantos majoritariamente retos conflitam com cards de 14–20 px sugeridos;
- header atual mostra relógio, “snd” e “cinema”, mas não busca, criação rápida, notificações ou Ask Dash;
- sidebar não tem seletor de empresa, favoritos ou plano;
- botões e ícones nem sempre alcançam alvo de toque de 44 px;
- strings misturam inglês, português e jargão técnico;
- não há componentes padronizados para tabela rica, Kanban, calendário, formulário, empty/error/skeleton, command palette ou painel de IA;
- contraste, teclado, screen reader e focus flow não possuem suíte verificável.

## 2. Princípios

1. **Ação antes de decoração:** cada destaque conduz a uma decisão ou ação.
2. **Uma primária por contexto:** demais ações usam hierarquia secundária/terciária.
3. **Urgência explicável:** cor + ícone + texto + motivo; nunca apenas cor.
4. **Densidade ajustável:** desktop suporta trabalho intenso; mobile preserva ação.
5. **Progressive disclosure:** resumo primeiro, detalhes e configuração sob demanda.
6. **Consistência sem monotonia:** módulos compartilham shell e patterns, não uma grade de cards idênticos.
7. **Motion funcional:** 150–300 ms, redução respeitada, sem atrasar input.
8. **Acessibilidade por construção:** WCAG 2.2 AA é critério de componente, não correção tardia.

## 3. Tokens de design propostos

Tokens devem ser CSS variables semânticas; valores abaixo são ponto de partida e precisam de teste de contraste em ambos os temas.

### 3.1 Cor

| Token | Dark | Light | Uso |
| --- | --- | --- | --- |
| `--bg-canvas` | `#07070A` | `#F6F7F9` | fundo da aplicação |
| `--bg-subtle` | `#0C0C11` | `#EEF0F4` | zonas/headers |
| `--bg-surface` | `#111118` | `#FFFFFF` | cards/painéis |
| `--bg-elevated` | `#17171F` | `#FFFFFF` | popovers/modais |
| `--text-primary` | `#F7F7FA` | `#17171C` | texto principal |
| `--text-secondary` | `#A8A8B3` | `#52525E` | apoio |
| `--text-tertiary` | `#747480` | `#71717C` | metadata; validar AA |
| `--border-subtle` | `#262631` | `#E1E3E8` | divisores |
| `--border-strong` | `#393947` | `#C7CBD3` | foco/ênfase |
| `--brand` | `#7C5CFF` | `#6D4AFF` | CTA/seleção |
| `--brand-hover` | `#8B6DFF` | `#5B3BE5` | hover |
| `--brand-soft` | `rgba(124,92,255,.14)` | `#F0ECFF` | fundos selecionados |
| `--info` | `#3B82F6` | `#2563EB` | informação |
| `--success` | `#22C55E` | `#15803D` | sucesso/pago |
| `--warning` | `#F59E0B` | `#B45309` | atenção/vencendo |
| `--danger` | `#EF4444` | `#DC2626` | erro/vencido/destrutivo |
| `--ai` | `#22D3EE` | `#0891B2` | identidade Ask Dash, junto de brand |

Cor auxiliar representa estado, nunca enfeite aleatório. `danger` não é cor primária de marca.

### 3.2 Tipografia

- família principal proposta: **Geist** ou **Inter**; confirmar licença e carregamento self-hosted;
- números usam `font-variant-numeric: tabular-nums` em tabelas/finanças;
- mono apenas para IDs, atalhos e metadata técnica curta;
- escala: 12, 14, 16, 18, 20, 24, 30, 36, 48 px; line-height 1.2–1.6 conforme função;
- títulos de página 30–36 px desktop, 24–30 px mobile; números KPI 32–48 px sem esmagar contexto;
- idioma padrão pt-BR; inglês apenas em nomes próprios/termos aprovados.

### 3.3 Espaçamento, forma e elevação

- base 4 px; escala `4, 8, 12, 16, 20, 24, 32, 40, 48, 64`;
- radius: input/button 10 px, card 16 px, modal 20 px, pill total;
- borda 1 px; sombra baixa no dark, suave no light;
- largura de conteúdo até 1440 px pode ser reaproveitada, mas tabelas operacionais podem usar viewport maior;
- alvo interativo mínimo 44×44 px; separação de ações destrutivas.

### 3.4 Motion

| Padrão | Duração | Curva |
| --- | --- | --- |
| hover/focus | 150 ms | ease-out |
| menu/popover | 180–220 ms | out-expo suave |
| painel/modal | 220–300 ms | out-expo |
| reorder Kanban | spring controlada | sem bounce excessivo |
| skeleton | sem shimmer agressivo | respeitar reduced motion |

Não animar números críticos de forma que dificulte leitura; evitar blur/filter em listas densas. `prefers-reduced-motion` atual deve ser preservado.

## 4. Arquitetura do sistema de componentes

```text
Tokens
  → Primitives (Button, Text, Icon, Surface, FocusRing)
    → Components (Input, Select, Dialog, Table, Toast, Tabs)
      → Patterns (QuickCreate, AttentionCenter, EntityTimeline, Kanban)
        → Domain compositions (Customer360, CashFlow, SalesPipeline)
```

Componentes não devem embutir permissão ou fetch. Patterns recebem estado/handlers; módulos orquestram queries, policies e rotas.

## 5. Inventário de componentes

### 5.1 Fundação obrigatória

- Button: primary, secondary, ghost, danger; loading sem alterar largura;
- IconButton 44 px com tooltip e accessible name;
- Link, Badge/Status, Avatar, Divider, Tooltip;
- Input, Textarea, Select/Combobox, Checkbox, Radio, Switch, Date/Time, Currency;
- Field com label, hint, required, error e `aria-describedby`;
- Dialog, Drawer, Popover, DropdownMenu, Tabs, Accordion;
- Toast + inline alert + confirm dialog;
- Skeleton, Spinner, Progress e optimistic state;
- DataTable, Pagination, FilterBar, SavedView **posterior ao MVP se necessário**;
- EmptyState, ErrorState, PermissionState e Offline/Retry state.

### 5.2 Patterns de produto

| Pattern | Responsabilidade |
| --- | --- |
| AppShell | organização, módulos, header, mobile navigation |
| GlobalSearch/CommandPalette | buscar entidades, navegar e executar comandos autorizados |
| QuickCreate | criação global/contextual em modal/drawer |
| AttentionCenter | prioridade, impacto, idade, responsável, CTA e dismiss/resolução |
| KPI/Card | valor, período, variação, definição e drill-down |
| EntityHeader | identidade, status, owner, ações e breadcrumbs |
| EntityTimeline | eventos legíveis, filtros e fonte |
| DataTable | sticky header, seleção, sort, filtros, paginação, densidade/colunas |
| Kanban | colunas, total/quantidade, drag keyboard-accessible, stale age |
| Calendar | dia/semana/mês e agenda list mobile |
| MoneySummary | recebido/pendente/vencido com moeda e período explícitos |
| AiPanel | sugestões, fontes, histórico, ações propostas e confirmação |
| ImportWizard | upload, mapping, preview, validação, progresso e relatório |

## 6. Shell e navegação

### 6.1 Desktop

Sidebar recolhível:

1. logo;
2. seletor de organização;
3. módulos por prioridade e permissão;
4. favoritos/recentes quando houver evidência de valor;
5. plano/limites de forma discreta;
6. perfil e logout real.

Header:

- busca/command palette (`Ctrl/Cmd + K`);
- botão de criação rápida;
- notificações;
- Ask Dash;
- ajuda e perfil.

Remover relógio, “snd”, “cinema” e status ornamental do shell principal. Health técnico fica em área administrativa/observabilidade, não na home do cliente.

### 6.2 Mobile

Barra inferior: Visão Geral, Clientes, criar, Tarefas, Agenda. Ask Dash/notificações no topo; módulo completo em menu. Safe areas respeitadas; CTA não cobre conteúdo. Cada fluxo crítico deve ser concluível com uma mão e teclado virtual.

## 7. Padrões por superfície

### 7.1 Visão Geral

Ordem recomendada:

1. saudação + resumo contextual + saúde explicável;
2. central de atenção priorizada;
3. quatro KPIs acionáveis;
4. agenda e próximas ações;
5. funil/fluxo de caixa;
6. atividade recente e resumo Ask Dash.

Mostrar skeleton por bloco, não travar página inteira. Cada KPI define período, moeda/unidade e link de drill-down.

### 7.2 Clientes

Desktop: tabela rica com busca, filtros e ações; alternância para cards apenas se validada. Mobile: cards com nome, status, owner, próximo passo e ações de contato. Customer 360 usa header + tabs/resumo e timeline; informação crítica fica no primeiro viewport.

### 7.3 Comercial/Kanban

- total e quantidade por coluna;
- card mostra cliente, valor, owner, próxima ação e tempo parado;
- drag com alternativa por menu/teclado e confirmação para ganho/perda;
- optimistic update com rollback e toast em erro;
- horizontal scroll visível e acessível, sem page overflow.

### 7.4 Financeiro

- moeda sempre formatada em locale e sem truncar sinal;
- status com texto e cor; vencido tem data/dias;
- registrar pagamento exige revisão de valor/data/método;
- ação destrutiva/cancelamento exige motivo;
- gráficos distinguem realizado/projetado por legenda, pattern e descrição.

### 7.5 Ask Dash

Painel lateral no desktop e tela/drawer amplo no mobile. Resposta inclui:

- resumo direto;
- período e escopo da consulta;
- fontes/links para registros;
- incerteza/ausência de dados;
- ações sugeridas separadas do texto;
- confirmação explícita antes de mutação.

Não representar texto gerado como fato sem fonte. Loading deve permitir cancelar; erro explica se dados foram preservados.

## 8. Estados de interface

### 8.1 Empty

Explicação curta, benefício, CTA primária e opção importar/demo quando pertinente. Ex.: “Você ainda não possui oportunidades. Crie a primeira ou importe clientes.” Não mostrar gráfico zerado como experiência inicial.

### 8.2 Loading

- skeleton com dimensões estáveis;
- optimistic update apenas reversível;
- progresso real para import/export/upload/IA;
- `aria-busy` e anúncio não intrusivo.

### 8.3 Error

Explicar o que falhou, o que foi preservado e como tentar novamente. Incluir trace ID copiável apenas em detalhes de suporte. Não expor stack/SQL/provider secret.

### 8.4 Permission e destructive

- permissão negada distingue falta de acesso de recurso inexistente sem vazar existência cross-tenant;
- botão sem permissão pode ser ocultado; deep link recebe estado seguro;
- exclusão/cancelamento descreve impacto e restauração; digitação de confirmação apenas para alto impacto.

## 9. Acessibilidade

Critérios mínimos por componente/slice:

- contraste AA normal/large em dark e light;
- ordem DOM e heading hierarchy coerentes;
- navegação completa por teclado, foco visível e retorno de foco em dialog;
- skip link e landmarks;
- labels programáticas, descrição de erro e `aria-live` para toast/status;
- ícones nunca são único sinal;
- drag-and-drop possui alternativa por teclado/menu;
- charts têm resumo textual e tabela acessível;
- alvo >= 44 px e zoom 200% sem perda;
- reduced motion e sem flashes;
- testes com axe + teclado + leitor de tela nos fluxos críticos.

## 10. Conteúdo e localização

- pt-BR por padrão; mensagens curtas, humanas e orientadas a ação;
- “cliente”, “cobrança”, “vencimento” e “responsável” devem manter terminologia;
- datas/timezone/moeda vêm da organização, mas armazenam UTC/ISO;
- números financeiros nunca usam abreviação ambígua em confirmação;
- texto de IA é marcado como gerado e pode receber feedback;
- estados e enums da API são traduzidos no frontend, não exibidos em uppercase técnico.

## 11. Matriz de reaproveitamento visual

| Componente atual | Decisão | Mudança necessária |
| --- | --- | --- |
| `ThemeContext` | reaproveitar | preferência por usuário/organização futura, evitar flash |
| CSS semantic variables | reaproveitar | renomear/ampliar tokens e paleta violet |
| Tailwind config | adaptar | tipografia, radius, spacing, shadows e estados |
| `RootLayout` | adaptar | guards, org switcher, command/quick create |
| `Sidebar/MobileNav` | adaptar | IA de navegação do PRD e permissões |
| `ToastProvider` | adaptar | `aria-live`, focus/action e fila previsível |
| `PaneFrame` | adaptar | radius/surface e remover cantos técnicos no core |
| `PageShell` | reaproveitar/adaptar | breadcrumb, responsive actions e título mais contido |
| `StatGrid` | adaptar | definição/período/drill-down e layout menos repetitivo |
| `StatusDot` | adaptar | sempre texto/ícone e sem ping por padrão |
| `Grain/Scanlines` | restringir | somente marketing/demo especial; off no app core |
| `ChromeText/Magnetic` | restringir/substituir | não usar em texto operacional/CTA frequente |
| Projects modal/table | extrair patterns | form, DataTable, confirm e mobile/a11y |
| motion utilities | reaproveitar | limitar a 150–300 ms e evitar blur em listas |

## 12. Governança

- tokens e componentes vivem em `frontend/src/design-system` e documentação Storybook **VALIDAR**;
- nenhuma cor/spacing arbitrário em novos módulos sem revisão;
- componentes têm estados default/hover/focus/disabled/loading/error/dark/light;
- alterações breaking usam changelog e codemod quando necessário;
- ownership: Design + Frontend; acessibilidade e conteúdo fazem parte da revisão;
- critérios visuais devem ser verificados em desktop e mobile, dark/light, teclado e reduced motion.

## 13. Definition of Done de UI

1. corresponde ao requisito e aos estados definidos;
2. utiliza tokens/components existentes ou documenta extensão;
3. cobre loading/empty/error/permission/success;
4. responsive em larguras acordadas, sem overflow horizontal de página;
5. teclado, foco, screen reader básico, contraste e alvo de toque validados;
6. dark/light e pt-BR revisados;
7. testes unitários/componentes/E2E proporcionais ao risco;
8. telemetria de evento/erro sem PII;
9. performance dentro do budget e reduced motion respeitado;
10. nenhuma fixture é mostrada como dado real.

## 14. Itens a validar

- fonte final e estratégia de loading;
- paleta exata por contraste e reconhecimento de marca;
- densidade default e necessidade de modo compacto;
- comportamento de mobile Kanban/calendário;
- editor/visualização de dashboards por plano;
- Storybook e ferramenta de visual regression;
- nomenclatura “Dash AI” vs “Ask Dash”; documentos usam **Ask Dash** como nome de trabalho;
- favoritos na sidebar e indicador de plano no MVP;
- limites aceitáveis de glass/glow na aplicação operacional.
