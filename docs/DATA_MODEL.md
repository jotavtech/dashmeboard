# Modelo de dados — Dashmeboard Business OS

> **Status:** modelo lógico **TO-BE**. O esquema físico final depende de ADRs sobre identidade, RLS, finanças, retenção e storage. Ver [arquitetura](./TECHNICAL_ARCHITECTURE.md) e [migração](./MIGRATION_PLAN.md).

## 1. Estado atual

`backend/prisma/schema.prisma` contém:

- `User`: e-mail único global e role técnico (`FOUNDER`, `ENGINEER`, `DESIGNER`, `GUEST`);
- `Project`: título, descrição, status, prioridade e owner referenciado por e-mail;
- `Task`: tarefa mínima vinculada ao projeto e assignee por e-mail;
- `AnalyticsLog`: JSON genérico, atualmente sem uso evidente nas rotas inspecionadas;
- `AiInsight`: prompt, output, context e model.

Não há `organizationId`, autenticação, memberships, clientes, oportunidades, agenda, financeiro, documentos, notificações ou auditoria. Portanto, o modelo atual não é seguro para multiempresa e não deve apenas receber campos opcionais sem políticas de acesso.

## 2. Convenções propostas

| Tema | Convenção |
| --- | --- |
| Identificador | UUID gerado no servidor/banco; nunca usar e-mail como FK |
| Tenancy | todo registro de negócio tem `organizationId NOT NULL` |
| Tempo | `timestamptz` UTC; timezone IANA na organização/evento |
| Dinheiro | `numeric(19,4)` + `currency char(3)`; arredondamento explícito |
| Nomes | Prisma em camelCase; tabelas/colunas SQL em snake_case via `@@map/@map` |
| Concorrência | `version int` ou `updatedAt` como precondition em agregados disputados |
| Exclusão | `deletedAt/deletedBy` apenas onde restauração é necessária; auditoria append-only |
| Origem | `source`, `externalId` e integração quando dado veio de import/provider |
| Metadata | JSON somente para extensão controlada; não substituir campos consultáveis |
| PII | mascarar em logs; criptografia de campo para dados definidos em threat model |

## 3. Regras de tenancy

1. O usuário global não é dono dos dados; `Membership` liga usuário e organização.
2. Unicidades de negócio incluem `organizationId`: por exemplo, CPF/CNPJ normalizado pode ser único por organização, não global.
3. Relações tenant-aware devem impedir vínculo cruzado. Preferir FK composta `(organization_id, entity_id)` quando viável ou validar na mesma transação com testes de integração.
4. Toda query parte de `organizationId`; `findUnique({id})` isolado é proibido nos repositories de negócio.
5. Jobs, cache keys, storage paths, search index e outbox carregam `organizationId`.
6. RLS, se adotada, usa `SET LOCAL app.current_organization_id` dentro da transação; bypass fica restrito a migrations e jobs administrativos auditados.
7. Entidades globais permitidas: `User`, catálogo de permissões e planos; nenhum dado operacional global.

## 4. Visão de relacionamentos

```text
User ──< Membership >── Organization ──< OrganizationSettings
                              │
                              ├──< Customer ──< Opportunity ──> PipelineStage
                              │      ├──< ContactAttempt
                              │      ├──< FinancialEntry ──< Installment ──< PaymentAllocation >── Payment
                              │      ├──< Task
                              │      ├──< CalendarEvent
                              │      ├──< Project
                              │      └──< Document
                              │
                              ├──< Notification
                              ├──< ActivityEvent
                              ├──< AuditLog
                              ├──< AiConversation ──< AiMessage/AiToolCall
                              ├──< AutomationRecipe ──< AutomationRun
                              ├──< IntegrationConnection ──< WebhookEvent
                              └──< OutboxEvent
```

## 5. Identidade, organização e autorização

### 5.1 `User`

| Campo | Tipo | Regra |
| --- | --- | --- |
| id | uuid | PK |
| authSubject | string | único por issuer; identidade externa |
| authIssuer | string | compõe unicidade com subject |
| email | citext | e-mail atual; único conforme estratégia do provedor |
| name, avatarUrl | string? | perfil |
| status | enum | `ACTIVE`, `BLOCKED`, `DELETED` |
| lastLoginAt | timestamptz? | operacional |
| createdAt, updatedAt | timestamptz | auditoria básica |

