<?php 

foreach ($arResult["ITEMS"] as &$arItem) {
	if ($arItem["PROPERTIES"]["PICTURE_MOBILE"]) {
		$arItem["MOBILE_PICTURE"] = CFile::GetFileArray($arItem["PROPERTIES"]["PICTURE_MOBILE"]["VALUE"]);
	}
}