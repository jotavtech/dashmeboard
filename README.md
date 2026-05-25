# DASHMEBOARD

> Smart Workflow & Operations Platform — visual infrastructure for modern teams.

Editorial-cyber dashboard for managing projects, workflows, analytics and team operations. Built as a fullstack monorepo with a local AI-agents subsystem for engineering augmentation.

---

## Stack

| Layer        | Tech                                                        |
|--------------|-------------------------------------------------------------|
| Frontend     | Vite, React 19, TypeScript, Tailwind 3, Framer Motion, Lenis|
| Backend      | Node.js, Express, TypeScript, Prisma                        |
| Database     | PostgreSQL 16                                               |
| Infra        | Docker, Docker Compose                                      |
| CI/CD        | GitHub Actions, SonarCloud                                  |
| AI Agents    | Node + TS, Anthropic / OpenAI / OpenRouter (local-only)     |

---

## Quickstart

```bash
# 1. Copy env files
cp .env.example .env
cp backend/.env.example backend/.env

# 2. Bring everything up with docker
npm run docker:up

# OR run dev mode locally (requires Postgres on :5432)
npm install
npm run db:migrate
npm run db:seed
npm run dev
```

| Service   | URL                       |
|-----------|---------------------------|
| Frontend  | http://localhost:5173     |
| Backend   | http://localhost:4000/api |
| Postgres  | localhost:5432            |

---

## Monorepo layout

```txt
dashmeboard/
├── frontend/          Vite + React UI
├── backend/           Express + Prisma API
├── agents/            Local AI-agents subsystem (gitignored)
├── memory/            Agent persistent memory (gitignored)
├── prompts/           Specialized system prompts (committed)
├── logs/              Agent execution logs (gitignored)
├── scripts/           Operational scripts
├── docker/            Dockerfiles + helpers
├── docker-compose.yml
└── .github/workflows/ CI/CD pipeline
```

---

## AI Agents subsystem

The `/agents` directory hosts a local-only orchestrator + specialized agents (architect, frontend, backend, devops, design, docs, reviewer). It is **gitignored by design** — credentials, memory and runs never leave the developer's machine.

```bash
npm run agents:start           # interactive orchestrator
npm run agent:frontend         # invoke frontend agent directly
npm run agent:reviewer         # invoke reviewer agent directly
```

Each agent's contract lives in `/prompts/{name}.md` (these *are* committed — they describe the public design of the system without exposing secrets).

See `agents/README.md` for the full contract.

---

## Design system

Editorial cyber / dark ambient / brutalism. Monospace-heavy. Bracket-coded navigation. Section markers. Grain + scanline overlays.

| Token            | Value                              |
|------------------|------------------------------------|
| `bg ink`         | #070707                            |
| `fg chrome`      | #E8E8E8                            |
| `accent rust`    | #FF3B1F                            |
| `hairline`       | rgba(255,255,255,0.08)             |
| Display font     | Space Grotesk                      |
| Body font        | Inter                              |
| Mono font        | JetBrains Mono                     |

See `frontend/tailwind.config.ts` for the full palette and `frontend/src/components/primitives/` for the design primitives (`ChromeText`, `TerminalLabel`, `PaneFrame`, `Magnetic`, `Grain`, `Scanlines`).