### 5.2 `Organization`

`id`, `name`, `slug`, `legalName?`, `taxIdEncrypted?`, `segment?`, `sizeRange?`, `phone?`, `countryCode`, `currency`, `locale`, `timezone`, `status`, `planCode`, `createdAt`, `updatedAt`, `deletedAt?`.

**VALIDAR:** armazenamento e busca de CNPJ/CPF da própria organização, plano/faturamento e ciclo de suspensão.

### 5.3 `Membership`

`id`, `organizationId`, `userId`, `roleKey`, `status` (`INVITED/ACTIVE/SUSPENDED/REVOKED`), `invitedById?`, `invitedAt`, `acceptedAt?`, `lastAccessAt?`, timestamps.

Constraints: único `(organizationId,userId)`; role só é efetiva quando membership ativa.

### 5.4 Permissões

- `RoleDefinition`: role inicial ou customizada por organização, `key`, `name`, `isSystem`.
- `RolePermission`: `(roleId, permissionKey, scope)`; scope inicial `OWN`, `TEAM`, `ALL` quando aplicável.
- `MembershipPermissionOverride`: allow/deny excepcional, opcional e auditado.
- `Invitation`: hash do token, e-mail, role, expiração, status e idempotência.

Catálogo inicial inclui `customers.*`, `sales.*`, `tasks.*`, `calendar.*`, `finance.*`, `projects.*`, `documents.*`, `reports.*`, `ai.use`, `members.manage`, `settings.manage`, `audit.read`, `exports.create`.

## 6. Clientes e relacionamento

### 6.1 `Customer`

| Campo | Tipo | Observação |
| --- | --- | --- |
| id, organizationId | uuid | tenant aggregate |
| type | enum | `PERSON`, `COMPANY` |
| name | string | obrigatório |
| companyName | string? | nome fantasia/empresa associada |
| taxIdHash/taxIdEncrypted | string? | hash para dedupe + valor protegido, se aprovado |
| email | citext? | contato principal |
| phone, whatsapp | string? | E.164 normalizado |
| address | json? ou campos | decidir conforme busca/relatórios |
| sourceId, categoryId | uuid? | catálogos da organização |
| status | enum | `LEAD`, `ACTIVE`, `INACTIVE`, `BLOCKED`, `ARCHIVED` |
| ownerMembershipId | uuid? | responsável ativo da organização |
| notes | text? | nota atual; histórico separado |
| lastInteractionAt, nextActionAt | timestamptz? | priorização |
| createdById, updatedById | uuid | autoria |
| createdAt, updatedAt, deletedAt | timestamptz | ciclo de vida |

Índices: `(organizationId,status,updatedAt)`, `(organizationId,ownerMembershipId,status)`, busca por nome normalizado; unicidade de taxId conforme decisão de produto.

### 6.2 Catálogos e contatos

- `CustomerSource`, `CustomerCategory`: configuráveis e ordenados por organização.
- `Tag`, `CustomerTag`: etiqueta reutilizável e join com unicidade.
- `CustomerContact`: múltiplos contatos da empresa, cargo, canais, primary flag.
- `CustomerNote`: nota com autor e visibilidade; não sobrescrever histórico.
- `ConsentRecord`: finalidade, canal, status, origem, policyVersion, capturedAt/revokedAt.

### 6.3 `ActivityEvent` vs `AuditLog`

`ActivityEvent` alimenta timeline amigável: `entityType`, `entityId`, `eventType`, `actorId`, `summary`, `metadataRedacted`, `occurredAt`. Pode ser ocultado ou reprocessado.

`AuditLog` é segurança/compliance: before/after selecionado, ação, actor, request ID, IP hash, user agent resumido, reason e timestamp. Append-only, retenção definida; não guardar segredos ou arquivo bruto.

## 7. Comercial

### 7.1 `Pipeline` e `PipelineStage`

- Pipeline: `organizationId`, `name`, `isDefault`, `active`.
- Stage: `pipelineId`, `name`, `position`, `kind` (`OPEN/WON/LOST`), `staleAfterDays?`, `probabilityDefault?`.
- constraints: posição/nome únicos no pipeline; stage won/lost não volta a open sem evento explícito.

