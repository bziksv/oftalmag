<?
$MESS["ARTURGOLUBEV_CSSINLINER_ERROS_SETTING_TITLE"] = "Для эффективной работы решения:<br>";
$MESS["ARTURGOLUBEV_CSSINLINER_MAINMODULE_OPTIMIZE_CSS"] = "В настройках <a href=\"/bitrix/admin/settings.php?lang=ru&mid=main\" target=\"_blank\">главного модуля</a> отключите параметр \"Объединять CSS файлы\"";
$MESS["ARTURGOLUBEV_CSSINLINER_NOTE_MAIN_JS"] = "В настройках <a href=\"/bitrix/admin/settings.php?lang=ru&mid=main\" target=\"_blank\">главного модуля</a> включите параметр \"Переместить весь Javascript в конец страницы\"";

$MESS["ARTURGOLUBEV_CSSINLINER_NOTE_CWEBP_NOT_FOUND"] = "Серверное расширение cwebp не найдено. Обратитесь к системному администратору или в поддержку хостинга";

$MESS["ARTURGOLUBEV_CSSINLINER_CSS_INLINE_TAB"] = "Общие настройки";
$MESS["ARTURGOLUBEV_CSSINLINER_SETTING_FOR"] = "Настройки для сайта ";


$MESS["ARTURGOLUBEV_CSSINLINER_ENABLE"] = "Модуль отключен:";
$MESS["ARTURGOLUBEV_CSSINLINER_DISABLED_SITE"] = "Отключить для сайта";

$MESS["ARTURGOLUBEV_CSSINLINER_WEBP_MAIN_SETTINGS"] = "Параметры оптимизации WebP";

$MESS["ARTURGOLUBEV_CSSINLINER_IMAGE_OPTIMIZATION"] = "Ускорение Изображений";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_OPTIMIZE"] = "Базовая оптимизация:";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_OPTIMIZE_VALUE"] = "перед включением оптимизации WebP рекомендуется провести <a href=\"/bitrix/admin/arturgolubev_cssinliner_image_optimize.php\" target=\"_blank\">предварительную конвертацию изображений</a>";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP"] = "<span data-hint='Изображения конвертируются библиотекой cwebp. После включения опции какое то время сайт будет работать чуть медленне обычного - решение будет налету конвертировать изображения которые встречаются на сайте и кешировать'></span>Включить оптимизацию webp:";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_PATH"] = "<span data-hint='Данную опцию нужно заполнять только если у вас не стандартный путь к библиотеке webp в формате /opt/alt/libwebp/bin/cwebp'></span>Кастомный путь cwebp:";
$MESS["ARTURGOLUBEV_CSSINLINER_WEBP_CHECK_TYPE"] = "<span data-hint='В данной опции выбирается в какой момент формируютя webp-изображений. В момент создания страницы или отдельным запросом. В обе конвертации включен механизм защиты от долгих загрузок, поэтому изображений которые встречает решение могут конвертироваться постепенно.'></span>Механизм конвертации:";
$MESS["ARTURGOLUBEV_CSSINLINER_WEBP_CHECK_TYPE_POST"] = "Отдельным запросом";
$MESS["ARTURGOLUBEV_CSSINLINER_WEBP_CHECK_TYPE_HIT"] = "При формировании страницы";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_ATTR"] = "<span data-hint='В данной опции можно указать html атрибуты, где хранятся картинки. Тег img и background-image обрабатываются автоматически, их указывать не надо'></span>Дополнительные атрибуты где содержатся картинки:<br>(каждый с новой строки)";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_ATTR_HINT"] = "Пример значений: data-src, data-bg, data-background";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_REGEX"] = "<span data-hint='В данной опции указываем регулярные выражения без слешей и флагов. В скобки берём путь к изображению. Опция для сложных случаев когда нужно \"зацепить\" картинки в js массиве и т.п.'></span>Регулярные выражения, для доп. поиска изображений:<br>(каждый с новой строки)";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_REGEX_HINT"] = "Пример значений: 'PICTURE':'([^>,]+)'";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_SKIP"] = "<span data-hint='Решение не будет оптимизировать изображения находящиеся в тегах с указанными атрибутами'></span>Атрибуты исключения из оптимизации webp:<br>(каждый с новой строки)";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_SKIP_HINT"] = "Пример значений data-webp-skip";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_ALGORITM"] = "<span data-hint='Потери - видимое ухудшение качества в webp-версии картинки. Обычный - стандартный алгоритм, файлы хорошо сжимаются, потери возможны, но крайне редки. Без потерь - тяжелый алгоритм, требует много ресурсов, большой вес получаемого файла, потери исключены. Почти без потерь - средняя версия алгоритма, вероятность потерь меньше чем в обычном, но вес больше чем в обычном режиме.'></span>Алгоритм оптимизации webp:";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_ALGORITM_LOSSLESS"] = "Без потерь";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_ALGORITM_NEARLESS"] = "Почти без потерь";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_ALGORITM_STANDART"] = "Стандартный (рекомендуется)";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_QT"] = "<span data-hint='Чем выше целевое качество, тем лучше визуально выглядит картинка и выше вес файла'></span>Целевое качество webp:";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_OP"] = "<span data-hint='Чем больше степерь оптимизации, тем меньше вес конечного файла и больше времени на операцию оптимизации'></span>Уровень оптимизации webp:";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_WEBP_OP_REK"] = "(рекомендуется)";

