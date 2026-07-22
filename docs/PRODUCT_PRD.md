# Dashmeboard Business OS — Especificação de produto

> **Status:** proposta técnica derivada do PRD fornecido e do repositório em `main` (`7bd2e05`). Este documento não declara como existentes capacidades que ainda não foram implementadas.

## 1. Como ler esta documentação

| Marcador | Significado |
| --- | --- |
| **AS-IS** | Confirmado no repositório atual. |
| **TO-BE** | Decisão proposta para a evolução do produto. |
| **VALIDAR** | Lacuna que exige decisão de produto, negócio, jurídico ou tecnologia. |

Documentos relacionados: [arquitetura](./TECHNICAL_ARCHITECTURE.md), [dados](./DATA_MODEL.md), [design system](./DESIGN_SYSTEM.md), [migração](./MIGRATION_PLAN.md), [APIs](./API_CONTRACTS.md) e [roadmap](./IMPLEMENTATION_ROADMAP.md).

## 2. Resumo executivo

O Dashmeboard Business OS será uma plataforma SaaS multiempresa para pequenas empresas brasileiras, especialmente prestadoras de serviços que vendem e atendem pelo WhatsApp. A proposta central é impedir perdas de clientes, pagamentos e prazos, reunindo CRM, comercial, financeiro, tarefas, agenda e inteligência operacional em um único ambiente.

**Promessa:** em até cinco minutos após abrir o sistema, proprietário ou gestor deve entender quem requer atendimento, quais oportunidades estão paradas, quem está devendo e o que precisa ser feito hoje.

**North star qualitativa:** quantidade de perdas ou tarefas importantes que o Dashmeboard ajudou a evitar. O instrumento quantitativo dessa métrica precisa ser validado antes do piloto.

## 3. Diagnóstico do produto atual

### 3.1 AS-IS confirmado

O repositório implementa um dashboard acadêmico/operacional de projetos, não o Business OS descrito no PRD:

- o frontend é uma SPA React/Vite com rotas para Dashboard, Projects, Analytics, Team, Workflows, AI Center, Database e Settings (`frontend/src/App.tsx`);
- projetos possuem CRUD real com busca e filtros (`frontend/src/pages/Projects.tsx`, `backend/src/routes/projects.ts`);
- dashboard, analytics e database consultam PostgreSQL por API (`frontend/src/pages/Dashboard.tsx`, `backend/src/services/analytics.service.ts`);
- AI Center gera insights e planos de projeto usando diretamente OpenAI e persiste a saída (`backend/src/services/ai.service.ts`);
- Team e Workflows apresentam fixtures estáticas no frontend (`frontend/src/pages/Team.tsx`, `frontend/src/pages/Workflows.tsx`);
- Settings persiste somente tema local; não é configuração de organização (`frontend/src/pages/Settings.tsx`);
- não existem autenticação, organizações, memberships, isolamento por tenant, RBAC, clientes, oportunidades, agenda, contas a pagar/receber, notificações, busca global, documentos ou histórico de auditoria;
- o modelo atual possui apenas `User`, `Project`, `Task`, `AnalyticsLog` e `AiInsight` (`backend/prisma/schema.prisma`).

### 3.2 Implicação

O objetivo não é preencher páginas do menu atual com mocks. É migrar um produto demonstrativo para um SaaS transacional, mantendo o CRUD e a implantação atuais disponíveis enquanto capacidades multiempresa entram por fatias verticais.

## 4. Problema, público e posicionamento

### 4.1 Problemas prioritários

1. Clientes e follow-ups desaparecem em conversas de WhatsApp.
2. Cobranças e pagamentos são esquecidos em planilhas.
3. Tarefas, prazos e responsáveis não ficam claros.
4. Informações e documentos ficam dispersos, sem histórico confiável.
5. O proprietário se torna o único ponto de memória e coordenação.

### 4.2 Segmento inicial

Empresas brasileiras de serviços com 2 a 30 funcionários, alto uso de WhatsApp, processos manuais e pagamentos recorrentes ou parcelados. Segmentos de validação: clínica/consultório, cartório, advocacia, contabilidade, imobiliária, agência, assistência técnica, provedor, instalação, manutenção e consultoria.