### 7.2 `Opportunity`

`id`, `organizationId`, `customerId`, `pipelineId`, `stageId`, `title`, `productOrService?`, `estimatedValue`, `currency`, `probability`, `sourceId?`, `ownerMembershipId`, `expectedCloseDate?`, `nextAction`, `nextActionAt?`, `lastActivityAt`, `wonAt?`, `lostAt?`, `lostReasonId?`, `status`, `createdById`, timestamps, `version`.

Índices: estágio/posição, owner/próxima ação, `lastActivityAt`, fechamento esperado. Mudança de estágio gera `OpportunityStageHistory` com from/to, autor, data e motivo.

### 7.3 Proposta MVP

`Proposal`: oportunidade, número, status, validade, total opcional, `documentId` do PDF, sentAt/acceptedAt. Itens/editoração/visualização/aceite digital ficam fora do MVP.

## 8. Tarefas, agenda e projetos

### 8.1 `Task`

`id`, `organizationId`, `title`, `description?`, `status` (`PENDING/IN_PROGRESS/WAITING/DONE/CANCELED`), `priority`, `assigneeMembershipId?`, `createdById`, `customerId?`, `opportunityId?`, `projectId?`, `dueAt?`, `completedAt?`, `position?`, `recurrenceRule?` (futuro), timestamps, `deletedAt?`, `version`.

Filhos: `TaskChecklistItem`, `TaskComment`, `TaskAttachment`, `TaskTag`. Apenas um módulo de negócio pode ser relação principal; links adicionais podem usar entidade de relacionamento explicitamente validada.

### 8.2 `CalendarEvent`

`id`, `organizationId`, `type`, `title`, `description?`, `startsAt`, `endsAt`, `timezone`, `allDay`, `location?`, `customerId?`, `ownerMembershipId`, `recurrenceRule?`, `recurrenceParentId?`, `status`, timestamps.

`EventParticipant` vincula membership/customer contact/e-mail externo; `EventReminder` define offset/canal/status. Recorrência requer biblioteca/especificação RFC 5545 e política de exceções **VALIDAR**.

### 8.3 `Project`

O modelo comercial substitui semanticamente o projeto técnico atual: `organizationId`, `customerId?`, `name`, `description?`, `ownerMembershipId`, `status` (`PLANNING/IN_PROGRESS/WAITING_CUSTOMER/PAUSED/DONE/CANCELED`), `progress`, `startsAt?`, `dueAt?`, `budget?`, `currency`, `riskLevel`, timestamps e version.

Filhos: `ProjectMember`, `ProjectMilestone`, tarefas/documentos/comentários. Migração do modelo atual é tratada em §16.

## 9. Financeiro

### 9.1 Princípios

- não é contabilidade; é controle operacional de contas e caixa;
- valor original não é sobrescrito por pagamento parcial;
- status é derivado de parcelas/pagamentos quando possível;
- cancelamento/estorno gera evento, nunca apaga histórico;
- datas relevantes: competência **VALIDAR**, emissão, vencimento e liquidação.

### 9.2 `FinancialEntry`

`id`, `organizationId`, `kind` (`RECEIVABLE/PAYABLE`), `customerId?`, `supplierName?`, `description`, `categoryId`, `costCenterId?`, `totalAmount`, `currency`, `issueDate`, `competenceDate?`, `status`, `paymentMethodExpected?`, `responsibleMembershipId?`, `recurrenceGroupId?`, `notes?`, `createdById`, timestamps, `canceledAt?`, `version`.

### 9.3 Parcelas e pagamentos

- `Installment`: entry, sequence, amount, dueDate, status, paidAmount derivado, nextCollectionAt?, unique `(entryId,sequence)`.
- `Payment`: organização, direction, amount, currency, paidAt, method, reference, proofDocumentId?, recordedById, reversedAt?, reversalReason?.
- `PaymentAllocation`: payment ↔ installment, amount; soma não excede payment ou saldo da parcela.
- `CollectionAttempt`: customer/installment, channel, generatedMessageId?, outcome, attemptedById, attemptedAt, nextAttemptAt?.
- `FinancialCategory`, `CostCenter`: catálogos por organização.

