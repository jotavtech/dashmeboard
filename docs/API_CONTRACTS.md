# Contratos de API — Dashmeboard Business OS

> **Status:** especificação **TO-BE** para `/api/v1`. Não representa endpoints já implementados. O legado confirmado está listado em §2 e permanece temporariamente compatível conforme [MIGRATION_PLAN.md](./MIGRATION_PLAN.md).

## 1. Princípios

- HTTP/JSON, schemas Zod e OpenAPI gerado/versionado;
- autenticação e autorização no backend;
- organização explícita no path;
- respostas sem campos internos/PII desnecessária;
- cursor pagination, filtros determinísticos e ordenação estável;
- idempotência para criação/efeitos críticos;
- erros em `application/problem+json` com código estável e `traceId`;
- timestamps ISO 8601 UTC; moeda como decimal string + ISO 4217;
- breaking changes exigem nova versão; campos aditivos são compatíveis.

Base proposta: `/api/v1`. Recursos tenant-aware: `/api/v1/organizations/{organizationId}/...`.

## 2. API AS-IS confirmada

| Método | Rota | Estado/limite |
| --- | --- | --- |
| GET | `/api/health`, `/api/health/ready` | público; liveness/readiness |
| GET/POST | `/api/projects` | CRUD global, sem auth/tenant/paginação |
| GET/PATCH/DELETE | `/api/projects/:id` | delete físico; owner por e-mail |
| GET | `/api/analytics/{overview,activity,throughput,database}` | agregações globais |
| GET/POST | `/api/ai/insights` | leitura/geração sem auth; rate limit por processo/IP |
| POST | `/api/ai/project-plan` | geração OpenAI por projectId |

Controllers validam parte das entradas com Zod; erros atuais são `{message, issues?}`. Não existe OpenAPI, versionamento, JWT, RBAC, idempotência ou request ID contratual.

## 3. Autenticação e contexto

### 3.1 Headers

```http
Authorization: Bearer <JWT>
Accept: application/json
Content-Type: application/json
X-Request-Id: <uuid opcional do cliente>
Idempotency-Key: <uuid ou chave opaca>   # quando requerido
If-Match: "<version>"                    # mutação concorrente quando suportada
```

A API valida assinatura, issuer, audience, expiry e subject. Não aceita `userId`, role ou organization claims fornecidos pelo body como autoridade.

### 3.2 Organização

`organizationId` no path é obrigatório para domínio. A API confirma membership ativa antes de consultar o recurso. Para reduzir enumeração, recurso de outra organização retorna `404 RESOURCE_NOT_FOUND` quando apropriado, sem revelar sua existência.

### 3.3 Endpoints globais de sessão

| Método | Rota | Permissão | Resultado |
| --- | --- | --- | --- |
| GET | `/me` | autenticado | perfil e organizações disponíveis |
| POST | `/organizations` | autenticado/entitlement | cria organização |
| GET | `/organizations` | autenticado | memberships ativas |
| POST | `/organizations/{id}/switch-events` | member | registra seleção/último acesso, opcional |

Login, reset e token podem ser hospedados pelo provedor; a API não deve duplicar senha.

## 4. RBAC

Permission keys seguem `module.action`, com escopo `OWN/TEAM/ALL` quando aplicável.

| Domínio | Read | Write/ações especiais |
| --- | --- | --- |
| Customers | `customers.read` | `customers.create/update/delete/export/import` |
| Sales | `sales.read` | `sales.create/update/move/export` |
| Tasks | `tasks.read` | `tasks.create/update/assign/delete` |
| Calendar | `calendar.read` | `calendar.create/update/delete` |
| Finance | `finance.read` | `finance.create/update/record_payment/cancel/export` |
| Projects | `projects.read` | `projects.create/update/delete` |
| Documents | `documents.read` | `documents.upload/delete` |
| AI | contexto das fontes | `ai.use`, mais permissão da ação proposta |
| Team | `members.read` | `members.invite/manage` |
| Settings/Audit | específica | `settings.manage`, `audit.read` |

A matriz por role do PRD é **VALIDAR**. Deny por padrão; frontend não é enforcement.

## 5. Formatos comuns

### 5.1 Resource metadata

