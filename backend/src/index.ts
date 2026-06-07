import { createApp } from "./app.js";
import { env } from "./lib/env.js";
import { disconnect } from "./lib/prisma.js";

const app = createApp();

const port = env.PORT ?? env.BACKEND_PORT;
const host = "0.0.0.0";

const server = app.listen(port, host, () => {
  console.log(
    `[dashmeboard-api] listening on http://${host}:${port}/api (${env.NODE_ENV})`,
  );
});

async function shutdown(signal: string) {
  console.log(`\n[dashmeboard-api] received ${signal}, shutting down…`);
  server.close(async () => {
    await disconnect();
    process.exit(0);
  });
}

process.on("SIGINT", () => shutdown("SIGINT"));
process.on("SIGTERM", () => shutdown("SIGTERM"));
