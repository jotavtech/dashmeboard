# Monorepo-root build of the backend service. Build context is the repo root,
# which is how Railway builds when no Root Directory is set (it detects the npm
# workspace here). Only the backend is built and run.
FROM node:20-alpine
WORKDIR /app

# NOTE: NODE_ENV is intentionally NOT set to "production" here. With
# NODE_ENV=production, `npm ci` skips devDependencies (typescript, @types/*,
# tsc), which the build needs. It is set to production further down, so it only
# affects the runtime, not the install/build.

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

# Runtime-only: enables production behaviour without having starved the build
# of devDependencies above.
ENV NODE_ENV=production
EXPOSE 4000

# Apply pending migrations when the database is reachable, then start the API.
# Liveness must still come up if DB variables are being fixed in the platform.
CMD ["sh", "-c", "npx prisma migrate deploy --schema=./prisma/schema.prisma || echo '[dashmeboard-api] migrate deploy skipped or failed; starting API for liveness'; node dist/index.js"]