$MESS["ARTURGOLUBEV_CSSINLINER_VIDEO_OPTIMIZATION"] = "Ускорение видео (YouTube и Rutube)";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_VIDEO_OPTIMIZATION"] = "<span data-hint='Youtube и Rutube видео будут заменяться на фасадные объекты'></span>Оптимизировать видео:";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_VIDEO_PREVIEWTYPE"] = "Разрешение превью картинки YouTube:";
$MESS["ARTURGOLUBEV_CSSINLINER_PREVIEW_TYPE_SD"] = "Высокое";
$MESS["ARTURGOLUBEV_CSSINLINER_PREVIEW_TYPE_HQ"] = "Сренее";
$MESS["ARTURGOLUBEV_CSSINLINER_PREVIEW_TYPE_MQ"] = "Низкое";
$MESS["ARTURGOLUBEV_CSSINLINER_PREVIEW_TYPE_MAX"] = "Максимальное";

$MESS["ARTURGOLUBEV_CSSINLINER_JS_OPTIMIZATION"] = "Ускорение JavaScript";
	$MESS["ARTURGOLUBEV_CSSINLINER_ENABLE_JS_OPTIMIZE"] = "Включить оптимизацию JavaScript:";
	$MESS["ARTURGOLUBEV_CSSINLINER_USE_JS_PASSIVE"] = "<span data-hint='Меняет режим слушателей событий для базовых библиотек BX и JQuery'></span>Использовать пассивные прослушиватели событий:";
	
	$MESS["ARTURGOLUBEV_CSSINLINER_LAZY_JS_FILE"] = "Отложенные до загрузки скрипты:";
	$MESS["ARTURGOLUBEV_CSSINLINER_LAZY_JS_FILE_VALUE"] = "<a href=\"/bitrix/admin/fileman_file_edit.php?path=#PATH#&amp;full_src=Y&amp;lang=ru\" target=\"_blank\">редактировать</a>";
	$MESS["ARTURGOLUBEV_CSSINLINER_LAZY_JS_FILE_HINT"] = "Загрузка данного блока скриптов будет происходить сразу после основной загрузки страницы (любой js который можно отложить до полной загрузки страницы и не используется в построении страницы, счётчики метрики/аналитики)";
	
	$MESS["ARTURGOLUBEV_CSSINLINER_ACTION_JS_FILE"] = "Отложенные до взаимодействия скрипты:";
	$MESS["ARTURGOLUBEV_CSSINLINER_ACTION_JS_FILE_VALUE"] = "<a href=\"/bitrix/admin/fileman_file_edit.php?path=#PATH#&amp;full_src=Y&amp;lang=ru\" target=\"_blank\">редактировать</a>";
	$MESS["ARTURGOLUBEV_CSSINLINER_ACTION_JS_FILE_HINT"] = "Загрузка данного блока скриптов будет происходить после взаимодействия пользователя со страницей (например для Jivosite)";
	
	$MESS["ARTURGOLUBEV_CSSINLINER_NOBOT_JS_FILE"] = "Скрытые от ботов скрипты:";
	$MESS["ARTURGOLUBEV_CSSINLINER_NOBOT_JS_FILE_VALUE"] = "<a href=\"/bitrix/admin/fileman_file_edit.php?path=#PATH#&amp;full_src=Y&amp;lang=ru\" target=\"_blank\">редактировать</a>";
	$MESS["ARTURGOLUBEV_CSSINLINER_NOBOT_JS_FILE_HINT"] = "Данные скрипты будут не будут загружаться для бота (например счётчики метрики, аналитики)";
	$MESS["ARTURGOLUBEV_CSSINLINER_BOT_MODE_HTMLCACHE"] = "Для использования <a href=\"/bitrix/admin/composite.php?lang=ru\" target=\"_blank\">композитный режим</a> должен быть отключен";





