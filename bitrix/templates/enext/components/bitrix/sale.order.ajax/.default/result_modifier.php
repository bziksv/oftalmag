<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/ArticleProperty.php";

\Oftalmag\ArticleProperty::normalizeBasketGrid($arResult);
if(!empty($arResult["JS_DATA"]) && is_array($arResult["JS_DATA"])) {
	\Oftalmag\ArticleProperty::normalizeBasketGrid($arResult["JS_DATA"]);
}

$component = $this->__component;
$component::scaleImages($arResult["JS_DATA"], $arParams["SERVICES_IMAGES_SCALING"]);

if(Bitrix\Main\Loader::includeModule("currency")) {
	CJSCore::Init(array("currency")); 
	$currencyFormat = CCurrencyLang::GetFormatDescription($arResult["BASE_LANG_CURRENCY"]);?>

	<script type="text/javascript">
		BX.Currency.setCurrencyFormat('<?=$arResult["BASE_LANG_CURRENCY"]?>', <?=CUtil::PhpToJSObject($currencyFormat, false, true, true)?>);
	</script>
<?}