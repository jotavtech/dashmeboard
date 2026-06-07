import OpenAI from "openai";
import { env } from "../lib/env.js";
import { prisma } from "../lib/prisma.js";
import { HttpError } from "../middlewares/error.js";

let client: OpenAI | null = null;

/**
 * Lazily resolve the OpenAI client. The key is optional in `env` so the app
 * (and the test suite) boots without it — instead of crashing at startup we
 * surface a clean 503 the moment an AI route is actually called.
 */
function getClient(): OpenAI {
  if (!env.OPENAI_API_KEY) {
    throw new HttpError(503, "AI is not configured: OPENAI_API_KEY is missing");
  }
  if (!client) {
    client = new OpenAI({ apiKey: env.OPENAI_API_KEY });
  }
  return client;
}

/** Prisma's Json input rejects raw Date objects; normalise to JSON-safe values. */
function toJson(value: unknown) {
  return JSON.parse(JSON.stringify(value));
}

async function complete(prompt: string): Promise<string> {
  const response = await getClient().responses.create({
    model: env.OPENAI_MODEL,
    input: prompt,
  });
  return response.output_text?.trim() || "Nenhum resultado foi gerado.";
}

export const aiService = {
  async generateDashboardInsight() {
    const [projects, totalProjects, activeProjects, totalTasks, completedTasks] =
      await Promise.all([
        prisma.project.findMany({
          orderBy: { updatedAt: "desc" },
          take: 20,
          include: { tasks: true },
        }),
        prisma.project.count(),
        prisma.project.count({ where: { status: "ACTIVE" } }),
        prisma.task.count(),
        prisma.task.count({ where: { status: "DONE" } }),
      ]);

    const context = {
      totalProjects,
      activeProjects,
      totalTasks,
      completedTasks,
      projects,
    };

    const prompt = `Você é o copiloto estratégico do Dashmeboard.

Analise os dados reais abaixo e gere uma resposta em português brasileiro, em markdown, com:

1. Resumo executivo
2. Principais gargalos
3. Riscos
4. Próximas 5 ações recomendadas
5. Prioridade da semana

Seja direto, profissional e útil.

DADOS:
${JSON.stringify(context, null, 2)}`;

    const output = await complete(prompt);

    const saved = await prisma.aiInsight.create({
      data: {
        type: "dashboard_insight",
        prompt,
        output,
        context: toJson(context),
        model: env.OPENAI_MODEL,
      },
    });

    return {
      id: saved.id,
      type: saved.type,
      output: saved.output,
      model: saved.model,
      createdAt: saved.createdAt,
    };
  },

  async generateProjectPlan(projectId: string) {
    const project = await prisma.project.findUnique({
      where: { id: projectId },
      include: { tasks: true },
    });

    if (!project) {
      throw new HttpError(404, "Project not found");
    }

    const prompt = `Você é um tech lead e product strategist.

Crie um plano de execução, em markdown, para o projeto abaixo:

${JSON.stringify(project, null, 2)}

A resposta deve conter:

1. Diagnóstico do projeto
2. Roadmap por etapas
3. Tarefas técnicas
4. Melhorias de UI/UX
5. Riscos
6. Critérios de aceite
7. Próximo passo mais importante

Responda em português brasileiro.`;

    const output = await complete(prompt);

    const saved = await prisma.aiInsight.create({
      data: {
        type: "project_plan",
        prompt,
        output,
        context: toJson({ projectId, project }),
        model: env.OPENAI_MODEL,
      },
    });

    return {
      id: saved.id,
      type: saved.type,
      projectId,
      output: saved.output,
      model: saved.model,
      createdAt: saved.createdAt,
    };
  },

  async listInsights() {
    return prisma.aiInsight.findMany({
      orderBy: { createdAt: "desc" },
      take: 30,
    });
  },
};