$MESS["ARTURGOLUBEV_CSSINLINER_WORKING_WITH_STYLE"] = "Ускорение CSS стилей";
$MESS["ARTURGOLUBEV_CSSINLINER_STYLES_WORK_ON"] = "Оптимизировать стили:";
$MESS["ARTURGOLUBEV_CSSINLINER_STYLES_WORK_MODE"] = "Тип ускорения стилей:";
$MESS["ARTURGOLUBEV_CSSINLINER_STYLES_WORK_MODE_INLINE"] = "Инлайн (устаревший режим)";
$MESS["ARTURGOLUBEV_CSSINLINER_STYLES_WORK_MODE_UNITE"] = "Объединение (рекомендуется)";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_COMPRESS"] = "<span data-hint='СтилиОптимизация стилей происходит за счёт удаления символов табуляции, переносов, комментариев и т.д. Минифицированные файлы (содержащие .min) не оптимизируются'></span>Сжимать оптимизируемые стили:";
$MESS["ARTURGOLUBEV_CSSINLINER_OUTER_STYLE_INLINE"] = "<span data-hint='Внешние стили и Google Fonts сохраняюся и подключаются локально'></span>Обрабатывать Google Fonts и Внешние стили:";
$MESS["ARTURGOLUBEV_CSSINLINER_USE_FONT_DISPLAY"] = "<span data-hint='Для подключаемых шрифтов будет устанавливаен параметр font-display: swap'></span>Оптимизировать шрифты:";

$MESS["ARTURGOLUBEV_CSSINLINER_EXCEPTIONS"] = "<span data-hint='В данной опции можно указать стили, которые не требуется обрабатывать. Формат /bitrix/js/tests.banner/style.css'></span>Исключить из обработки стили:<br>(каждый с новой строки)";
$MESS["ARTURGOLUBEV_CSSINLINER_ADMIN_DEBUG"] = "Включить отладку";



