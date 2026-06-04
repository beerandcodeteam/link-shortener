#!/bin/bash
#
# ralph.sh
#
# Orquestrador que le docs/project-phases.md, quebra em fases,
# e alimenta cada uma ao Codex CLI ou Claude Code para implementacao automatica.
#
# Uso:
#   chmod +x ralph.sh
#   ./ralph.sh [--engine codex|claude] [--provider anthropic|minimax|ollama] [--model NOME] [caminho-do-arquivo]
#
# Exemplos:
#   ./ralph.sh                                      # default: codex
#   ./ralph.sh --engine claude                      # Claude Code (modelo padrao Anthropic)
#   ./ralph.sh --engine claude --provider minimax   # Claude Code falando com MiniMax
#   ./ralph.sh --engine claude --provider ollama    # Claude Code via claude-code-router -> Ollama local
#   ./ralph.sh --engine claude --model claude-sonnet-4-6   # forca um modelo Anthropic
#   ./ralph.sh --from 3                             # comeca na fase 3 (re-roda dela em diante)
#   ./ralph.sh --from phase-03-database            # comeca na fase pelo slug (match parcial)
#
# Pre-requisitos:
#   - Codex: npm install -g @openai/codex + OPENAI_API_KEY
#   - Claude: npm install -g @anthropic-ai/claude-code + ANTHROPIC_API_KEY
#   - MiniMax (--provider minimax): preencha MINIMAX_KEY no .env
#   - Ollama (--provider ollama): npm install -g @musistudio/claude-code-router
#       + Ollama acessivel (no Windows: OLLAMA_HOST=0.0.0.0; ver ~/.claude-code-router/config.json)
#   - Estar na raiz do projeto Laravel (dentro de um repo git)

set -euo pipefail

ENGINE="codex"
PROVIDER="anthropic"
MODEL=""
INPUT_FILE=""
FROM_PHASE=""

while [[ $# -gt 0 ]]; do
  case "$1" in
    --from)
      FROM_PHASE="$2"
      shift 2
      ;;
    --from=*)
      FROM_PHASE="${1#*=}"
      shift
      ;;
    --engine)
      ENGINE="$2"
      shift 2
      ;;
    --engine=*)
      ENGINE="${1#*=}"
      shift
      ;;
    --provider)
      PROVIDER="$2"
      shift 2
      ;;
    --provider=*)
      PROVIDER="${1#*=}"
      shift
      ;;
    --model)
      MODEL="$2"
      shift 2
      ;;
    --model=*)
      MODEL="${1#*=}"
      shift
      ;;
    *)
      INPUT_FILE="$1"
      shift
      ;;
  esac
done

INPUT_FILE="${INPUT_FILE:-docs/project-phases.md}"

if [[ "$ENGINE" != "codex" && "$ENGINE" != "claude" ]]; then
  echo "Engine invalida: $ENGINE. Use 'codex' ou 'claude'."
  exit 1
fi

if [[ "$PROVIDER" != "anthropic" && "$PROVIDER" != "minimax" && "$PROVIDER" != "ollama" ]]; then
  echo "Provider invalido: $PROVIDER. Use 'anthropic', 'minimax' ou 'ollama'."
  exit 1
fi

# Variaveis preenchidas por configure_provider() e consumidas por run_engine().
PROVIDER_BASE_URL=""
PROVIDER_AUTH_TOKEN=""
PROVIDER_API_KEY=""
PROVIDER_MODEL=""
PHASES_DIR=".phases"
LOG_DIR=".phases/logs"
PROMPT_DIR=".phases/prompts"
MANIFEST="$PHASES_DIR/manifest.txt"
PROGRESS_FILE="$PHASES_DIR/.progress"
MAX_RETRIES=2

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log()     { echo -e "${BLUE}[$(date '+%H:%M:%S')]${NC} $1"; }
success() { echo -e "${GREEN}[$(date '+%H:%M:%S')] $1${NC}"; }
warn()    { echo -e "${YELLOW}[$(date '+%H:%M:%S')] $1${NC}"; }
fail()    { echo -e "${RED}[$(date '+%H:%M:%S')] $1${NC}"; }

