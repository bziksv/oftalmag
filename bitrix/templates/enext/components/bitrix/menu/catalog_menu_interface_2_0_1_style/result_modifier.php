<?
global $arSettings;
$arSettings = $APPLICATION->IncludeComponent("altop:settings.enext", "", array(), false, array("HIDE_ICONS" => "Y"));

$arSection = getCurrSection();
		
$contentAttrSections = [];
foreach($arResult as $arItem) 
{
	if($arItem["SELECTED"]) {
		$contentAttrSections = $arItem["PARAMS"]["CONTENT_ATTR"];	
		break;
	}
}

$uf_iblock_id = 26;
$uf_name = Array("UF_NAME_MENU");

//$uf_section_id = $matches[1];

if(CModule::IncludeModule("iblock")): 
	$uf_arresult = CIBlockSection::GetList(Array("SORT"=>"­­ASC"), Array("IBLOCK_ID" => $uf_iblock_id), false, $uf_name);
	while($uf_value = $uf_arresult->GetNext()):
		if($uf_value["UF_NAME_MENU"]): 
			$nameMenu[$uf_value["CODE"]] = $uf_value["UF_NAME_MENU"];
		endif;
	endwhile;
endif;


foreach($arResult as $key => &$arItem)
{

	$arCode = explode('/',$arItem['LINK']);
	$thisCode = $arCode[count($arCode)-2];

	if($nameMenu[$thisCode]){
		$arResult[$key]["TEXT"] = $nameMenu[$thisCode];
	}

	$arItem["SHOW"] = true;
	
	if(in_array($arItem["PARAMS"]["ID"], $contentAttrSections) ) {
//if(in_array($arItem["PARAMS"]["ID"], $contentAttrSections) || $arSettings["SEARCH_ENGINE_MENU_HIDE"]["VALUE"] === "Y") {
		$arItem["SHOW"] = false;
	}

	$arResult[$key]["PARAMS"]["URL"] = $arSection["URL"];


    if($arSettings["SEARCH_ENGINE_MENU_HIDE"]["VALUE"] === "Y"){
	//if($arSection["UF_HIDE_MENU_INDEX"]){
        $arResult[$key]["PARAMS"]["HIDE_MENU_INDEX"] = true;   
    }else{
        $arResult[$key]["PARAMS"]["HIDE_MENU_INDEX"] = false;
    }

}