### 4.3 Personas e resultados esperados

| Persona | Resultado principal | Restrições de acesso esperadas |
| --- | --- | --- |
| Proprietário/administrador | visão total, risco e previsibilidade | acesso administrativo e financeiro completo |
| Gestor | priorizar equipe, tarefas e resultados | escopo por módulos/equipes |
| Comercial | acompanhar clientes, funil e follow-ups | CRM; financeiro apenas quando concedido |
| Financeiro | registrar cobranças, receitas e despesas | dados financeiros; sem administração global por padrão |
| Atendente | registrar cliente, contato e retorno | contatos atribuídos e comunicação |
| Operacional/colaborador | executar tarefas e projetos | escopo próprio/equipe |
| Visualizador | consultar informações autorizadas | sem mutações/exportações por padrão |

### 4.4 Posicionamento

> O sistema operacional para pequenas empresas que vendem e atendem pelo WhatsApp.

Não deve parecer ERP contábil, painel genérico, planilha ornamentada, ferramenta de desenvolvedor ou coleção de módulos independentes.

## 5. Princípios e guardrails

| ID | Princípio | Critério prático |
| --- | --- | --- |
| P-01 | Simplicidade operacional | ações frequentes em poucos passos, com criação rápida contextual |
| P-02 | Clareza e ação | urgências mostram motivo, responsável, prazo e ação recomendada |
| P-03 | Valor imediato | onboarding conduz a uma primeira operação real |
| P-04 | Automação progressiva | começar por receitas prontas; construtor avançado fora do MVP |
| P-05 | IA como ferramenta | respostas fundamentadas em dados autorizados e ações confirmáveis |
| P-06 | Segurança e rastreabilidade | ações críticas registram organização, autor, data e mudança |
| P-07 | Personalização controlada | configurar etapas/categorias sem criar uma plataforma arbitrária |
| P-08 | Mobile operacional | criar, atualizar, cobrar e concluir; não apenas consultar |

Uma funcionalidade só deve ser priorizada se recuperar vendas, reduzir inadimplência, economizar tempo, evitar atrasos, melhorar atendimento/controle ou facilitar decisão.

## 6. Escopo do MVP

### 6.1 Obrigatório

| Capacidade | Requisito resumido | ID |
| --- | --- | --- |
| Identidade | login por e-mail, recuperação, convite e sessão segura | MVP-ID-01 |
| Multiempresa | organizações, seleção de organização e isolamento | MVP-ORG-01 |
| Equipe/RBAC | membership e permissões básicas por módulo/ação | MVP-RBAC-01 |
| Clientes | cadastro, busca, filtros, importação, visão 360 e timeline | MVP-CRM-01 |
| Comercial | oportunidades, funil, próxima ação, alertas e PDF de proposta | MVP-SALES-01 |
| Tarefas | lista/Kanban/minhas/atrasadas, responsável, prazo e checklist | MVP-TASK-01 |
| Agenda | dia/semana/mês, evento, cliente, responsáveis e lembrete | MVP-CAL-01 |
| Financeiro | receitas, despesas, parcelas, pagamento, vencimento e fluxo | MVP-FIN-01 |
| Visão geral | atenção priorizada, resumo operacional/financeiro e atividade | MVP-DASH-01 |
| Notificações | inbox interno com prioridade, leitura e deep link | MVP-NOTIF-01 |
| Histórico | timeline de negócio e auditoria de ações críticas | MVP-AUDIT-01 |
| Busca/criação | busca global e criação rápida dos agregados principais | MVP-CMD-01 |
| IA básica | resumo, priorização, perguntas e geração de mensagens | MVP-AI-01 |
| WhatsApp assistido | gerar/copiar mensagem, abrir conversa e registrar tentativa | MVP-WA-01 |
| Responsividade | operação real em mobile, teclado e acessibilidade AA | MVP-UX-01 |

### 6.2 Fora do primeiro MVP

WhatsApp oficial e inbox compartilhada, chatbot autônomo, contabilidade completa, folha, assinatura digital, editor de documentos, automação visual avançada, aplicativo nativo, marketplace, integração bancária e relatórios altamente configuráveis.

### 6.3 Corte recomendado para piloto