# Le o stream-json do claude (uma linha JSON por evento) e converte em texto
# legivel (texto, tool calls, thinking, resultados). Linhas nao-JSON passam
# direto. Usado pra popular o log da fase com o trabalho intermediario.
render_stream() {
  python3 /dev/fd/3 3<<'PYEOF'
import sys, json

def summarize(inp):
    if not isinstance(inp, dict):
        return ""
    for key in ("title", "slug", "name", "path", "file_path",
                "command", "description", "url", "query", "prompt"):
        v = inp.get(key)
        if isinstance(v, str) and v.strip():
            v = v.strip().splitlines()[0]
            return v if len(v) <= 100 else v[:97] + "..."
    return ""

def render(ev):
    t = ev.get("type")
    out = []
    if t == "system" and ev.get("subtype") == "init":
        out.append(f"· session iniciada ({ev.get('model', '')}, "
                   f"{len(ev.get('tools') or [])} tools)")
    elif t == "assistant":
        for b in ev.get("message", {}).get("content", []) or []:
            bt = b.get("type")
            if bt == "text":
                txt = (b.get("text") or "").rstrip()
                if txt:
                    out.append(txt)
            elif bt == "tool_use":
                s = summarize(b.get("input"))
                out.append(f"→ {b.get('name', '?')}" + (f" {s}" if s else ""))
            elif bt == "thinking":
                lines = (b.get("thinking") or "").strip().splitlines()
                if lines:
                    out.append(f"… {lines[0][:120]}")
    elif t == "user":
        for b in ev.get("message", {}).get("content", []) or []:
            if b.get("type") != "tool_result":
                continue
            content = b.get("content")
            if isinstance(content, list):
                content = " ".join(
                    c.get("text", "") for c in content
                    if isinstance(c, dict) and c.get("type") == "text"
                )
            preview = ""
            if isinstance(content, str) and content.strip():
                first = content.strip().splitlines()[0]
                preview = first if len(first) <= 120 else first[:117] + "..."
            marker = "✗" if b.get("is_error") else "←"
            out.append(f"{marker} {preview}")
    elif t == "result":
        if ev.get("is_error"):
            out.append(f"✗ erro: {ev.get('result') or ev.get('error') or ''}")
        cost = ev.get("total_cost_usd")
        dur = ev.get("duration_ms")
        if cost is not None and dur is not None:
            out.append(f"· concluído em {dur / 1000:.1f}s · ${cost:.4f}")
    return out

for line in iter(sys.stdin.readline, ''):
    line = line.rstrip("\n")
    if not line:
        continue
    try:
        ev = json.loads(line)
    except json.JSONDecodeError:
        print(line, flush=True)
        continue
    for rendered in render(ev):
        print(rendered, flush=True)
PYEOF
}

format_duration() {
  local total_seconds=$1
  local hours=$((total_seconds / 3600))
  local minutes=$(( (total_seconds % 3600) / 60 ))
  local seconds=$((total_seconds % 60))

  if [ $hours -gt 0 ]; then
    printf "%dh %dm %ds" $hours $minutes $seconds
  elif [ $minutes -gt 0 ]; then
    printf "%dm %ds" $minutes $seconds
  else
    printf "%ds" $seconds
  fi
}

# Le uma unica chave do .env sem dar source no arquivo (evita expansao de ${...}
# e quebra com valores contendo espacos). Remove aspas externas do valor.
read_env() {
  local key="$1"
  [ -f .env ] || return 0
  grep -E "^${key}=" .env | head -1 | cut -d'=' -f2- | sed -e 's/^"//' -e 's/"$//' || true
}

# Descobre o IP onde o Ollama esta acessivel a partir do WSL.
# Tenta localhost (networking mirrored) e cai para o gateway padrao (NAT WSL2).
detect_ollama_host() {
  local port="${1:-11434}"
  if curl -s --max-time 2 "http://localhost:${port}/api/tags" &> /dev/null; then
    echo "localhost"
    return 0
  fi
  local gw
  gw="$(ip route show default 2>/dev/null | awk '/default/ {print $3; exit}')"
  if [ -n "$gw" ] && curl -s --max-time 3 "http://${gw}:${port}/api/tags" &> /dev/null; then
    echo "$gw"
    return 0
  fi
  return 1
}

