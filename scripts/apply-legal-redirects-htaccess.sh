#!/bin/sh
# Вставляет или обновляет блок legal-редиректов в .htaccess (prod Apache).
set -e
cd "$(dirname "$0")/.."
HTACCESS="$PWD/.htaccess"
SNIPPET="$PWD/scripts/htaccess-legal-redirects.snippet"

if [ ! -f "$HTACCESS" ]; then
	echo "Missing $HTACCESS" >&2
	exit 1
fi

if [ ! -f "$SNIPPET" ]; then
	echo "Missing $SNIPPET" >&2
	exit 1
fi

HTACCESS="$HTACCESS" SNIPPET="$SNIPPET" python3 <<'PY'
import os
from pathlib import Path

htaccess = Path(os.environ['HTACCESS'])
snippet = Path(os.environ['SNIPPET']).read_text(encoding='utf-8').strip() + '\n'
begin = '# BEGIN oftalmag legal redirects'
end = '# END oftalmag legal redirects'

text = htaccess.read_text(encoding='utf-8')
start = text.find(begin)
finish = text.find(end)

if start != -1 and finish != -1:
	finish = text.find('\n', finish)
	if finish == -1:
		finish = len(text)
	else:
		finish += 1
	text = text[:start] + snippet + text[finish:]
else:
	if not text.endswith('\n'):
		text += '\n'
	text += '\n' + snippet

htaccess.write_text(text, encoding='utf-8')
print(f'Updated {htaccess}')
PY