Para evitar um MVP horizontal e superficial, o piloto deve validar primeiro o fluxo ponta a ponta:

`cliente → oportunidade → próxima ação/tarefa → cobrança → pagamento/tentativa → dashboard/histórico`.

Agenda, notificações, busca, criação rápida e IA entram como suportes desse fluxo. Projetos, documentos avançados e automações customizadas evoluem depois dos primeiros sinais de retenção.

## 7. Requisitos funcionais por domínio

### 7.1 Visão geral

- **DASH-01:** exibir saudação e contagem de itens que requerem atenção.
- **DASH-02:** resumir clientes sem resposta, oportunidades paradas, tarefas atrasadas, cobranças vencidas, eventos do dia e projetos em risco.
- **DASH-03:** apresentar faturamento, recebido, pendente, despesas, saldo projetado e comparação mensal.
- **DASH-04:** oferecer funil resumido, agenda do dia, atividade recente e ações rápidas.
- **DASH-05:** gerar resumo inteligente com fatos rastreáveis; cada afirmação deve ligar aos registros de origem.
- **DASH-06:** indicador de saúde deve explicar fatores comercial, financeiro, operação e equipe; não usar nota opaca.

### 7.2 Clientes

- **CRM-01:** listar, buscar, filtrar, ordenar, segmentar, importar e exportar clientes.
- **CRM-02:** manter nome/razão, empresa, CPF/CNPJ, telefones, WhatsApp, e-mail, endereço, origem, categoria, status, responsável, notas, etiquetas e datas.
- **CRM-03:** visão 360 reúne oportunidades, tarefas, agenda, financeiro, documentos, projetos, mensagens e notas.
- **CRM-04:** timeline cronológica registra contatos e mudanças relevantes.
- **CRM-05:** importação deve pré-visualizar, mapear colunas, validar, deduplicar e produzir relatório de erros.

### 7.3 Comercial

- **SALES-01:** funil Kanban com etapas configuráveis por organização.
- **SALES-02:** oportunidade mantém cliente, valor, item, etapa, responsável, probabilidade, origem, fechamento previsto e próxima ação.
- **SALES-03:** alertar inatividade, follow-up atrasado, proposta vencida e alto valor parado.
- **SALES-04:** MVP aceita upload de proposta PDF e status; edição, rastreio e aceite ficam posteriores.
- **SALES-05:** movimentação de etapa gera histórico; ganho pode disparar criação assistida de cobrança/projeto.

### 7.4 Financeiro

- **FIN-01:** registrar receitas e despesas, categorias, vencimento, recorrência/parcelas, comprovante, centro de custo e responsável.
- **FIN-02:** suportar previsto, pendente, pago, vencido, cancelado e parcialmente pago.
- **FIN-03:** pagamento parcial nunca sobrescreve o valor original; registrar alocações e saldo.
- **FIN-04:** fluxo de caixa diário/semanal/mensal distingue realizado e projetado.
- **FIN-05:** cobrança assistida gera mensagem, copia/abre WhatsApp, registra tentativa e próxima cobrança.
- **FIN-06:** relatórios cobrem receita, despesa, inadimplência, ticket, cliente e serviço.

### 7.5 Tarefas e agenda

- **TASK-01:** tarefa contém título, descrição, responsável, cliente/projeto, prioridade, status, prazo, etiquetas, checklist, anexos e comentários.
- **TASK-02:** visões lista, Kanban, calendário, minhas, equipe, atrasadas e prioritárias.
- **TASK-03:** estados: pendente, em andamento, aguardando, concluída e cancelada.
- **CAL-01:** evento contém tipo, início/fim, timezone, responsável, cliente, participantes, localização, notas, lembretes e recorrência.
- **CAL-02:** visões diária, semanal e mensal; Google/Outlook ficam para evolução.

### 7.6 Projetos e documentos

- **PROJ-01:** projeto de serviço contém cliente, equipe, status, progresso, prazo, orçamento, etapas, tarefas, documentos e comentários.
- **PROJ-02:** manter escopo simplificado; não competir com gestão avançada.
- **DOC-01:** upload/download, busca, pasta/etiqueta, autorização e vínculo com cliente/projeto.
- **DOC-02:** validar tipos/tamanho, usar URL temporária e registrar acesso; versionamento fica posterior.