# Atualiza api_base_url do provider ollama no config do CCR com o IP correto do host.
# Retorna 0 se o arquivo foi alterado (CCR precisa reiniciar).
sync_ollama_host() {
  local cfg="$HOME/.claude-code-router/config.json"
  [ -f "$cfg" ] || return 1
  local host
  host="$(detect_ollama_host 11434)" || {
    fail "Ollama inacessivel via localhost ou gateway WSL. Confira OLLAMA_HOST=0.0.0.0 no Windows."
    exit 1
  }
  local new_url="http://${host}:11434/v1/chat/completions"
  local cur_url
  cur_url="$(grep -oE '"api_base_url"[[:space:]]*:[[:space:]]*"[^"]*"' "$cfg" | head -1 | sed -E 's/.*"([^"]*)"$/\1/')"
  if [ "$cur_url" = "$new_url" ]; then
    return 1
  fi
  log "Ajustando Ollama host no CCR: ${cur_url} -> ${new_url}"
  sed -i -E "s#\"api_base_url\"[[:space:]]*:[[:space:]]*\"[^\"]*\"#\"api_base_url\": \"${new_url}\"#" "$cfg"
  return 0
}

# Garante que o claude-code-router esteja instalado e rodando (usado pelo Ollama).
ensure_ccr() {
  if ! command -v ccr &> /dev/null; then
    fail "claude-code-router (ccr) nao encontrado. Instale: npm install -g @musistudio/claude-code-router"
    exit 1
  fi
  local config_changed=1
  sync_ollama_host && config_changed=0
  if ! ccr status 2>/dev/null | grep -qi "running"; then
    log "Iniciando claude-code-router..."
    ccr start &> /dev/null || true
    sleep 2
  elif [ "$config_changed" -eq 0 ]; then
    log "Reiniciando claude-code-router para aplicar novo IP do Ollama..."
    ccr restart &> /dev/null || { ccr stop &> /dev/null; ccr start &> /dev/null; }
    sleep 2
  fi
}

# Extrai o modelo do Router.default do config do CCR (ex: "ollama,qwen3.6:latest"
# -> "qwen3.6:latest"). Usado so como label honesto no output do claude.
read_ccr_model() {
  local cfg="$HOME/.claude-code-router/config.json"
  [ -f "$cfg" ] || return 0
  grep -oE '"default"[[:space:]]*:[[:space:]]*"[^"]*"' "$cfg" \
    | head -1 \
    | sed -E 's/.*"([^"]*)"$/\1/' \
    | sed -E 's/^[^,]*,//'
}

# Resolve endpoint, auth e modelo do provider escolhido. Falha cedo se faltar dep.
configure_provider() {
  PROVIDER_BASE_URL=""
  PROVIDER_AUTH_TOKEN=""
  PROVIDER_API_KEY=""
  PROVIDER_MODEL="$MODEL"

  case "$PROVIDER" in
    anthropic)
      : # usa endpoint e credenciais padrao do Claude Code
      ;;
    minimax)
      local key
      key="$(read_env MINIMAX_KEY)"
      if [ -z "$key" ]; then
        fail "MINIMAX_KEY vazio no .env. Preencha antes de usar --provider minimax."
        exit 1
      fi
      PROVIDER_BASE_URL="$(read_env MINIMAX_BASE_URL)"
      PROVIDER_BASE_URL="${PROVIDER_BASE_URL:-https://api.minimax.io/anthropic}"
      PROVIDER_AUTH_TOKEN="$key"
      if [ -z "$PROVIDER_MODEL" ]; then
        PROVIDER_MODEL="$(read_env MINIMAX_MODEL)"
        PROVIDER_MODEL="${PROVIDER_MODEL:-MiniMax-M2}"
      fi
      ;;
    ollama)
      ensure_ccr
      PROVIDER_BASE_URL="http://127.0.0.1:3456"
      PROVIDER_API_KEY="ccr" # CCR sem APIKEY ignora, mas o claude exige um token setado
      # O modelo Ollama e controlado pelo Router em ~/.claude-code-router/config.json.
      # CCR ignora o model recebido e usa Router.default, entao passar --model aqui e
      # apenas cosmetico: faz o "session iniciada" mostrar o modelo real em vez de opus.
      if [ -z "$PROVIDER_MODEL" ]; then
        PROVIDER_MODEL="$(read_ccr_model)"
      fi
      ;;
  esac
}

