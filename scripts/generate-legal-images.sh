#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

if ! curl -fsS "http://127.0.0.1:8087/legal/export/?doc=cookie" >/dev/null; then
  echo "Local site is not available at http://127.0.0.1:8087" >&2
  exit 1
fi

cd "$ROOT/scripts"
npm install --no-fund --no-audit
npx playwright install chromium
node generate-legal-images.mjs
