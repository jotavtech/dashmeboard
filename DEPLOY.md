# Dashmeboard — Deploy Guide

Production stack:

| Layer     | Platform                | Notes                                   |
| --------- | ----------------------- | --------------------------------------- |
| Frontend  | **Vercel**              | Vite SPA, built from the `frontend` workspace |
| Backend   | **Railway**             | Node/Express, root directory `backend`  |
| Database  | **Railway PostgreSQL**  | Provisioned as a service in the project |
| ORM       | **Prisma**              | Migrations applied on every deploy      |
| AI        | **OpenAI API**          | Called **only** from the backend        |

```
Vercel (frontend) ──▶ Railway (backend) ──▶ Railway PostgreSQL
                              │
                              └────────────▶ OpenAI API
```

> **Prisma is not the database.** Prisma is the ORM. The database is PostgreSQL,
> hosted on Railway. Prisma owns the schema, migrations and queries.

---

## 1. Database — Railway PostgreSQL

1. Open your Railway project → **New** → **Database** → **Add PostgreSQL**.
2. Railway provisions the service and exposes a `DATABASE_URL`.
3. You'll reference it from the backend as `${{Postgres.DATABASE_URL}}` (see below) —
   no need to copy the value by hand.

---

## 2. Backend — Railway

Create a service from the GitHub repo:

- **New** → **GitHub Repo** → `jotavtech/dashmeboard`
- **Settings → Root Directory**: leave empty so Railway builds from the repo root

Build & start come from the root [`railway.json`](railway.json) and
[`Dockerfile`](Dockerfile):

```
Build:  npm ci && npm run db:generate --workspace backend && npm run build --workspace backend
Start:  npx prisma migrate deploy --schema=./prisma/schema.prisma && node dist/index.js
Health: /api/health
```

`prisma migrate deploy` runs on every start, so the schema (including
`ai_insights`) is applied automatically.

### Environment variables (backend service)

```env
NODE_ENV=production
BACKEND_PORT=4000
DATABASE_URL=${{Postgres.DATABASE_URL}}
CORS_ORIGIN=https://YOUR-FRONTEND.vercel.app
OPENAI_API_KEY=sk-...            # backend only — never exposed to the client
OPENAI_MODEL=gpt-4o              # any model your OpenAI account can access
```

Notes:
- `OPENAI_API_KEY` is **optional**. Without it the backend still boots and all
  non-AI routes work; the AI routes return `503` until a key is set.
- Generate a public domain for the service (Settings → Networking → Generate Domain),
  e.g. `https://dashmeboard-api.up.railway.app`.

Verify:

```bash
curl https://YOUR-BACKEND.up.railway.app/api/health
curl https://YOUR-BACKEND.up.railway.app/api/health/ready
```

---

## 3. Frontend — Vercel

Import the repo into Vercel. The root [`vercel.json`](vercel.json) already
configures the monorepo build (Vite, `frontend/dist`, SPA rewrites).

- **Root Directory**: leave at repo root (vercel.json drives the build).
- **Environment variable**:

```env
VITE_API_URL=https://YOUR-BACKEND.up.railway.app/api
```

> `VITE_*` variables are public and build-time. Never create `VITE_OPENAI_API_KEY`.
> Redeploy after changing any `VITE_*` value.

---

## 4. Wire CORS

After the first Vercel deploy:

1. Copy the final Vercel URL.
2. In Railway, set `CORS_ORIGIN` on the backend to that exact origin
   (comma-separate multiple origins if needed).
3. Redeploy the backend.

---

## 5. Smoke test

### Backend

```bash
curl https://YOUR-BACKEND.up.railway.app/api/health
curl https://YOUR-BACKEND.up.railway.app/api/health/ready
curl https://YOUR-BACKEND.up.railway.app/api/projects
curl https://YOUR-BACKEND.up.railway.app/api/analytics/overview
curl https://YOUR-BACKEND.up.railway.app/api/ai/insights        # [] until generated
curl -X POST https://YOUR-BACKEND.up.railway.app/api/ai/insights # requires OPENAI_API_KEY
```

### Frontend (browser)

1. Dashboard loads; Health panel shows backend + database online.
2. Projects: list / create / edit / delete.
3. Analytics and Database views load real data.
4. **AI Center** opens → **generate insight** → result renders and is saved to History.

---

## Local development

```bash
# 1. Start Postgres
npm run docker:up            # or bring your own Postgres and set DATABASE_URL

# 2. Configure env
cp backend/.env.example backend/.env     # add OPENAI_API_KEY to use AI locally
cp frontend/.env.example frontend/.env

# 3. Migrate + run
npm run db:migrate --workspace backend
npm run dev                  # backend :4000, frontend :5173
```

---

## Troubleshooting

- **CORS error in prod** → `CORS_ORIGIN` must match the Vercel origin exactly.
- **AI returns 503** → `OPENAI_API_KEY` is not set on the backend service.
- **AI request times out** → the frontend uses a 60s timeout for generations;
  if you proxy the backend, ensure the proxy doesn't cap below that.
- **`ai_insights` table missing** → confirm the `migrate deploy` step ran in the
  deploy logs; the migration lives in `backend/prisma/migrations`.
- **`VITE_API_URL` not applied** → it's build-time; redeploy the frontend.
