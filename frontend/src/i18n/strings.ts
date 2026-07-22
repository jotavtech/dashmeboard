/**
 * Central pt-BR strings for the Business OS shell (ADR-002 §4).
 *
 * Every user-facing string of the new shell lives here so that (a) segment
 * templates can rename entity labels per organization and (b) a future locale
 * can swap the whole dictionary. Legacy pages keep their inline copy until
 * they are rebuilt.
 */
export const strings = {
  product: {
    name: "Dashmeboard",
    tagline: "Clientes, vendas, finanças e tarefas em um só lugar.",
  },
  nav: {
    today: "Hoje",
    customers: "Clientes",
    sales: "Vendas",
    finance: "Financeiro",
    calendar: "Agenda",
    tasks: "Tarefas",
    reports: "Relatórios",
    settings: "Configurações",
    logout: "Sair",
  },
  auth: {
    loginTitle: "Entrar",
    loginSubtitle: "Acesse o painel da sua empresa.",
    registerTitle: "Criar conta",
    registerSubtitle: "Comece a organizar sua empresa em minutos.",
    name: "Seu nome",
    email: "E-mail",
    password: "Senha",
    passwordHint: "Mínimo de 8 caracteres",
    organizationName: "Nome da empresa",
    submitLogin: "Entrar",
    submitRegister: "Criar conta grátis",
    switchToRegister: "Não tem conta? Crie agora",
    switchToLogin: "Já tem conta? Entrar",
    genericError: "Algo deu errado. Tente novamente.",
    invalidCredentials: "E-mail ou senha inválidos.",
    emailTaken: "Este e-mail já está cadastrado.",
    loading: "Carregando…",
  },
  today: {
    greetingMorning: "Bom dia",
    greetingAfternoon: "Boa tarde",
    greetingEvening: "Boa noite",
    subtitle: "Aqui está o que precisa da sua atenção.",
    overdueTasks: "Tarefas atrasadas",
    dueToday: "Para hoje",
    activeProjects: "Projetos ativos",
    weeklyDone: "Concluídas na semana",
    quickStart: "Comece por aqui",
    quickStartHint: "Os módulos do Dashmeboard ficam prontos nesta ordem.",
    emptyAttention: "Nada atrasado. Bom trabalho!",
    seeAll: "Ver tudo",
  },
  modules: {
    comingSoon: "Em construção",
    customers: {
      title: "Clientes",
      description:
        "Cadastro central de clientes com histórico de interações, follow-ups e status — chega de perder cliente no WhatsApp.",
    },
    sales: {
      title: "Vendas",
      description:
        "Funil visual de oportunidades: arraste do primeiro contato ao fechamento e saiba sempre o que está parado.",
    },
    finance: {
      title: "Financeiro",
      description:
        "Contas a receber e a pagar com parcelas, cobranças e visão de fluxo de caixa simplificada.",
    },
    calendar: {
      title: "Agenda",
      description:
        "Compromissos, atendimentos e prazos da empresa em um calendário único, ligado a clientes e tarefas.",
    },
  },
} as const;

export type Strings = typeof strings;
