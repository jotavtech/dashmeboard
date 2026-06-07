# Dashmeboard

Dashmeboard is a production-ready academic/business dashboard for project operations. It combines a cinematic React interface, an Express API, PostgreSQL, Prisma migrations, Docker Compose, GitHub Actions and SonarCloud quality analysis.

Important: this repository preserves the current Dashmeboard purpose and improves the existing monorepo directly. It is not a separate rewrite.

## Stack

| Layer | Technology |
| --- | --- |
| Frontend | React 19, Vite, TypeScript, Tailwind CSS, Framer Motion, TanStack Query |
| Backend | Node.js, Express, TypeScript, Zod, Helmet, CORS |
| Database | PostgreSQL 16, Prisma ORM and committed migrations |
| AI | OpenAI API (Responses), called only from the backend, insights persisted |
| DevOps | Docker Compose, GitHub Actions, SonarCloud, Railway, Vercel |
| Quality | ESLint 9, TypeScript strict mode, Vitest, Supertest, npm audit |

## Architecture

```txt
dashmeboard/
├── frontend/              React application and cinematic UI
├── backend/               Express API, Prisma schema, migrations and tests
├── scripts/               Local bootstrap helpers
├── .github/workflows/     CI, Docker validation and SonarCloud scan
├── docker-compose.yml     Frontend + backend + PostgreSQL
├── sonar-project.properties
└── README.md
```

## Environment

Create local env files from the examples:

```bash
cp .env.example .env
cp backend/.env.example backend/.env
```

Main variables:

| Variable | Purpose | Default for local development |
| --- | --- | --- |
| `DATABASE_URL` | Backend PostgreSQL connection | `postgresql://dashme:dashme_local_password@localhost:5432/dashmeboard?schema=public` |
| `CORS_ORIGIN` | Allowed frontend origin | `http://localhost:5173` |
| `BACKEND_PORT` | API port | `4000` |
| `VITE_API_URL` | Frontend API URL | `http://localhost:4000/api` |
| `OPENAI_API_KEY` | OpenAI key (backend only, optional) | unset → AI routes return `503` |
| `OPENAI_MODEL` | OpenAI model for generations | `gpt-4o` |
| `POSTGRES_*` | Docker database settings | see `.env.example` |

## Local Development

```bash
npm install
npm run db:generate
docker compose up -d postgres
npm run db:migrate:deploy
npm run db:seed
npm run dev
```

Services:

| Service | URL |
| --- | --- |
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:4000/api |
| Health check | http://localhost:4000/api/health |
| PostgreSQL | localhost:5432 |

## Docker Execution

The full system runs with:

```bash
docker compose up --build
```

The backend container applies committed Prisma migrations before starting. To reset local Docker data during development:

```bash
docker compose down -v
docker compose up --build
```

## Scripts

| Command | Description |
| --- | --- |
| `npm run dev` | Start backend and frontend in development mode |
| `npm run lint` | Run ESLint across workspaces |
| `npm run typecheck` | Run strict TypeScript checks |
| `npm run test` | Run backend API tests with coverage |
| `npm run build` | Build backend and frontend |
| `npm run db:generate` | Generate Prisma client |
| `npm run db:migrate:deploy` | Apply committed migrations |
| `npm run db:seed` | Seed presentation data |
| `npm run docker:config` | Validate Docker Compose |
| `npm run docker:build` | Build Docker images |

## API Endpoints

Base URL: `/api`

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/health` | API, database, uptime and environment status |
| `GET` | `/projects` | List projects with search/status/priority filters |
| `POST` | `/projects` | Create a project with Zod validation |
| `GET` | `/projects/:id` | Fetch one project |
| `PATCH` | `/projects/:id` | Update a project |
| `DELETE` | `/projects/:id` | Delete a project and related tasks |
| `GET` | `/analytics/overview` | Project/task/user metrics from PostgreSQL |
| `GET` | `/analytics/activity` | Recent project and task activity |
| `GET` | `/analytics/throughput` | Completed tasks over the last 7 days |
| `GET` | `/analytics/database` | Public schema table snapshot |
| `GET` | `/ai/insights` | List persisted AI insights (most recent first) |
| `POST` | `/ai/insights` | Generate a dashboard insight from live data (rate-limited) |
| `POST` | `/ai/project-plan` | Generate an execution plan for a project (rate-limited) |

## Deployment

Production runs on **Vercel** (frontend) → **Railway** (backend) → **Railway PostgreSQL**, with OpenAI called only from the backend. Full step-by-step in [`DEPLOY.md`](DEPLOY.md).

## Quality Gates

The GitHub Actions workflow runs:

- dependency installation with `npm ci`
- Prisma client generation and migrations
- lint
- typecheck
- tests with coverage
- production build
- high-severity dependency audit
- `docker compose config`
- `docker compose build`
- SonarCloud scan on `main` pushes when `SONAR_TOKEN` is configured

SonarCloud is configured in `sonar-project.properties` with source/test paths, TypeScript configs and justified exclusions for generated/build/coverage artifacts.

## Presentation Notes

For an academic demo:

1. Run `docker compose up --build`.
2. Open http://localhost:5173.
3. Show Dashboard health panel: frontend, backend, database, latency and environment.
4. Open Projects and demonstrate create, edit, delete, search and filters.
5. Open Database to show live PostgreSQL-backed table counts.
6. Open GitHub Actions and SonarCloud configuration in the repository.

## Academic Requirements Checklist

- [x] Frontend integrated with backend
- [x] PostgreSQL database
- [x] Docker Compose
- [x] GitHub Actions
- [x] SonarCloud
- [x] Complete CRUD
- [x] Organized conventional commits for this hardening pass
- [x] Professional README

## Troubleshooting

| Problem | Fix |
| --- | --- |
| Port `5432` already in use | Set `POSTGRES_PORT` in `.env` or stop the other PostgreSQL container |
| API cannot connect to database | Check `DATABASE_URL`, then run `npm run db:migrate:deploy` |
| Frontend calls wrong API URL | Update `VITE_API_URL` and rebuild the frontend |
| SonarCloud job is skipped | Add repository secret `SONAR_TOKEN` |
| Docker data is stale | Run `docker compose down -v` before rebuilding |
