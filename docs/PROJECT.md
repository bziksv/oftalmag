# oftalmag.ru — документация проекта

Bitrix-интернет-магазин офтальмологического оборудования (шаблон **enext**, модуль **altop.enext**).

## Репозиторий и окружения

| | |
|---|---|
| GitHub | https://github.com/bziksv/oftalmag |
| **Git root** | `oftalmag.ru/` (= корень сайта на prod) |
| Prod IP | `45.90.35.63` (SSH: `almamed`) |
| Prod path | `/var/www/oftalmag_ru_usr/data/www/oftalmag.ru` |
| Домен | https://oftalmag.ru |
| Локально | http://127.0.0.1:8087/ |

Родительская папка `oftalmag/` — **не в git**: дампы БД, `.cursorignore`.

## Структура

```
oftalmag/                      # workspace (Mac)
├── oftalmag_ru.sql            # дамп БД (не в git)
├── oftalmag_ru.sql.gz
├── .cursorignore
└── oftalmag.ru/               # git root
    ├── .cursor/rules/         # правила Cursor
    ├── .local/                # nginx/php-fpm (Mac)
    ├── docs/                  # документация
    ├── scripts/               # dev + deploy
    ├── bitrix/
    ├── upload/                # не в git
    └── personal/
```

## Git — что в репозитории

**В git:** код сайта, `bitrix/modules/`, шаблоны, компоненты, `scripts/`, `docs/`, `.local/*.example`.

**Не в git:** `upload/`, кэш Bitrix, секреты (`.settings.php`, `dbconn.php`, `license_key.php`, `.htaccess`), дампы `*.sql`.

## Локальная разработка (Mac, soft)

Порты: **8087** (nginx), **9087** (php-fpm). MySQL 3306 (Homebrew `mysql@8.0`).

```bash
cd oftalmag.ru
cp .local/db.env.example .local/db.env   # один раз
./scripts/setup-local-db.sh --background # один раз, импорт дампа
./scripts/start-dev.sh
./scripts/stop-dev.sh
```

Soft-режим: php-fpm `ondemand`, max 2 workers, 256M RAM.

### Занятые порты (соседние проекты)

| Порт | Проект |
|------|--------|
| 8080 | almamed |
| 8082 | vilmed |
| 8084 | polimer |
| 8085 | lormag |
| 8086 | metplus-vrn |
| **8087** | **oftalmag** |
| 8088 | metprof-vrn |

## Деплой на prod

**Git после правок — всегда.** **Prod — только по явной просьбе.**

1. Обычно: `git commit` + `git push origin main` (на сервер **не** трогаем).
2. Когда попросили выкатить: `./scripts/deploy-prod.sh` (pull с GitHub на prod).

```bash
cd oftalmag.ru
git add … && git commit -m "…" && git push origin main   # после правок

# только когда пользователь просит:
./scripts/deploy-prod.sh
```

**Запрещено:** автодеплой на prod, правки на prod без commit, `scp` файлов кода.

## Личный кабинет (ЧПУ)

Компонент `bitrix:sale.personal.section` с SEF в `/personal/index.php`.

Обязательные правила в `urlrewrite.php`:

```php
'#^/personal/order/#' → /personal/order/index.php
'#^/personal/#'       → /personal/index.php
```

Маршруты: `/personal/orders/`, `/personal/subscribe/`, `/personal/private/`.  
Корзина `/personal/cart/` — отдельный физический `index.php`.

## Кастомные модули (примеры)

- `altop.enext` — шаблон/магазин
- `niges.cookiesaccept`, `sng.secure`, `prime.cleaner`
- `arturgolubev.cssinliner`

## Проверка после изменений

```bash
curl -sS -o /dev/null -w '%{http_code}\n' https://oftalmag.ru/
curl -sS -o /dev/null -w '%{http_code}\n' https://oftalmag.ru/personal/orders/
```

Локально: `./scripts/start-dev.sh` и curl на `:8087`.

## Шаблон enext: CSS

На **prod** Bitrix подключает `template_styles.min.css`, локально часто — `template_styles.css`.

Если правили `bitrix/templates/enext/template_styles.css`, **обязательно пересобрать min**:

```bash
cd oftalmag.ru
npx clean-css-cli -o bitrix/templates/enext/template_styles.min.css bitrix/templates/enext/template_styles.css
```

Иначе на prod ломается вёрстка (пример: popup «Выбор города» без `display:none`).
