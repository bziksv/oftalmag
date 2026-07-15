# oftalmag.ru

Сайт на **1C-Bitrix** — интернет-магазин офтальмологического оборудования.

Полная документация: **[docs/PROJECT.md](docs/PROJECT.md)**

## Быстрый старт

| | |
|---|---|
| GitHub | https://github.com/bziksv/oftalmag |
| Prod | `ssh almamed` → `/var/www/oftalmag_ru_usr/data/www/oftalmag.ru` |
| Локально | http://127.0.0.1:8087/ |

```bash
# локальная разработка
cp .local/db.env.example .local/db.env
./scripts/setup-local-db.sh --background
./scripts/start-dev.sh

# деплой
git push origin main
./scripts/deploy-prod.sh
```

Git root = **эта папка** (как на сервере). `bitrix/modules/` — в git.