### 7.7 Comunicação

- **COM-01:** templates por finalidade (cobrança, confirmação, orçamento, follow-up, agradecimento, lembrete, reativação, pós-venda).
- **COM-02:** abrir WhatsApp com conteúdo preparado, sem afirmar envio automático.
- **COM-03:** tentativa manual registra canal, autor, horário, resultado e próximo retorno.
- **COM-04:** qualquer envio futuro exige consentimento, opt-out e regras da plataforma.

### 7.8 IA e automações

- **AI-01:** gerar mensagens, resumir cliente/oportunidade, sugerir prioridades, explicar indicadores e responder perguntas autorizadas.
- **AI-02:** backend expõe ferramentas controladas; o modelo não consulta banco diretamente.
- **AI-03:** excluir, enviar, alterar pagamento ou executar irreversível requer confirmação e nova autorização no servidor.
- **AI-04:** minimizar/redigir dados, registrar provedor/modelo/ferramentas e permitir rastrear fontes.
- **AUTO-01:** MVP oferece receitas prontas com gatilho, condição e ação; execução é assíncrona, idempotente e auditada.

### 7.9 Equipe, busca, notificações e onboarding

- **RBAC-01:** perfis iniciais: proprietário, administrador, gestor, comercial, financeiro, atendente, colaborador e visualizador.
- **RBAC-02:** ações: visualizar, criar, editar, excluir, exportar, aprovar e administrar.
- **SEARCH-01:** localizar clientes, oportunidades, tarefas, projetos, pagamentos, documentos, páginas e comandos.
- **NOTIF-01:** tipos do PRD com prioridade crítica/importante/informativa, leitura e link ao objeto.
- **ONB-01:** criar empresa, objetivo, importação/demo, equipe, fluxo e primeira ação real.
- **ONB-02:** checklist mede empresa, cliente, oportunidade, tarefa, cobrança e membro convidado.

## 8. Requisitos não funcionais

| ID | Requisito | Meta/controle proposto |
| --- | --- | --- |
| NFR-SEC-01 | isolamento | nenhum acesso sem membership ativo; `organizationId` aplicado no backend e banco |
| NFR-PERF-01 | interação | p95 de leitura comum < 1 s no servidor; página principal útil < 2 s em condição de teste definida |
| NFR-SCALE-01 | escala | paginação e índices desde o início; filas para IA/automações/importações |
| NFR-OBS-01 | observabilidade | request ID, logs estruturados, métricas, tracing de jobs e Sentry |
| NFR-A11Y-01 | acessibilidade | WCAG 2.2 AA, teclado, foco visível, toque >= 44 px, redução de movimento |
| NFR-LGPD-01 | privacidade | exportação, exclusão/anomização, retenção, consentimento e trilha de acesso |
| NFR-REL-01 | confiabilidade | backups testados, migração reversível, idempotência e restore runbook |
| NFR-COMPAT-01 | migração | app implantável e fluxo legado disponível durante cada fatia |

As condições de rede/dispositivo, volumes de teste, SLOs e RPO/RTO finais são **VALIDAR**.

## 9. Navegação e arquitetura de informação

Desktop proposto: Visão Geral, Clientes, Comercial, Financeiro, Tarefas, Agenda, Projetos, Comunicação, Documentos, Automações, Inteligência, Relatórios, Equipe e Configurações.

No mobile, a barra inferior deve priorizar Visão Geral, Clientes, Tarefas, Agenda e uma ação central de criação; demais módulos ficam em menu. Ask Dash e notificações permanecem acessíveis no cabeçalho. A hierarquia visual detalhada está em [DESIGN_SYSTEM.md](./DESIGN_SYSTEM.md).

## 10. Métricas e eventos

### 10.1 Produto

- ativação: organização criada, primeiro cliente/oportunidade/tarefa/cobrança e primeiro convite;
- engajamento: WAU por organização, registros atualizados, tarefas concluídas, cobranças tentadas, perguntas úteis à IA;
- retenção: organizações ativas D30/D90, frequência e amplitude de módulos;
- negócio: trial→pago, CAC, MRR, ARPA, churn e receita de implantação;
- resultado: oportunidades recuperadas, cobrança vencida acionada, tarefa crítica concluída antes do prazo e tempo poupado.

