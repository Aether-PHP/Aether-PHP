#!/usr/bin/env bash
#
# Aether-PHP installer
#

set -Eeuo pipefail

REPO_URL="https://github.com/Aether-PHP/Aether-PHP.git"
CLONE_DIR="Aether-PHP"

# - Available choices (path relative to the project root)
SERVICES_AVAILABLE=("Database" "Event" "Task" "Http/Auth" "Http/Session")
MODULES_AVAILABLE=("I18n" "Analytics" "CLI")

# - Outputs usable later in the script (space separated)
SELECTED_SERVICES=""
SELECTED_MODULES=""
APP_NAME=""
APP_VERSION=""

if [[ -t 1 ]]; then
    C_RESET=$'\033[0m'
    C_BOLD=$'\033[1m'
    C_RED=$'\033[31m'
    C_GREEN=$'\033[32m'
    C_YELLOW=$'\033[33m'
    C_BLUE=$'\033[38;5;105m'
else
    C_RESET="" C_BOLD="" C_RED="" C_GREEN="" C_YELLOW="" C_BLUE=""
fi

info() { printf '%s[*]%s %s\n' "$C_BLUE" "$C_RESET" "$*"; }
ok() { printf '%s[+]%s %s\n' "$C_GREEN" "$C_RESET" "$*"; }
warn() { printf '%s[!]%s %s\n' "$C_YELLOW" "$C_RESET" "$*"; }
die() { printf '%s[x]%s %s\n' "$C_RED" "$C_RESET" "$*" >&2; exit 1; }
title() { printf '\n%s%s== %s ==%s\n' "$C_BOLD" "$C_BLUE" "$*" "$C_RESET"; }

trap 'die "Installation failed at line $LINENO."' ERR

# - Prompts read from the terminal so the installer also works through "curl | bash"
if [[ -r /dev/tty ]]; then
    TTY_IN="/dev/tty"
else
    TTY_IN="/dev/stdin"
fi

# ask <var> <label> [default]
ask() {
    local _var="$1" _label="$2" _default="${3:-}" _answer=""
    if [[ -n "$_default" ]]; then
        read -r -p "    $_label [$_default]: " _answer < "$TTY_IN" || true
    else
        read -r -p "    $_label: " _answer < "$TTY_IN" || true
    fi
    printf -v "$_var" '%s' "${_answer:-$_default}"
}

# ask_secret <var> <label> [default]
ask_secret() {
    local _var="$1" _label="$2" _default="${3:-}" _answer=""
    if [[ -n "$_default" ]]; then
        read -r -s -p "    $_label [hidden, enter = keep default]: " _answer < "$TTY_IN" || true
    else
        read -r -s -p "    $_label [hidden]: " _answer < "$TTY_IN" || true
    fi
    printf '\n'
    printf -v "$_var" '%s' "${_answer:-$_default}"
}

# ask_yes_no <label> [default y|n] -> returns 0 for yes
ask_yes_no() {
    local _label="$1" _default="${2:-y}" _answer="" _hint="Y/n"
    [[ "$_default" == "n" ]] && _hint="y/N"
    while true; do
        read -r -p "    $_label [$_hint]: " _answer < "$TTY_IN" || true
        _answer="${_answer:-$_default}"
        case "${_answer,,}" in
            y|yes|o|oui) return 0 ;;
            n|no|non) return 1 ;;
            *) warn "Please answer y or n." ;;
        esac
    done
}

# select_items <result_var> <item>...
select_items() {
    local _result="$1" _item _picked=()
    shift
    for _item in "$@"; do
        if ask_yes_no "Install ${_item}?" "y"; then
            _picked+=("$_item")
        fi
    done
    printf -v "$_result" '%s' "${_picked[*]:-}"
}

random_hex() {
    if command -v openssl > /dev/null 2>&1; then
        openssl rand -hex "$1"
    else
        head -c "$1" /dev/urandom | od -An -tx1 | tr -d ' \n'
    fi
}

# env_set <file> <key> <value> : replaces the value of KEY= without any shell/sed escaping issue
env_set() {
    local _file="$1" _key="$2" _tmp
    _tmp="$(mktemp)"
    ENV_VALUE="$3" awk -v key="$_key" '
        index($0, key "=") == 1 { print key "=" ENVIRON["ENV_VALUE"]; next }
        { print }
    ' "$_file" > "$_tmp"
    cat "$_tmp" > "$_file"
    rm -f "$_tmp"
}

