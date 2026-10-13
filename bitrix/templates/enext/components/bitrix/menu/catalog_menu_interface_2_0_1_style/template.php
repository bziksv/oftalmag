<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

global $arSettings;

$isSiteClosed = false;
if(COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings"))
	$isSiteClosed = true;

if(!$isSiteClosed && !empty($arResult)) {
	$css = '';
	foreach($arResult as $arItem) {
		if(!$arItem["SHOW"] || ($arItem["DEPTH_LEVEL"]>1 && $arItem["PARAMS"]["HIDE_MENU_INDEX"] && $arItem["PARAMS"]["URL"]=='product')) {
			$arCode = explode('/',$arItem["LINK"]);
			if(empty($arCode[2])) {
				continue;
			}
			$css .= '.show_'.preg_replace('/[^a-zA-Z0-9_-]/', '', $arCode[2]).':before{content:'.json_encode((string)$arItem["TEXT"], JSON_UNESCAPED_UNICODE).';}';
		}
	}
	if($css !== '') {
		\Bitrix\Main\Page\Asset::getInstance()->addString(
			'<style type="text/css">'.$css.'</style>',
			true,
			\Bitrix\Main\Page\AssetLocation::AFTER_CSS
		);
	}
}