Status `OVERDUE` deve ser calculado por vencimento + saldo, não persistido sem mecanismo consistente. Se materializado para consulta, um job o reconcilia.

Índices: `(organizationId,kind,dueDate,status)`, cliente/vencimento, próxima cobrança e categoria/competência.

## 10. Comunicação, documentos e notificações

### 10.1 Comunicação

- `MessageTemplate`: finalidade, canal, nome, conteúdo, variáveis permitidas, versão, ativo.
- `GeneratedMessage`: template/prompt, conteúdo, customer, creator, provider/model se IA, `approvedAt`.
- `ContactAttempt`: canal, direction, customer, opportunity/finance link, summary, result, actor, occurredAt, nextActionAt.

No MVP, `ContactAttempt` registra ação humana; não criar `MessageDelivery` como entregue se apenas abriu WhatsApp.

### 10.2 Documentos

`Document`: organização, storageKey opaca, originalName, mimeType, size, checksum, status (`PENDING_SCAN/AVAILABLE/QUARANTINED/DELETED`), type, customer/project/opportunity link, uploadedBy, timestamps. Download sempre por autorização e URL temporária.

Pastas podem ser `DocumentFolder` com parent; tags usam `DocumentTag`. Versionamento posterior usa `DocumentVersion`, sem sobrescrever objeto.

### 10.3 Notificações

`Notification`: recipientMembershipId, type, priority, title, body, entityType/entityId, readAt, archivedAt, createdAt. `NotificationPreference` por tipo/canal. `NotificationDelivery` registra canal, provider ID, status, tentativas e erro redigido.

## 11. IA, automações e integrações

### 11.1 IA

- `AiConversation`: organização, creator, scope/entity, title, status.
- `AiMessage`: role, conteúdo redigido, provider/model, token/cost metadata, createdAt.
- `AiToolCall`: tool, input redigido, permission decision, output summary, status, latency.
- `AiActionProposal`: action, payload, risk, confirmation token hash/expiry, confirmedBy/At, execution status.
- `AiUsage`: organização/dia/model, units/cost para entitlement.

O `AiInsight` atual pode ser migrado para conversation/message, mas prompts e contextos devem passar por classificação de PII antes.

### 11.2 Automações

- `AutomationRecipe`: template pronto, triggerType, conditions JSON validado, actions JSON validado, active, version.
- `AutomationRun`: recipe/version, triggeringEventId, status, started/finished, idempotencyKey, erro seguro.
- `AutomationStepRun`: ação, attempt, status, output seguro.

### 11.3 Integrações

- `IntegrationConnection`: provider, status, scopes, encryptedCredentialRef, externalAccountId, timestamps.
- `WebhookEvent`: provider, externalEventId, signatureValid, payload seguro, status, attempts; unique `(provider,externalEventId)` por conexão.
- `SyncCursor`: recurso, cursor, lastSuccessAt.

## 12. Plataforma e confiabilidade

- `OutboxEvent`: aggregate, type, payload versionado, occurredAt, publishedAt, attempts; gravado na transação do domínio.
- `IdempotencyRecord`: organization, actor, key, route, requestHash, response status/body resumido, expiry; unique por escopo.
- `FeatureFlagAssignment`: flag, organization/user, variant, timestamps; preferir serviço/config simples inicialmente.
- `ImportJob`: tipo, arquivo, mapping, status, contagens, errorReportDocumentId, requestedBy.
- `ExportJob`: escopo/filtros, status, documentId, expiresAt, requestedBy.
- `DeadLetter`: referência a job/evento, razão e replay auditado **se o provedor de fila não cobrir**.

## 13. Constraints de integridade essenciais