preflight_checks() {
  if [[ "$ENGINE" == "codex" ]]; then
    if ! command -v codex &> /dev/null; then
      fail "codex CLI nao encontrado. Instale com: npm install -g @openai/codex"
      exit 1
    fi
  elif [[ "$ENGINE" == "claude" ]]; then
    if ! command -v claude &> /dev/null; then
      fail "Claude Code CLI nao encontrado. Instale com: npm install -g @anthropic-ai/claude-code"
      exit 1
    fi
    configure_provider
  fi

  if [ ! -f "$INPUT_FILE" ]; then
    fail "Arquivo nao encontrado: $INPUT_FILE"
    exit 1
  fi

  if [ ! -f "artisan" ]; then
    warn "Nao parece ser a raiz de um projeto Laravel (artisan nao encontrado)"
    read -p "Continuar mesmo assim? (y/N) " -n 1 -r
    echo
    [[ $REPLY =~ ^[Yy]$ ]] || exit 1
  fi

  if ! git rev-parse --is-inside-work-tree &> /dev/null 2>&1; then
    fail "Requer um repositorio git."
    exit 1
  fi

  if [[ "$ENGINE" == "claude" ]]; then
    success "Pre-checks OK (engine: $ENGINE, provider: $PROVIDER${PROVIDER_MODEL:+, model: $PROVIDER_MODEL})"
  else
    success "Pre-checks OK (engine: $ENGINE)"
  fi
}

