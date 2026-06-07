# Monorepo-root build of the backend service. Build context is the repo root,
# which is how Railway builds when no Root Directory is set (it detects the npm
# workspace here). Only the backend is built and run.
FROM node:20-alpine
WORKDIR /app
ENV NODE_ENV=production

# Install workspace dependencies deterministically from the root lockfile.
# All three manifests are required for `npm ci` to resolve the workspace graph.
COPY package.json package-lock.json ./
COPY backend/package.json ./backend/package.json
COPY frontend/package.json ./frontend/package.json
RUN npm ci

# Backend sources only; generate the Prisma client, then compile to dist/.
COPY backend ./backend
RUN npm run db:generate --workspace backend
RUN npm run build --workspace backend

WORKDIR /app/backend
EXPOSE 4000

# Apply any pending migrations, then start the API. The app binds to $PORT
# (injected by Railway), falling back to BACKEND_PORT locally.
CMD ["sh", "-c", "npx prisma migrate deploy --schema=./prisma/schema.prisma && node dist/index.js"]
