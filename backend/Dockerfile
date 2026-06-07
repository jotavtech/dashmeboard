# Standalone backend image. Build context is the `backend/` directory
# (Railway Root Directory = backend), so everything here is relative to it.

# ---- Build stage ----
FROM node:20-alpine AS build
WORKDIR /app

# Install all dependencies (incl. dev) — the Prisma CLI and tsc live in devDeps.
COPY package.json ./
RUN npm install

# Copy sources, generate the Prisma client, then compile TypeScript to dist/.
COPY tsconfig.json prisma.config.ts ./
COPY prisma ./prisma
COPY src ./src
RUN npx prisma generate --schema=./prisma/schema.prisma
RUN npm run build

# ---- Runtime stage ----
FROM node:20-alpine AS runner
WORKDIR /app
ENV NODE_ENV=production

# node_modules carries the generated Prisma client and the Prisma CLI used by
# `migrate deploy` at boot; prisma.config.ts is required by that CLI invocation.
COPY package.json prisma.config.ts ./
COPY --from=build /app/node_modules ./node_modules
COPY --from=build /app/dist ./dist
COPY --from=build /app/prisma ./prisma

# Informational; the app actually binds to $PORT (injected by Railway) and
# falls back to BACKEND_PORT (4000) locally.
EXPOSE 4000

# Apply any pending migrations, then start the API.
CMD ["sh", "-c", "npx prisma migrate deploy --schema=./prisma/schema.prisma && node dist/index.js"]