split_phases() {
  log "Quebrando $INPUT_FILE em fases..."

  rm -rf "$PHASES_DIR"
  mkdir -p "$PHASES_DIR" "$LOG_DIR" "$PROMPT_DIR"
  > "$MANIFEST"

  local current_file=""
  local phase_count=0

  while IFS= read -r line || [ -n "$line" ]; do
    if [[ "$line" =~ ^##[[:space:]]+(Phase[[:space:]]+[0-9]+[^#]*) ]]; then
      phase_count=$((phase_count + 1))

      local raw_title="${BASH_REMATCH[1]}"
      raw_title="$(echo "$raw_title" | sed 's/[[:space:]]*$//')"

      local slug
      slug=$(echo "$raw_title" \
        | tr '[:upper:]' '[:lower:]' \
        | sed 's/phase[[:space:]]*/phase-/' \
        | sed 's/[^a-z0-9-]/-/g' \
        | sed 's/--*/-/g' \
        | sed 's/-$//' \
        | sed 's/^-//')
      slug=$(echo "$slug" | sed -E 's/phase-([0-9])$/phase-0\1/' | sed -E 's/phase-([0-9])-/phase-0\1-/')

      current_file="$PHASES_DIR/${slug}.md"
      echo "$line" > "$current_file"
      echo "${slug}.md|${raw_title}" >> "$MANIFEST"
      continue
    fi

    if [ -n "$current_file" ]; then
      echo "$line" >> "$current_file"
    fi
  done < "$INPUT_FILE"

  success "$phase_count fases extraidas"
}

build_prompt_file() {
  local phase_file="$1"
  local prompt_file="$PROMPT_DIR/${phase_file%.md}.txt"

  cat > "$prompt_file" <<PROMPT
Voce e um desenvolvedor Laravel senior.

## Stack do projeto
- Laravel 13, PHP 8.5
- Pest PHP 4 (testes)
- postgresql (via Laravel Sail)
- Livewire 4

## Arquivos de referencia importantes
- Voce tem acesso ao mem0 para entender o contexto do projeto
- docs/project-phases.md — plano completo de fases
- docs/user-stories.md — user stories
- docs/project-description.md — descricao geral
- docs/design-handoff — descricao geral

## Sua tarefa agora
Implemente COMPLETAMENTE a fase descrita abaixo.

Para cada item:
1. Implemente o codigo completo (nao deixe TODOs ou placeholders)
2. Crie os testes listados
3. Rode os testes com o subagent
4. Se um teste falhar, corrija o codigo e rode novamente
5. So passe pro proximo item quando os testes passarem

## Regras obrigatorias
- LEIA o CLAUDE.md antes de comecar — ele contem as convencoes do projeto
- Todos os comandos devem usar ./vendor/bin/sail (Docker/Sail)
- Factories devem criar todas as dependencias (role, user, product, etc.)
- Nomes de classes, arquivos e metodos devem seguir EXATAMENTE o que esta descrito
- Nao pule nenhum item marcado com [ ]
- Ao final utilize o subagent de testes para validar se esta tudo correto

## Fase a implementar
$(cat "$PHASES_DIR/$phase_file")
PROMPT

  echo "$prompt_file"
}

build_retry_prompt_file() {
  local phase_file="$1"
  local test_output="$2"
  local prompt_file="$PROMPT_DIR/${phase_file%.md}-retry.txt"

  cat > "$prompt_file" <<PROMPT
Os testes falharam apos a implementacao anterior. Corrija os erros.

Saida dos testes:
\`\`\`
$test_output
\`\`\`

Corrija o codigo para que todos os testes passem. Rode os testes novamente apos cada correcao.
PROMPT

  echo "$prompt_file"
}

run_engine() {
  local prompt_file="$1"
  local log_file="$2"

  # Exporta contexto da fase atual para os hooks (notify-n8n.sh usa quando .message vem vazio)
  export RALPH_ENGINE="$ENGINE"
  export RALPH_PHASE_TITLE="${RALPH_PHASE_TITLE:-}"
  export RALPH_PHASE_NUM="${RALPH_PHASE_NUM:-}"
  export RALPH_PHASE_TOTAL="${RALPH_PHASE_TOTAL:-}"
  export RALPH_PHASE_ATTEMPT="${RALPH_PHASE_ATTEMPT:-1}"
  export RALPH_PHASE_MAX_ATTEMPTS="$((MAX_RETRIES + 1))"

  if [[ "$ENGINE" == "codex" ]]; then
    cat "$prompt_file" | codex exec --sandbox danger-full-access - 2>&1 | tee "$log_file"
  elif [[ "$ENGINE" == "claude" ]]; then
    # Monta o env do claude conforme o provider. Tokens de provider sobrescrevem
    # as credenciais herdadas, e cada modo de auth desliga o outro.
    local -a claude_env=(-u CLAUDECODE)
    if [ -n "$PROVIDER_AUTH_TOKEN" ]; then
      claude_env+=(-u ANTHROPIC_API_KEY "ANTHROPIC_AUTH_TOKEN=$PROVIDER_AUTH_TOKEN")
    fi
    if [ -n "$PROVIDER_API_KEY" ]; then
      claude_env+=(-u ANTHROPIC_AUTH_TOKEN "ANTHROPIC_API_KEY=$PROVIDER_API_KEY")
    fi
    if [ -n "$PROVIDER_BASE_URL" ]; then
      claude_env+=("ANTHROPIC_BASE_URL=$PROVIDER_BASE_URL")
    fi
    local -a model_arg=()
    if [ -n "$PROVIDER_MODEL" ]; then
      model_arg=(--model "$PROVIDER_MODEL")
    fi
    env "${claude_env[@]}" claude --dangerously-skip-permissions "${model_arg[@]}" -p "$(cat "$prompt_file")" --output-format stream-json --verbose 2>&1 | render_stream | tee "$log_file"
  fi
}

run_phase() {
  local phase_file="$1"
  local phase_title="$2"
  local phase_num="$3"
  local total_phases="$4"
  local log_file="$LOG_DIR/${phase_file%.md}.log"
  local phase_start
  phase_start=$(date +%s)

  export RALPH_PHASE_TITLE="$phase_title"
  export RALPH_PHASE_NUM="$phase_num"
  export RALPH_PHASE_TOTAL="$total_phases"

  echo ""
  log "[$phase_num/$total_phases] $phase_title"

  local attempt=0
  local phase_success=false

  while [ $attempt -le $MAX_RETRIES ]; do
    attempt=$((attempt + 1))
    export RALPH_PHASE_ATTEMPT="$attempt"

    if [ $attempt -gt 1 ]; then
      warn "Tentativa $attempt/$((MAX_RETRIES + 1))..."
    fi

    local prompt_file
    if [ $attempt -eq 1 ]; then
      prompt_file=$(build_prompt_file "$phase_file")
    fi

    if run_engine "$prompt_file" "$log_file"; then
      phase_success=true
      break
    else
      fail "$ENGINE retornou erro"
      if [ $attempt -le $MAX_RETRIES ]; then
        local test_output
        test_output=$(tail -30 "$log_file" 2>/dev/null || echo "Sem output disponivel")
        prompt_file=$(build_retry_prompt_file "$phase_file" "$test_output")
      fi
    fi
  done

  local phase_end
  phase_end=$(date +%s)
  local phase_duration=$((phase_end - phase_start))

  if $phase_success; then
    success "$phase_title — COMPLETA ($(format_duration $phase_duration))"

    if git rev-parse --is-inside-work-tree &> /dev/null 2>&1; then
      git add -A
      git commit -m "feat: $phase_title" --allow-empty
      log "Commit criado no git"
    fi

    echo "$phase_file" >> "$PROGRESS_FILE"
    return 0
  else
    fail "$phase_title — FALHOU apos $((MAX_RETRIES + 1)) tentativas ($(format_duration $phase_duration))"
    fail "Log disponivel em: $log_file"
    return 1
  fi
}

is_phase_done() {
  local phase_file="$1"
  [ -f "$PROGRESS_FILE" ] && grep -qF "$phase_file" "$PROGRESS_FILE"
}

# Resolve --from (numero ou slug parcial) para o numero da fase no manifest.
# Sem --from, FROM_NUM=0 (roda tudo). Falha cedo se o alvo nao existir.
FROM_NUM=0
resolve_from_phase() {
  [ -z "$FROM_PHASE" ] && return 0

  if [[ "$FROM_PHASE" =~ ^[0-9]+$ ]]; then
    FROM_NUM="$FROM_PHASE"
    local total
    total=$(wc -l < "$MANIFEST")
    if [ "$FROM_NUM" -lt 1 ] || [ "$FROM_NUM" -gt "$total" ]; then
      fail "--from $FROM_PHASE fora do intervalo (1..$total)"
      exit 1
    fi
    return 0
  fi

  local n=0 file title
  while IFS="|" read -r file title; do
    n=$((n + 1))
    if [[ "${file%.md}" == *"$FROM_PHASE"* ]]; then
      FROM_NUM="$n"
      return 0
    fi
  done < "$MANIFEST"

  fail "Fase nao encontrada para --from: $FROM_PHASE"
  exit 1
}

main() {
  preflight_checks
  split_phases
  resolve_from_phase

  local total_phases
  total_phases=$(wc -l < "$MANIFEST")

  echo ""
  log "$total_phases fases para implementar"
  if [ "$FROM_NUM" -gt 0 ]; then
    log "Comecando a partir da fase $FROM_NUM (--from)"
  fi
  echo ""

  local num=0
  while IFS="|" read -r file title; do
    num=$((num + 1))
    if [ "$FROM_NUM" -gt 0 ] && [ "$num" -lt "$FROM_NUM" ]; then
      echo -e "  ${BLUE}[$num] $title (pulada: antes de --from)${NC}"
    elif is_phase_done "$file"; then
      echo -e "  ${GREEN}[$num] $title (ja completada)${NC}"
    else
      echo -e "  ${YELLOW}[$num] $title${NC}"
    fi
  done < "$MANIFEST"

  echo ""
  read -p "Iniciar implementacao? (Y/n) " -n 1 -r
  echo
  [[ $REPLY =~ ^[Nn]$ ]] && exit 0

  local start_time
  start_time=$(date +%s)
  log "Inicio: $(date '+%d/%m/%Y %H:%M:%S')"

  local current=0
  local failed_phases=()
  local skipped_phases=()
  local completed_phases=()

  while IFS="|" read -r file title; do
    current=$((current + 1))

    if [ "$FROM_NUM" -gt 0 ] && [ "$current" -lt "$FROM_NUM" ]; then
      log "Pulando $title (antes de --from $FROM_NUM)"
      skipped_phases+=("$title")
      continue
    fi

    if is_phase_done "$file"; then
      log "Pulando $title (ja completada)"
      skipped_phases+=("$title")
      continue
    fi

    if run_phase "$file" "$title" "$current" "$total_phases"; then
      completed_phases+=("$title")
    else
      failed_phases+=("$title")
      echo ""
      warn "Fase falhou: $title"
      read -p "Continuar para a proxima fase? (Y/n) " -n 1 -r
      echo
      [[ $REPLY =~ ^[Nn]$ ]] && break
    fi
  done < "$MANIFEST"

  local end_time
  end_time=$(date +%s)
  local total_duration=$((end_time - start_time))

  echo ""
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
  log "RELATORIO FINAL (engine: $ENGINE)"
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

  if [ ${#completed_phases[@]} -gt 0 ]; then
    echo ""
    success "Completadas (${#completed_phases[@]}):"
    for phase in "${completed_phases[@]}"; do
      echo -e "    ${GREEN}$phase${NC}"
    done
  fi

  if [ ${#skipped_phases[@]} -gt 0 ]; then
    echo ""
    log "Puladas (${#skipped_phases[@]}):"
    for phase in "${skipped_phases[@]}"; do
      echo -e "    $phase"
    done
  fi

  if [ ${#failed_phases[@]} -gt 0 ]; then
    echo ""
    fail "Falharam (${#failed_phases[@]}):"
    for phase in "${failed_phases[@]}"; do
      echo -e "    ${RED}$phase${NC}"
    done
    echo ""
    fail "Verifique os logs em $LOG_DIR/"
  fi

  echo ""
  log "Inicio: $(date -d @$start_time '+%d/%m/%Y %H:%M:%S')"
  log "Fim:    $(date -d @$end_time '+%d/%m/%Y %H:%M:%S')"
  log "Duracao total: $(format_duration $total_duration)"
  echo ""
}

main