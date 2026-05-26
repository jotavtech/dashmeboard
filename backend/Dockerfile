FROM node:20-alpine AS deps
WORKDIR /app
COPY package.json package-lock.json ./
COPY backend/package.json backend/package.json
COPY frontend/package.json frontend/package.json
RUN npm ci

FROM node:20-alpine AS build
WORKDIR /app
COPY --from=deps /app/package.json /app/package-lock.json ./
COPY --from=deps /app/node_modules ./node_modules
COPY --from=deps /app/backend/node_modules ./backend/node_modules
COPY backend/prisma backend/prisma
COPY backend/src backend/src
COPY backend/tsconfig.json backend/tsconfig.json
COPY backend/package.json backend/package.json
RUN ./backend/node_modules/.bin/prisma generate --schema backend/prisma/schema.prisma
RUN ./node_modules/.bin/tsc -p backend/tsconfig.json

FROM node:20-alpine AS runner
WORKDIR /app
ENV NODE_ENV=production
COPY --from=build /app/node_modules ./node_modules
COPY --from=build /app/backend/node_modules ./backend/node_modules
COPY --from=build /app/backend/dist ./backend/dist
COPY --from=build /app/backend/prisma ./backend/prisma
COPY backend/package.json ./backend/package.json
WORKDIR /app/backend
EXPOSE 4000
CMD ["node", "dist/index.js"]
