#!/usr/bin/env bash
set -euo pipefail

# Bootstraps the local development environment.
# Idempotent — safe to re-run.

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

echo "· dashmeboard / bootstrap"

if [ ! -f .env ]; then
  cp .env.example .env
  echo "  created .env from example"
fi

if [ ! -f backend/.env ]; then
  cp backend/.env.example backend/.env
  echo "  created backend/.env from example"
fi

if [ ! -f .env.agent ]; then
  cp .env.agent.example .env.agent
  echo "  created .env.agent from example — fill in your provider key(s)"
fi

echo "· installing workspaces"
npm install

echo "· generating prisma client"
npm run --workspace backend db:generate || true

echo "· done. next steps:"
echo "    docker compose up -d postgres   # start the db"
echo "    npm run db:migrate              # apply schema"
echo "    npm run db:seed                 # populate demo data"
echo "    npm run dev                     # start backend + frontend"