```json
{
  "id": "uuid",
  "organizationId": "uuid",
  "createdAt": "2026-07-16T15:00:00.000Z",
  "updatedAt": "2026-07-16T15:00:00.000Z",
  "version": 3
}
```

`organizationId` pode ser omitido da representação pública quando redundante, mas nunca do armazenamento/contexto.

### 5.2 Dinheiro

```json
{ "amount": "8420.00", "currency": "BRL" }
```

Cliente envia string decimal, nunca float. A API rejeita mais casas/valores conforme regra do domínio.

### 5.3 Paginação

Request: `?limit=50&cursor=<opaque>&sort=-updatedAt`. `limit` default 25, máximo 100 salvo export job.

```json
{
  "data": [],
  "page": {
    "nextCursor": "opaque-or-null",
    "hasMore": false
  },
  "meta": { "requestId": "uuid" }
}
```

Cursor codifica ordenação + ID de desempate e é opaco. `total` só quando barato/solicitado; não prometer contagem global em toda lista.

### 5.4 Filtros

Filtros repetidos usam CSV ou parâmetros repetidos definidos em OpenAPI. Datas usam `from/to`. Busca `q` tem limite. Campos desconhecidos retornam validação, não são ignorados silenciosamente.

### 5.5 Erros

Content-Type `application/problem+json`:

```json
{
  "type": "https://docs.dashmeboard.com/problems/validation-error",
  "title": "Não foi possível concluir a solicitação",
  "status": 422,
  "code": "VALIDATION_ERROR",
  "detail": "Revise os campos indicados.",
  "instance": "/api/v1/organizations/ORG/customers",
  "traceId": "uuid",
  "errors": [
    { "path": "email", "code": "INVALID_EMAIL", "message": "Informe um e-mail válido." }
  ]
}
```

| HTTP | Código exemplo | Uso |
| --- | --- | --- |
| 400 | `MALFORMED_REQUEST` | JSON/query inválido |
| 401 | `AUTH_REQUIRED`, `TOKEN_EXPIRED` | sem sessão válida |
| 403 | `PERMISSION_DENIED` | membership existe, ação negada |
| 404 | `RESOURCE_NOT_FOUND` | ausente ou não revelável |
| 409 | `CONFLICT`, `DUPLICATE_RESOURCE` | unicidade/estado |
| 412 | `VERSION_MISMATCH` | `If-Match` falhou |
| 422 | `VALIDATION_ERROR`, `BUSINESS_RULE_VIOLATION` | campos/regra |
| 429 | `RATE_LIMITED`, `QUOTA_EXCEEDED` | limite; `Retry-After` |
| 503 | `DEPENDENCY_UNAVAILABLE` | provider temporariamente indisponível |

5xx não expõe stack, SQL ou segredo. Logs correlacionam `traceId`.

## 6. Idempotência e concorrência

### 6.1 Idempotência

`Idempotency-Key` é obrigatório em:

- criação de organização/invite/import/export;
- criação financeira, registro/estorno de pagamento;
- upload confirmation;
- geração/execução de ação IA;
- futura entrega de mensagem/webhook replay.

Escopo: organização + actor + método + rota. Mesmo key e mesmo hash retorna status/body original; mesmo key com payload diferente retorna `409 IDEMPOTENCY_KEY_REUSED`. Retenção mínima de 24h; financeiro pode exigir maior **VALIDAR**.

### 6.2 Concorrência

Recursos disputados retornam `ETag: "3"`; PATCH exige `If-Match`. Em mismatch, 412 com versão atual/metadados seguros. Drag Kanban e pagamento não fazem last-write-wins silencioso.

## 7. Contratos de identidade e equipe

### 7.1 `GET /me`

```json
{
  "id": "usr_uuid",
  "name": "João",
  "email": "joao@example.com",
  "organizations": [
    { "id": "org_uuid", "name": "Empresa Alfa", "role": "OWNER", "status": "ACTIVE" }
  ]
}
```

### 7.2 Memberships

| Método | Rota | Permissão |
| --- | --- | --- |
| GET | `/organizations/{org}/members` | `members.read` |
| POST | `/organizations/{org}/invitations` | `members.invite` + idempotência |
| PATCH | `/organizations/{org}/members/{id}` | `members.manage` |
| DELETE | `/organizations/{org}/members/{id}` | `members.manage`; revoga, não apaga user |