### 10.2 Taxonomia mínima de eventos

`onboarding_step_completed`, `customer_created`, `customer_import_completed`, `opportunity_stage_changed`, `follow_up_completed`, `task_completed`, `financial_entry_created`, `payment_recorded`, `collection_attempt_logged`, `attention_item_resolved`, `ai_answer_rated`.

Eventos analíticos não substituem `AuditLog`. O esquema e consentimento são definidos em [DATA_MODEL.md](./DATA_MODEL.md).

## 11. Monetização

As faixas do PRD são hipóteses, não preços aprovados:

- Essencial: R$ 99–149/mês, até 3 usuários;
- Profissional: R$ 249–399/mês, até 10 usuários, IA/automações/relatórios;
- Empresa: R$ 599–1.499/mês, permissões e integrações ampliadas;
- implantação: R$ 500–5.000 conforme migração/treinamento.

**VALIDAR:** limites de uso, franquia de IA/armazenamento, política de usuário adicional, impostos, trial, suspensão e grace period. Billing externo não deve controlar autorização sem um entitlement interno resiliente.

## 12. Critérios de validação do produto

Produto validado quando, simultaneamente:

1. cinco empresas usam semanalmente;
2. três aceitam pagar;
3. registram operações reais, com mais de um membro;
4. há evidência de cobrança/tarefa/oportunidade recuperada;
5. onboarding é concluído sem ajuda técnica;
6. há recorrência mensal e ausência de incidentes de isolamento.

Interface aprovada quando ações prioritárias são encontráveis, dashboard é acionável, mobile permite operar, temas têm alta legibilidade, identidade não parece template e estética não compromete performance.

## 13. Rastreabilidade ao PRD de origem

| Seções do PRD | Cobertura nesta especificação | Documentos técnicos |
| --- | --- | --- |
| 1–7, 40 | visão, problema, público, posicionamento e guardrails | este documento §§2–5 |
| 8–22, 24–28 | módulos, busca, notificações, onboarding e estados | §§6–9; [APIs](./API_CONTRACTS.md) |
| 23, 32, 39 | interface, mobile e acessibilidade | [DESIGN_SYSTEM.md](./DESIGN_SYSTEM.md) |
| 29–31 | arquitetura, tenancy, segurança, LGPD e performance | [arquitetura](./TECHNICAL_ARCHITECTURE.md), [dados](./DATA_MODEL.md) |
| 33–34 | monetização e lançamento | §§11–12; [roadmap](./IMPLEMENTATION_ROADMAP.md) |
| 35–36 | MVP e fases | §6; [migração](./MIGRATION_PLAN.md), [roadmap](./IMPLEMENTATION_ROADMAP.md) |
| 37–38 | métricas e sucesso | §§10–12 |

## 14. Lacunas e decisões pendentes

1. **Identidade:** Supabase Auth ou Auth.js/provedor externo; critérios: convites, MFA futuro, custo e portabilidade.
2. **Banco/hosting:** manter Railway PostgreSQL ou migrar para Supabase PostgreSQL; o PRD sugere Supabase, não o torna obrigatório.
3. **Tenancy comercial:** usuário pode pertencer a múltiplas organizações? A proposta técnica assume que sim.
4. **CPF/CNPJ:** obrigatoriedade, unicidade por organização, validação e criptografia/mascaramento.
5. **Financeiro:** regime de caixa/competência, juros/multa, conciliação, moeda e fuso por organização.
6. **Exclusão:** política de soft delete, retenção legal, anonimização e restauração.
7. **WhatsApp:** base legal, opt-in/opt-out e provedor futuro.
8. **IA:** provedores aprovados, região, retenção, budget por plano e conteúdo que pode sair do ambiente.
9. **Permissões:** matriz final por persona e necessidade de escopo por equipe/registro.
10. **Métricas:** fórmula auditável de “perda evitada” e baseline do piloto.
11. **SLO/DR:** disponibilidade, RPO/RTO e suporte por plano.
12. **Roadmap:** capacidade da equipe, prazo e orçamento não foram informados; o roadmap é sequencial, não uma promessa de datas.