# env_default <key> : fallback value when the .env.example leaves a key empty
env_default() {
    case "$1" in
        PROJECT_NAME) printf '%s' "$APP_NAME" ;;
        DATABASE_ADDRESS) printf '%s' "127.0.0.1" ;;
        DATABASE_USERNAME) printf '%s' "root" ;;
        COOKIE_SESSION_TTL) printf '%s' "3600" ;;
        SESSION_FOLDER_PATH) printf '%s' "storage/sessions" ;;
        SESSION_HMAC) random_hex 32 ;;
        SESSION_COOKIE_SECURE) printf '%s' "true" ;;
        ENABLE_ROUTE_CACHING) printf '%s' "false" ;;
        RATELIMIT_SECOND_INTERVAL) printf '%s' "60" ;;
        RATELIMIT_MAX_LIMIT) printf '%s' "100" ;;
        MAINTENANCE) printf '%s' "false" ;;
        REDIS_ADDRESS) printf '%s' "127.0.0.1" ;;
        REDIS_PORT) printf '%s' "6379" ;;
        JWT_SECRET) random_hex 32 ;;
        JWT_TTL) printf '%s' "3600" ;;
        HTTP_FOLLOW_REDIRECTS) printf '%s' "false" ;;
        HTTP_ALLOW_PRIVATE) printf '%s' "false" ;;
        *) printf '%s' "" ;;
    esac
}

is_secret() {
    case "$1" in
        *PASSWORD*|*SECRET*|*HMAC*) return 0 ;;
        *) return 1 ;;
    esac
}

write_default_env_example() {
    cat > "$1" <<'EOF'
# -- Warning : DO NOT COMMIT THIS FILE IN PRODUCTION ENV !

# - Project Details
PROJECT_NAME=

# - Database IDs
DATABASE_ADDRESS=
DATABASE_USERNAME=
DATABASE_PASSWORD=

# - Authentification
AUTH_DATABASE_GATEWAY=
AUTH_TABLE_GATEWAY=

# - Sessions and Cookies
COOKIE_SESSION_TTL=
SESSION_FOLDER_PATH=
SESSION_HMAC=
SESSION_COOKIE_SECURE=

# - Caching
ENABLE_ROUTE_CACHING=

# - Ratelimit Middleware
RATELIMIT_SECOND_INTERVAL=
RATELIMIT_MAX_LIMIT=

# - CI/CD
MAINTENANCE=
REDIS_ADDRESS=
REDIS_PORT=

# - JWT
JWT_SECRET=
JWT_TTL=

# - HTTP Client / SSRF
HTTP_FOLLOW_REDIRECTS=
HTTP_ALLOW_PRIVATE=
EOF
}

write_htaccess() {
    cat > "$1" <<'EOF'
RewriteEngine On

# Bloque tous les .php sauf index.php
<FilesMatch "\.php$">
    Require all denied
</FilesMatch>

<Files "index.php">
    Require all granted
</Files>

# Front Controller
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]

DirectorySlash Off

# Fichiers sensibles
<FilesMatch "^\.env|^.ht">
    Require all denied
</FilesMatch>
<FilesMatch "^\.sql">
    Order allow,deny
    Deny from all
</FilesMatch>
EOF
}

# ---------------------------------------------------------------------------
# 1. ASCII banner
# ---------------------------------------------------------------------------
show_banner() {
    printf '%s' "$C_BLUE"
    cat <<'EOF'

      █████╗ ███████╗████████╗██╗  ██╗███████╗██████╗         ██████╗ ██╗  ██╗██████╗
     ██╔══██╗██╔════╝╚══██╔══╝██║  ██║██╔════╝██╔══██╗        ██╔══██╗██║  ██║██╔══██╗
     ███████║█████╗     ██║   ███████║█████╗  ██████╔╝ █████╗ ██████╔╝███████║██████╔╝
     ██╔══██║██╔══╝     ██║   ██╔══██║██╔══╝  ██╔══██╗ ╚════╝ ██╔═══╝ ██╔══██║██╔═══╝
     ██║  ██║███████╗   ██║   ██║  ██║███████╗██║  ██║        ██║     ██║  ██║██║
     ╚═╝  ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝        ╚═╝     ╚═╝  ╚═╝╚═╝
EOF
    printf '%s\n          The divine secure and lightweight PHP framework - Installer\n\n' "$C_RESET"
}

# ---------------------------------------------------------------------------
# 2. Clone
# ---------------------------------------------------------------------------
clone_repository() {
    title "Cloning Aether-PHP"
    command -v git > /dev/null 2>&1 || die "git is required but not installed."
    [[ -e "$CLONE_DIR" ]] && die "./$CLONE_DIR already exists, remove or rename it first."
    git clone --depth 1 "$REPO_URL" "$CLONE_DIR"
    ok "Repository cloned in ./$CLONE_DIR"
}