Invite request: `{ "email":"...", "roleKey":"FINANCE" }`. Não retornar token bruto em listagens.

## 8. Clientes

### 8.1 Rotas

| Método | Rota | Observação |
| --- | --- | --- |
| GET/POST | `/organizations/{org}/customers` | lista/cria |
| GET/PATCH/DELETE | `/organizations/{org}/customers/{id}` | detalhe/atualiza/arquiva |
| GET | `/organizations/{org}/customers/{id}/timeline` | cursor por occurredAt/id |
| GET | `/organizations/{org}/customers/{id}/summary` | visão 360 resumida |
| POST | `/organizations/{org}/customer-imports` | job com upload/mapping |
| POST | `/organizations/{org}/customer-exports` | job auditado |

### 8.2 Customer create

```json
{
  "type": "COMPANY",
  "name": "Empresa Alfa",
  "companyName": "Alfa Serviços",
  "taxId": "12.345.678/0001-00",
  "email": "contato@alfa.com.br",
  "phone": "+5511999999999",
  "whatsapp": "+5511999999999",
  "status": "ACTIVE",
  "ownerMembershipId": "uuid",
  "sourceId": "uuid",
  "categoryId": "uuid",
  "tagIds": ["uuid"],
  "notes": "Prefere contato à tarde"
}
```

Response 201 inclui `Location`, resource e ETag. Tax ID/telefone são normalizados; política de duplicate retorna 409 com informação segura.

### 8.3 Lista

Filtros: `q`, `status`, `ownerId`, `sourceId`, `categoryId`, `tagId`, `updatedFrom`, `nextActionBefore`, `sort`. Resposta não inclui notes completas/documentos.

## 9. Comercial

| Método | Rota | Função |
| --- | --- | --- |
| GET/POST | `/organizations/{org}/pipelines` | listar/criar quando permitido |
| GET/PATCH | `/organizations/{org}/pipelines/{id}` | stages/configuração |
| GET/POST | `/organizations/{org}/opportunities` | lista/cria |
| GET/PATCH | `/organizations/{org}/opportunities/{id}` | detalhe/edita |
| POST | `/organizations/{org}/opportunities/{id}/stage-transitions` | movimento explícito |
| POST | `/organizations/{org}/opportunities/{id}/proposals` | metadata/PDF MVP |

Opportunity create:

```json
{
  "customerId": "uuid",
  "pipelineId": "uuid",
  "stageId": "uuid",
  "title": "Implantação mensal",
  "productOrService": "Consultoria",
  "estimatedValue": { "amount": "12500.00", "currency": "BRL" },
  "probability": 60,
  "ownerMembershipId": "uuid",
  "expectedCloseDate": "2026-08-31",
  "nextAction": "Enviar proposta revisada",
  "nextActionAt": "2026-07-18T13:00:00.000Z"
}
```

Transition request exige `toStageId`, `position?`, `reason?` e `If-Match`. Ir para LOST exige motivo; WON pode retornar ações sugeridas, mas não cria cobrança sem comando/confirm.

## 10. Tarefas e agenda

### 10.1 Tasks

`GET/POST /organizations/{org}/tasks`; `GET/PATCH/DELETE /.../tasks/{id}`; `POST /.../tasks/{id}/comments`; `POST/PATCH /.../tasks/{id}/checklist-items`.

```json
{
  "title": "Retornar para Empresa Alfa",
  "description": "Confirmar aceite da proposta",
  "status": "PENDING",
  "priority": "HIGH",
  "assigneeMembershipId": "uuid",
  "customerId": "uuid",
  "opportunityId": "uuid",
  "dueAt": "2026-07-18T17:00:00.000Z",
  "tagIds": []
}
```

Completar tarefa usa PATCH com `status=DONE` e `If-Match`; servidor define `completedAt`/actor.

### 10.2 Calendar

`GET/POST /organizations/{org}/events`; `GET/PATCH/DELETE /.../events/{id}`. Lista exige range `from/to`, com máximo (ex. 90 dias) **VALIDAR**.

