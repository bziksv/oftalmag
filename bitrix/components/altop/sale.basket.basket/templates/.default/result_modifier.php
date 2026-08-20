<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/ArticleProperty.php";

\Oftalmag\ArticleProperty::normalizeBasketGrid($arResult);
