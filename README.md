# oftalmag.ru

Сайт на **1C-Bitrix** — интернет-магазин офтальмологического оборудования.

## Репозиторий

- GitHub: https://github.com/bziksv/oftalmag

## Сервер (production)

| Параметр | Значение |
|----------|----------|
| IP | `45.90.35.63` |
| Путь на сервере | `/var/www/oftalmag_ru_usr/data/www/oftalmag.ru` |
| Домен | `oftalmag.ru` |

```bash
ssh root@45.90.35.63
cd /var/www/oftalmag_ru_usr/data/www/oftalmag.ru
git pull origin main
```

## Локальная разработка (Mac)

Проектная обёртка (дампы, scripts, nginx/php-fpm) — каталог `../` (родитель `oftalmag/`).

| Сервис | Порт |
|--------|------|
| HTTP (nginx) | **8087** |
| PHP-FPM | **9087** |

```bash
cd ..   # oftalmag/
./scripts/setup-local-db.sh --background   # один раз
./scripts/start-dev.sh
./scripts/stop-dev.sh
```

Сайт: http://127.0.0.1:8087/

## Git

Корень репозитория = **эта папка** (`oftalmag.ru/`), как на сервере.

Секреты (`bitrix/.settings.php`, `dbconn.php`, `license_key.php`) и `upload/` — в `.gitignore`.  
`bitrix/modules/` — в git.