/* other optimization */
$MESS["ARTURGOLUBEV_CSSINLINER_MAIN_OPTIMIZATION"] = "Прочие оптимизации";
$MESS["ARTURGOLUBEV_CSSINLINER_DELETE_OPEN_SANS"] = "<span data-hint='Удаляет подключаемый ядром битрикса шрифт Open Sans. Опцию следует использовать если шрифт Open Sans не используется публичной части'></span>Удалять стандартный шрифт Open Sans:";
$MESS["ARTURGOLUBEV_CSSINLINER_PRECONNECT"] = "<span data-hint='Preconnect рекомендуется использовать если на сайте используются скрипты/стили/шрифты с внешних сервисов. Например: Яндекс.Метрика, CDN Jquery, Google Шрифты. В таком случае полезно делать предсоединение к доменам сервисов, указывая в настройке соотвествующие домены: https://mc.yandex.ru , https://ajax.aspnetcdn.com , https://fonts.googleapis.com
'></span>Preconnect: Доменные имена к которым осуществлять предсоедиенение:<br>(каждое с новой строки)";
$MESS["ARTURGOLUBEV_CSSINLINER_PRELOADING"] = "<span data-hint='Preloading рекомендуется использовать Только при необходимости - если Google PageSpeed рекомендует \"Настройте предварительную загрузку ключевых запросов\" к конкретным скриптам указанным в рекомендации. Ресурсы указывайте в формате /bitrix/js/tests.banner/scripts.js'></span>Preloading: Ресурсы, которые нужно предзагружать:<br>(каждое с новой строки)";

/* bot optimization */
$MESS["ARTURGOLUBEV_CSSINLINER_BOT_VERSON"] = "Оптимизации для ботов";
$MESS["ARTURGOLUBEV_CSSINLINER_BOT_CLEAR_SYSTEM"] = "Удалять системные скрипты при визите бота:";

/* aspro optimization */
$MESS["ARTURGOLUBEV_CSSINLINER_ASPRO_OPTIMIZATION"] = "Дополнительные экспериментальные оптимизации";
// $MESS["ARTURGOLUBEV_CSSINLINER_ASPRO_THEME_CACHE"] = "Кешировать запрос к скрипту setTheme.php (beta):";
$MESS["ARTURGOLUBEV_CSSINLINER_ASPRO_SCRIPT_MOOVING"] = "<span data-hint='Решение ищет скрипты с атрибутом data-skip-mooving и переносит их в футер, не нарушая логики выполнения'></span>Перенос заблокированных скриптов в футер (beta):";


$MESS["ARTURGOLUBEV_CSSINLINER_ALLOW_URL_FOPEN_NOT_FOUND"] = "Отключен параметр PHP allow_url_fopen";
$MESS["ARTURGOLUBEV_CSSINLINER_ALLOW_URL_FOPEN_NOT_FOUND_TEXT"] = "Для работы модуля с внешними стилями и Google Fonts требуется установить параметр PHP allow_url_fopen = On. Для настройки рекоммендуется обратиться в поддержку хостинга или к администратору";


$MESS["ARTURGOLUBEV_CSSINLINER_CLEAR_CACHE"] = 'Ускорение загрузки сайта: Настройки решения изменены. Очистите <a target="_blank" href="/bitrix/admin/cache.php?lang=ru&tabControl_active_tab=fedit2">Все страницы HTML кеша</a> и закройте это уведомление';
$MESS["ARTURGOLUBEV_CSSINLINER_CHANGE_WEBP_ALGORITM"] = 'Ускорение загрузки сайта: Алгоритм оптимизации webp изменён. Рекомендуется очистить папку /upload/cssinliner_webp (Будьте внимательны!) и выполнить <a href="/bitrix/admin/arturgolubev_cssinliner_image_optimize.php" target=\"_blank\">оптимизацию изображений</a>';


$MESS["ARTURGOLUBEV_CSSINLINER_DEMO_IS_EXPIRED"] = "Демонстрационный период работы решения закончился. Для дальнейшего использования необходимо приобрести полую версию решения в <a href=\"http://marketplace.1c-bitrix.ru/solutions/arturgolubev.cssinliner/\" target=\"_blank\">marketplace.1c-bitrix.ru</a>";


$MESS["ARTURGOLUBEV_CSSINLINER_USE_INLINE"] = "Подключить таблицы стилей как inline код";