# ---------------------------------------------------------------------------
# 3. Basic questions
# ---------------------------------------------------------------------------
ask_basics() {
    title "Application"
    while true; do
        ask APP_NAME "App name (letters, digits, - and _)"
        if [[ "$APP_NAME" =~ ^[A-Za-z0-9][A-Za-z0-9_-]*$ ]]; then
            [[ -e "$APP_NAME" ]] && { warn "./$APP_NAME already exists, pick another name."; continue; }
            break
        fi
        warn "Invalid app name."
    done
    ask APP_VERSION "App version" "1.0.0"
}

# ---------------------------------------------------------------------------
# 4. Services / 5. Modules
# ---------------------------------------------------------------------------
ask_services() {
    title "Services"
    select_items SELECTED_SERVICES "${SERVICES_AVAILABLE[@]}"
    info "Selected services: ${SELECTED_SERVICES:-none}"
}

ask_modules() {
    title "Modules"
    select_items SELECTED_MODULES "${MODULES_AVAILABLE[@]}"
    info "Selected modules: ${SELECTED_MODULES:-none}"
}

# Removes what the user did not select from the freshly cloned project
prune_unselected() {
    local _item _path

    for _item in "${SERVICES_AVAILABLE[@]}"; do
        [[ " $SELECTED_SERVICES " == *" $_item "* ]] && continue
        _path="$CLONE_DIR/src/Aether/$_item"
        [[ -e "$_path" ]] && rm -rf "$_path" && info "Removed service $_item"
    done

    for _item in "${MODULES_AVAILABLE[@]}"; do
        [[ " $SELECTED_MODULES " == *" $_item "* ]] && continue
        if [[ "$_item" == "CLI" ]]; then
            rm -rf "$CLONE_DIR/modules/AetherCLI" "$CLONE_DIR/bin/aether"
        else
            rm -rf "$CLONE_DIR/modules/$_item"
        fi
        info "Removed module $_item"
    done
}

# ---------------------------------------------------------------------------
# 6. .env
# ---------------------------------------------------------------------------
fill_env() {
    title "Environment (.env)"
    local _example="$CLONE_DIR/.env.example" _line _key _current _default _value

    if [[ ! -f "$_example" ]]; then
        warn ".env.example not found in the repository, using the built-in template."
        write_default_env_example "$_example"
    fi

    while IFS= read -r _line || [[ -n "$_line" ]]; do
        if [[ "$_line" =~ ^#\ -\ (.+)$ ]]; then
            printf '\n  %s%s%s\n' "$C_BOLD" "${BASH_REMATCH[1]}" "$C_RESET"
            continue
        fi
        [[ "$_line" =~ ^([A-Z][A-Z0-9_]*)=(.*)$ ]] || continue
        _key="${BASH_REMATCH[1]}"
        _current="${BASH_REMATCH[2]}"
        _default="${_current:-$(env_default "$_key")}"
        if is_secret "$_key"; then
            ask_secret _value "$_key" "$_default"
        else
            ask _value "$_key" "$_default"
        fi
        env_set "$_example" "$_key" "$_value"
    done < "$_example"

    mv "$_example" "$CLONE_DIR/.env"
    chmod 600 "$CLONE_DIR/.env"
    ok ".env.example moved to .env"
}

# ---------------------------------------------------------------------------
# 7. .htaccess
# ---------------------------------------------------------------------------
setup_htaccess() {
    title "Apache"
    mkdir -p "$CLONE_DIR/public"
    write_htaccess "$CLONE_DIR/public/.htaccess"
    ok "public/.htaccess configured"
}

# ---------------------------------------------------------------------------
# 8. Rename
# ---------------------------------------------------------------------------
rename_project() {
    title "Finalizing"
    mv "$CLONE_DIR" "$APP_NAME"
    ok "Project directory renamed to ./$APP_NAME"
}

main() {
    show_banner
    clone_repository
    ask_basics
    ask_services
    ask_modules
    prune_unselected
    fill_env
    setup_htaccess
    rename_project

    printf '\n%s%s%s v%s is ready in ./%s%s\n' "$C_BOLD" "$C_GREEN" "$APP_NAME" "$APP_VERSION" "$APP_NAME" "$C_RESET"
    printf '    Services : %s\n    Modules  : %s\n' "${SELECTED_SERVICES:-none}" "${SELECTED_MODULES:-none}"
}

main "$@"