1. `endsAt > startsAt` para evento.
2. `estimatedValue`, entry/installment/payment/allocation >= 0`; ajustes negativos usam tipo explícito.
3. soma das parcelas igual ao total da entrada, com tolerância de arredondamento definida.
4. pagamento/alocação usam mesma organização/moeda ou conversão explicitamente fora do MVP.
5. `probability` entre 0 e 100; stage final consistente com won/lost timestamps.
6. membership referenciada pertence à mesma organização e está ativa na atribuição.
7. relacionamento de customer/opportunity/project/document não cruza organização.
8. slugs/nomes configuráveis têm unicidade tenant-aware.
9. timestamps de exclusão/cancelamento exigem autor/reason quando críticos.
10. toda tabela volumosa tem índice iniciando por organização e padrão de paginação.

## 14. Retenção, auditoria e LGPD

| Classe | Exemplos | Proposta | VALIDAR |
| --- | --- | --- | --- |
| Operacional | clientes, tarefas, finanças | enquanto contrato/base legal + janela de restauração | prazos e obrigação fiscal |
| Comunicação | tentativas, templates | prazo configurado e consentimento | WhatsApp/e-mail |
| Auditoria | mudanças críticas | append-only por prazo de compliance | 12/24/60 meses |
| IA | prompts, mensagens, tools | mínimo necessário; redaction; opt-out | provedor/região |
| Telemetria | logs/traces | curta, pseudonimizada | 30/90 dias |
| Arquivos | documentos/comprovantes | conforme entidade e plano | legal hold/scan |

Exclusão de titular deve considerar outros fundamentos legais. Preferir anonimização de referências e preservar integridade financeira/auditoria quando legalmente necessário. Toda exportação/exclusão é job auditado com dupla confirmação para escopo organizacional.

## 15. Estratégia de índices e particionamento

No piloto, índices compostos bem escolhidos são suficientes. Não particionar preventivamente. Medir tamanho/latência e considerar particionamento por data/organization hash para `audit_logs`, `activity_events`, `notifications`, `outbox_events` e telemetria apenas quando manutenção/consulta justificarem.

Busca começa com PostgreSQL (`pg_trgm`/full-text) limitada por organização. Serviço externo só após requisitos de relevância, volume ou permissões complexas.

## 16. Migração do schema atual

### 16.1 Expand

1. criar `organizations`, identity/membership e tabela de mapeamento sem alterar modelos atuais;
2. criar organização legado e membership para cada usuário atual;
3. adicionar `organizationId` nullable a Project/Task/AiInsight; backfill; índice; depois `NOT NULL`;
4. adicionar `ownerUserId/assigneeUserId` e preencher a partir de e-mail;
5. implementar v1 com dual-read/dual-write ou adapters legados;
6. criar novos modelos de negócio em tabelas separadas.

### 16.2 Decisão semântica sobre projetos

Projetos atuais são projetos técnicos/de demonstração. Não devem ser convertidos silenciosamente em projetos de clientes. Opções:

- marcar organização legado como “Demo” e manter os registros apenas no fluxo legado;
- importar como projetos internos sem `customerId`, com flag `source=LEGACY`;
- arquivar após exportação.

Escolha é **VALIDAR** com o dono dos dados antes do cutover.

### 16.3 Contract

Após paridade, reconciliação e janela de rollback:

- remover FKs por e-mail e roles técnicos;
- tornar `organizationId` obrigatório;
- retirar endpoints/telas legados;
- remover colunas antigas somente em release posterior;
- avaliar migração de `AiInsight` com redaction ou retenção separada.

Detalhes operacionais e rollback: [MIGRATION_PLAN.md](./MIGRATION_PLAN.md).

## 17. Dados de demonstração e teste

- seeds atuais são destrutivos (`deleteMany`) e não podem ser usados em produção;
- criar fixtures determinísticas por tenant apenas em dev/test;
- demo data deve ser claramente rotulada e apagável;
- testes geram duas organizações e verificam negação cruzada em todo repository/endpoint;
- dados sensíveis sintéticos, nunca cópia de produção em preview;
- migration test usa snapshot anonimizado e valida contagens/checksums.

## 18. Decisões pendentes

1. Supabase Auth/Auth.js e vínculo `authSubject`.
2. RLS obrigatório ou defesa opcional; estratégia de connection pool/context.
3. tax ID: validação, busca, criptografia e unicidade.
4. soft delete por domínio e prazo de recuperação.
5. financeiro: competência, recorrência, juros/multa, estorno e moeda.
6. storage, antimalware e limite por plano.
7. retenção de auditoria, IA, comunicação e arquivos.
8. escopo de equipe e permissões customizáveis no MVP.
9. recurrence de agenda/tarefa e exceções.
10. fórmula e persistência da métrica “perda evitada”.