```json
{
  "type": "FOLLOW_UP",
  "title": "Retorno comercial",
  "startsAt": "2026-07-18T16:00:00.000Z",
  "endsAt": "2026-07-18T16:30:00.000Z",
  "timezone": "America/Sao_Paulo",
  "customerId": "uuid",
  "ownerMembershipId": "uuid",
  "participantMembershipIds": ["uuid"],
  "reminders": [{ "minutesBefore": 30, "channel": "IN_APP" }]
}
```

## 11. Financeiro

### 11.1 Rotas

| Método | Rota | Requisito |
| --- | --- | --- |
| GET/POST | `/organizations/{org}/financial-entries` | read/create |
| GET/PATCH | `/organizations/{org}/financial-entries/{id}` | versionado |
| POST | `/organizations/{org}/financial-entries/{id}/cancel` | motivo + permissão |
| POST | `/organizations/{org}/payments` | idempotência obrigatória |
| POST | `/organizations/{org}/payments/{id}/reverse` | motivo + idempotência |
| POST | `/organizations/{org}/installments/{id}/collection-attempts` | histórico |
| GET | `/organizations/{org}/cash-flow` | from/to/granularity/basis |

### 11.2 Financial entry

```json
{
  "kind": "RECEIVABLE",
  "customerId": "uuid",
  "description": "Mensalidade julho",
  "categoryId": "uuid",
  "total": { "amount": "1500.00", "currency": "BRL" },
  "issueDate": "2026-07-01",
  "installments": [
    { "sequence": 1, "amount": "750.00", "dueDate": "2026-07-10" },
    { "sequence": 2, "amount": "750.00", "dueDate": "2026-08-10" }
  ],
  "responsibleMembershipId": "uuid"
}
```

### 11.3 Payment

```json
{
  "paidAt": "2026-07-16T14:30:00.000Z",
  "amount": { "amount": "500.00", "currency": "BRL" },
  "method": "PIX",
  "reference": "pix-e2e-ou-referencia",
  "allocations": [
    { "installmentId": "uuid", "amount": "500.00" }
  ],
  "proofDocumentId": null
}
```

Servidor valida soma, saldos, organização e moeda. Response inclui saldos resultantes. Não aceitar PATCH destrutivo em payment; usar reverse.

## 12. Projetos e documentos

Projetos: `GET/POST /organizations/{org}/projects`, `GET/PATCH/DELETE /.../{id}`, agora com customer/owner por ID, paginação, auth e audit. `/api/projects` legado não muda até sunset.

Upload em três etapas:

1. `POST /documents/upload-intents` com nome/MIME/tamanho/checksum/vínculo;
2. upload direto por URL assinada;
3. `POST /documents/{id}/confirm` idempotente; documento fica pending/available após validação.

Download: `POST /documents/{id}/download-intents`; URL curta, permission check e audit. Nunca expor storage key/bucket público.

## 13. Dashboard, timeline, busca e notificações

| Método | Rota | Contrato |
| --- | --- | --- |
| GET | `/organizations/{org}/dashboard?period=...` | blocos independentes com `generatedAt` |
| GET | `/organizations/{org}/attention-items` | prioridade, reason, entity link, CTA |
| GET | `/organizations/{org}/activity` | feed cursor-based |
| GET | `/organizations/{org}/search?q=...&types=...` | resultados autorizados |
| GET | `/organizations/{org}/commands?q=...` | comandos disponíveis por permissão |
| GET | `/organizations/{org}/notifications` | unread/priority/type |
| POST | `/organizations/{org}/notifications/{id}/read` | idempotente |
| POST | `/organizations/{org}/notifications/read-batch` | IDs limitados |

Dashboard não deve mascarar parcial failure. Exemplo:

```json
{
  "data": {
    "attention": { "status": "ok", "items": [] },
    "finance": { "status": "unavailable", "errorCode": "WIDGET_UNAVAILABLE" }
  },
  "generatedAt": "2026-07-16T15:00:00.000Z",
  "meta": { "requestId": "uuid", "period": { "from": "2026-07-01", "to": "2026-07-31" } }
}
```

## 14. Comunicação e IA

### 14.1 WhatsApp assistido

| Método | Rota | Função |
| --- | --- | --- |
| GET/POST | `/organizations/{org}/message-templates` | template autorizado |
| POST | `/organizations/{org}/messages/generate` | texto, não envio |
| POST | `/organizations/{org}/contact-attempts` | registra tentativa humana |