/* optimize page */
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_NO_MODULE"] = "Модуль Ускорение загрузки не найден";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_TITLE"] = "Оптимизация изображений webp";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_DESCRIPTION"] = "
Полезная информация по оптимизации:
<ul>
	<li style='margin-bottom: 5px;'>В результате оптимизации для каждого изображения будет создана оптимизированная .webp копия - для этой операции потребуется свободное место на хостинге, убедитесь что оно есть</li>
	<li style='margin-bottom: 5px;'>Запускать ручной процесс оптимизаци рекомендуется при включения функционала оптимизации webp в решении и при смене алгоритма оптимизации webp. При загрузке новых изображений ручную оптимизацию делать не нужно, изображения загруженные после оптимизации будут обрабатываться автоматически при контакте с пользователем сайта</li>
	<li style='margin-bottom: 5px;'>Исходные файлы в полной безопасности - решение их не редактирует</li>
	<li style='margin-bottom: 5px;'>Оптимизированные изображения хранятся в папке <a href=\"/bitrix/admin/fileman_admin.php?lang=ru&path=%2Fupload%2Fcssinliner_webp\" target=\"_blank\">/upload/cssinliner_webp</a></li>
</ul>
";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_LAST_RUN"] = "Полная оптимизация была выполнена #last#.";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_IMAGE_TABLE_SIZE"] = "В результате сканирования таблицы b_file найдено #full# изображений.";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_START_OPTIMIZE_BTN"] = "Запустить процесс оптимизации";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_START_OPTIMIZE_BTN_NEXT"] = "Продолжить процесс оптимизации";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_STOP_OPTIMIZE_BTN"] = "Остановить процесс оптимизации";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_CURRENT_OF"] = "Проверено #progress# из #full#";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_OPTIMIZED"] = "Оптимизировано #optimized#";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_NOT_OPTIMIZED"] = "Оптимизация не требуется #skiped#";
$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_END"] = "Таблица просканирована, оптимизация завершена";

$MESS["ARTURGOLUBEV_CSSINLINER_OPTIMIZE_PROCESS_START"] = "Идёт оптимизация изображений, ожидайте...";


/* help tab */
$MESS["ARTURGOLUBEV_CSSINLINER_HELP_TAB_ADMIN_INFO"] = "Внимание! Для удобства и безопасности оптимизация страницы не производится если Вы авторизованы как Администратор.";
$MESS["ARTURGOLUBEV_CSSINLINER_HELP_TAB_TITLE"] = "Полезная информация";
$MESS["ARTURGOLUBEV_CSSINLINER_HELP_TAB_VALUE"] = "
Карточка решения на Marketplace - <a href='https://marketplace.1c-bitrix.ru/solutions/arturgolubev.cssinliner/#tab-about-link' target='_blank'>ссылка</a><br/>
Информация по установке - <a href='https://marketplace.1c-bitrix.ru/solutions/arturgolubev.cssinliner/#tab-install-link' target='_blank'>ссылка</a><br/>
Видео-инструкция - <a href='http://arturgolubev.ru/learning/course/index.php?COURSE_ID=7&INDEX=Y' target='_blank'>ссылка</a><br/>
Проверка работы решения - <a href='https://arturgolubev.ru/knowledge/course7/lesson23/' target='_blank'>ссылка</a><br/>
Часто задаваемые вопросы по данному модулю - <a href='http://arturgolubev.ru/learning/course/index.php?COURSE_ID=7&INDEX=Y' target='_blank'>ссылка</a><br/>
Вопросы по покупке, оплате, активации модуля и т.п. - <a href='http://arturgolubev.ru/learning/course/?COURSE_ID=1&INDEX=Y' target='_blank'>ссылка</a><br/>
";
$MESS["ARTURGOLUBEV_CSSINLINER_HELP_CWEBP_VERSION"] = "Версия серверной библиотеки cwebp: ";

/* Решение работает с определенным набором проблем, включая те что видит PageSpeed. Справочник прочих проблем и советы как с ними бороться - <a href=\"https://arturgolubev.ru/blog/poleznoe/spravochnik-problem-pagespeed-insight/\" target=\"_blank\">arturgolubev.ru</a><br/>
---<br/> */
?>