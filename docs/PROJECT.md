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

Soft-режим: php-fpm `ondemand`, max 4 workers, **512M** RAM (секции каталога ~1MB HTML + cssinliner; при 256M бывают белые страницы).

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
- `prime.alerts` — политика e-mail (`.ru`/`.su`); файлы в `local/modules/prime.alerts/`, настройки: `/bitrix/admin/settings.php?mid=prime.alerts`
- `arturgolubev.cssinliner`

После копирования `prime.alerts` с prod на локалку модуль нужно **установить в БД** (файлов мало): Marketplace/модули → установить, либо `DoInstall()` / запись в `b_module` + обработчики. Иначе в админке «не работает», на витрине нет `PRIME_ALERTS`.

## Инструменты

### Реестр картинок сайта

| | |
|---|---|
| URL | http://127.0.0.1:8087/tools/site-images.php |
| Prod | https://oftalmag.ru/tools/site-images.php |
| Файл | `tools/site-images.php` |

Собирает изображения из `b_file` / `upload/`. В таблице:

- где используется (элемент инфоблока, свойство, UF)
- прямая ссылка на файл (всегда `https://oftalmag.ru/...`)
- кнопка «Скачать»
- даты создания/изменения (файл + БД)
- экспорт в Excel (`?export=excel`)

Доступ: localhost или админ Bitrix.

## Проверка после изменений

```bash
curl -sS -o /dev/null -w '%{http_code}\n' https://oftalmag.ru/
curl -sS -o /dev/null -w '%{http_code}\n' https://oftalmag.ru/personal/orders/
```

Локально: `./scripts/start-dev.sh` и curl на `:8087`.

## Шаблон enext: CSS

На **prod** Bitrix часто подключает `.min.css`, локально — полный `.css`. После правок CSS **обязательно пересобирать min**, иначе на prod старая вёрстка.

```bash
cd oftalmag.ru
npx clean-css-cli -o bitrix/templates/enext/template_styles.min.css bitrix/templates/enext/template_styles.css
npx clean-css-cli -o bitrix/templates/enext/components/bitrix/menu/catalog_menu_interface_2_0_1/style.min.css \
  bitrix/templates/enext/components/bitrix/menu/catalog_menu_interface_2_0_1/style.css
npx clean-css-cli -o bitrix/templates/enext/components/bitrix/catalog/.default/style.min.css \
  bitrix/templates/enext/components/bitrix/catalog/.default/style.css
```

Примеры поломок без пересборки: popup «Выбор города»; дубли шапки (Избранное/Корзина); кривые «Сортировка» / «Вид отображения» в каталоге.

То же для **JS**: на prod Bitrix часто склеивает в `bitrix/cache/js/...` из `script.min.js`, локально — из `script.js`. После правок анимации «в корзину» и т.п. синхронизировать оба:

```bash
npx terser bitrix/templates/enext/components/bitrix/catalog.item/.default/script.js -c -m \
  -o bitrix/templates/enext/components/bitrix/catalog.item/.default/script.min.js
# то же для catalog.item/gift, catalog.element/.default, catalog.element/article
```

После выката на prod сбросить `bitrix/cache/js/*` (делает `deploy-prod.sh`).

## Каталог: сортировка и вид

В панели раздела (`catalog/.default/section_vertical.php`) два блока в **одну строку** (как на lormag):

- сортировка (`data-role="catalogSectionSort"`)
- вид: Плитка / Список / Прайс (`data-role="catalogSectionView"`, `?view=CARD|LIST|PRICE`)

Стили: `.catalog-section-sort-container` — `flex` row + `nowrap`, чтобы панель оставалась компактной; при скролле sticky-панель (`catalog-section-panel.fixed`) садится под шапку. После правок CSS — пересобрать `style.min.css`. Проверять Список и Плитку.

**Важно:** не оставлять глобальные правила вроде `.container-ws { padding-top: … !important }` в компонентах (пример: `news.list/block_brands_catalog`) — они раздувают заголовок раздела до скролла.