Generate request contém `customerId`, `purpose`, `templateId?`, `tone?`, `context?` restrito. Response:

```json
{
  "id": "uuid",
  "content": "Olá, ...",
  "channel": "WHATSAPP",
  "status": "DRAFT",
  "deepLink": "https://wa.me/...",
  "generatedBy": "AI",
  "warnings": []
}
```

`deepLink` não prova envio. Registrar tentativa é operação separada.

### 14.2 Ask Dash

| Método | Rota | Observação |
| --- | --- | --- |
| POST | `/organizations/{org}/ai/conversations` | cria contexto |
| GET | `/organizations/{org}/ai/conversations` | histórico permitido |
| POST | `/organizations/{org}/ai/conversations/{id}/messages` | pode responder 202/job |
| POST | `/organizations/{org}/ai/action-proposals/{id}/confirm` | revalida auth/payload |
| POST | `/organizations/{org}/ai/action-proposals/{id}/reject` | auditado |

Mensagem:

```json
{
  "content": "Quem está me devendo?",
  "scope": { "customerId": null },
  "clientRequestId": "uuid"
}
```

Resposta inclui `answer`, `asOf`, `sources[]` com entityType/id/label permitido, `suggestedActions[]` e `usage`. Tools são executadas no backend com as permissões do usuário. Proposta expira; confirmar revalida role, tenant, version e idempotency key. Excluir, enviar mensagem e alterar pagamento não são automáticos no MVP.

## 15. Jobs, imports e exports

Operações longas retornam 202:

```json
{
  "jobId": "uuid",
  "status": "QUEUED",
  "statusUrl": "/api/v1/organizations/org/jobs/uuid"
}
```

`GET /jobs/{id}` retorna progress counts e erro seguro. Cancelamento só se job suportar. Export URL expira e requer nova autorização. Jobs não podem ser consultados cross-tenant.

## 16. Webhooks futuros

- endpoint por provider, fora do path tenant quando provider não envia org;
- validar assinatura antes de parse/use;
- resolver connection/organization internamente;
- dedupe por external event ID, persistir receipt e responder 2xx rapidamente;
- processamento assíncrono; retries idempotentes;
- nunca confiar em valor/status sem verificar contrato do provider.

## 17. Versionamento e depreciação

- breaking HTTP: `/api/v2`; não usar header escondido para mudar semântica;
- campos novos opcionais e novos enum values exigem clientes tolerantes; enum breaking pode demandar nova versão;
- OpenAPI publicado por release, diff no CI;
- legado recebe `Deprecation: true`, `Sunset` e `Link` quando data aprovada;
- telemetria identifica consumidores antes do desligamento;
- eventos/outbox têm `eventVersion`; webhooks de saída teriam assinatura e retry contract.

## 18. Cache e segurança HTTP

- respostas privadas: `Cache-Control: private, no-store` por default para PII/financeiro;
- ETag em recursos/versioning; dashboard pode usar short private cache tenant-aware;
- CORS allowlist não substitui auth;
- CSP/Helmet, body limits por rota, upload direto;
- rate limit distribuído por user/org/IP/route cost; headers de limite sem expor plano sensível;
- busca/export/AI têm quotas independentes.

## 19. Testes de contrato obrigatórios

1. cada schema OpenAPI possui success/error examples validados;
2. cliente gerado compila com frontend;
3. nenhuma rota tenant funciona sem auth/org membership;
4. IDs de outra organização retornam resposta não reveladora;
5. role matrix cobre allow/deny;
6. idempotency replay e payload mismatch;
7. pagination sem duplicação/perda com ordenação estável;
8. money serialization e timezone;
9. If-Match mismatch;
10. N/N-1 durante expand/contract;
11. provider timeout/429/503 mapeados sem segredo;
12. logs/Problem Details possuem trace ID e não PII.

## 20. Pontos a validar

- auth provider e formato exato JWT;
- nomes finais de permission keys/roles/scopes;
- retenção de idempotency records;
- max page/range e limites por plano;
- `taxId` duplicate behavior e masking;
- recorrência de eventos/tarefas;
- estratégia 202/SSE para streaming de IA;
- formato de filtros OpenAPI;
- data de sunset do legado;
- domínio público de Problem Types e documentação da API.
