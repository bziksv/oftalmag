#!/usr/bin/env bash
# Обновляет href на политику cookie в попапе niges.cookiesaccept (текст не меняется).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="$ROOT/.local/db.env"

if [[ -f "$ENV_FILE" ]]; then
	# shellcheck disable=SC1090
	source "$ENV_FILE"
fi

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_NAME="${DB_NAME:-oftalmag_ru}"
DB_LOGIN="${DB_LOGIN:?DB_LOGIN is required}"
DB_PASSWORD="${DB_PASSWORD:?DB_PASSWORD is required}"

export DB_HOST DB_PORT DB_NAME DB_LOGIN DB_PASSWORD
php "$ROOT/scripts/update-nca-cookie-link.php"
