# ADR-002 — Shell do produto: navegação, tema e desnichamento

**Status:** aceito · **Data:** 2026-07-22
**Origem:** [`BRIEF_SOL_REDESIGN.md`](../BRIEF_SOL_REDESIGN.md) (Documento 2)

## Decisões

1. **Navegação por resultado** (ordem fixa):
   `Hoje · Clientes · Vendas · Financeiro · Agenda · Tarefas · Relatórios`.
   *Hoje* é a home de atenção (atrasos, cobranças, follow-ups, agenda do dia).
   *Database*, *Workflows* e *AI Center* deixam de existir como navegação; IA (Ask Dash)
   vira superfície transversal (atalho global), não página.
2. **Tema claro é o default do produto** (emenda ao §23.4 do PRD). O escuro permanece
   como preferência do usuário e asset de marketing/demo. Racional: público-alvo é dono
   de pequena empresa em horário comercial; estética dark-terminal lê como ferramenta de
   dev e viola o §5.3 ("não parecer ferramenta exclusiva para programadores").
3. **Core horizontal, go-to-market vertical:** nada de lógica hardcoded por segmento.
   Segmento entra por template de onboarding (pipeline, categorias financeiras, tipos de
   evento) e por **labels renomeáveis por organização** (ex.: "Pacientes" numa clínica;
   entidade interna continua `Customer`).
4. **Strings do frontend centralizadas desde o shell novo** (pt-BR único idioma ativo).
   Pré-requisito barato para labels por segmento e para i18n futura.
5. **Comunicação modelada como inbox multicanal** — WhatsApp é o primeiro canal, não a
   identidade do módulo (registro manual de interações no MVP; integrações depois).

## Consequências

- Rotas/páginas novas no frontend; páginas 100% estáticas da V1 são removidas.
- Tokens de design: base clara nova; a paleta atual vira o tema escuro opcional.
- Tipografia: Inter para UI; mono permanece só para dados tabulares/código.
- As 8 funcionalidades de negócio priorizadas no brief (§5) entram no roadmap nas fases
  ali indicadas (Pix/link de pagamento na R5; importação IA na R2; portal do cliente
  decidido cedo por afetar o modelo de dados).
