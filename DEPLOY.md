# Dashmeboard — Deploy Guide

Stack de produção recomendada (gratuito + estável):

| Camada    | Plataforma          | Plano       |
| --------- | ------------------- | ----------- |
| Frontend  | **Vercel**          | Hobby       |
| Backend   | **Render**          | Free        |
| Database  | **Neon Postgres**   | Free tier   |

---

## 1. Database — Neon

1. Criar conta em https://neon.tech
2. Criar novo projeto → `dashmeboard-prod`
3. Copiar a **Connection String** (formato `postgres://user:pass@host/dbname?sslmode=require`)
4. Guardar como `DATABASE_URL`

---

## 2. Backend — Render

### Via blueprint (`render.yaml`)

Render detecta o `render.yaml` automaticamente quando você conecta o repo.

1. Login em https://dashboard.render.com
2. **New +** → **Blueprint**
3. Conectar repo `jotavtech/dashmeboard`
4. Render lê `render.yaml` e propõe o serviço `dashmeboard-api`
5. Preencher os env vars `sync: false`:
   - `DATABASE_URL` = (Neon connection string)
   - `CORS_ORIGIN` = `https://dashmeboard.vercel.app` (ou seu domínio)
6. **Apply**

### Manual (se não usar Blueprint)

- **Type**: Web Service
- **Root Directory**: `backend`
- **Build Command**: `npm install && npx prisma generate && npm run build`
- **Start Command**: `npx prisma migrate deploy && npm run start`
- **Health Check Path**: `/api/health`
- **Env**:
  - `NODE_ENV=production`
  - `BACKEND_PORT=4000`
  - `DATABASE_URL=…`
  - `CORS_ORIGIN=…`

---

## 3. Frontend — Vercel

### Via dashboard

1. Login em https://vercel.com
2. **Add New → Project** → importar `jotavtech/dashmeboard`
3. **Root Directory**: `frontend`
4. Framework: **Vite** (detectado automaticamente)
5. **Environment Variables**:
   - `VITE_API_URL` = `https://dashmeboard-api.onrender.com/api`
6. **Deploy**

### Via CLI

```bash
npm i -g vercel
cd frontend
vercel --prod
```

---

## 4. Pós-deploy

1. **Testar health**: `curl https://dashmeboard-api.onrender.com/api/health`
2. **Atualizar `CORS_ORIGIN`** no Render com a URL final da Vercel
3. **Smoke test** no browser: dashboard, health panel, projects (CRUD), database view, theme toggle

---

## Variáveis de ambiente — resumo

### `backend/.env`
```env
DATABASE_URL=postgres://…
CORS_ORIGIN=https://dashmeboard.vercel.app
BACKEND_PORT=4000
NODE_ENV=production
```

### `frontend/.env`
```env
VITE_API_URL=https://dashmeboard-api.onrender.com/api
```

---

## Local dev (Docker)

```bash
docker compose up --build
```

- Frontend: http://localhost:5173
- Backend:  http://localhost:4000/api
- Postgres: localhost:5432

---

## Troubleshooting

- **CORS error em produção** → confirmar que `CORS_ORIGIN` no backend bate exatamente com o domínio Vercel.
- **Render dorme no plano free** → primeiro request leva ~30s; considerar upgrade ou cron de keep-alive.
- **Migrations não aplicam** → checar logs do Render; pode rodar manual via shell: `npx prisma migrate deploy`.
- **`VITE_API_URL` não pega** → variáveis Vite são build-time; redeploy após mudar.
