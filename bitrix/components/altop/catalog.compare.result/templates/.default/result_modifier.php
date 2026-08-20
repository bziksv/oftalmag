<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

//PRODUCT_URL + ARTICLE//
// Карточки каталога открываются по PROPERTY_code, а не по CODE элемента
// (см. /product/index.php и catalog.item). Артикул — CML2_ARTICLE.
foreach($arResult["ITEMS"] as &$item) {
	if(!empty($item["DETAIL_PAGE_URL"])) {
		$item["DETAIL_PAGE_URL"] = str_replace("catalog/product", "product", $item["DETAIL_PAGE_URL"]);
	}
	$seoCode = !empty($item["PROPERTIES"]["code"]["VALUE"]) ? $item["PROPERTIES"]["code"]["VALUE"] : "";
	if($seoCode !== "") {
		if(!empty($item["CODE"]) && $item["CODE"] !== $seoCode && !empty($item["DETAIL_PAGE_URL"])) {
			$item["DETAIL_PAGE_URL"] = str_replace($item["CODE"], $seoCode, $item["DETAIL_PAGE_URL"]);
		} elseif(empty($item["DETAIL_PAGE_URL"]) || strpos($item["DETAIL_PAGE_URL"], $seoCode) === false) {
			$item["DETAIL_PAGE_URL"] = SITE_DIR."product/".$seoCode."/";
		}
	}
	if(empty($item["PROPERTIES"]["ARTNUMBER"]["VALUE"]) && !empty($item["PROPERTIES"]["CML2_ARTICLE"]["VALUE"])) {
		$item["PROPERTIES"]["ARTNUMBER"] = $item["PROPERTIES"]["CML2_ARTICLE"];
	}
	if(!empty($item["OFFER_PROPERTIES"]) && empty($item["OFFER_PROPERTIES"]["ARTNUMBER"]["VALUE"]) && !empty($item["OFFER_PROPERTIES"]["CML2_ARTICLE"]["VALUE"])) {
		$item["OFFER_PROPERTIES"]["ARTNUMBER"] = $item["OFFER_PROPERTIES"]["CML2_ARTICLE"];
	}
}
unset($item);

//OBJECTS//
foreach($arResult["ITEMS"] as $item) {
	foreach($item["PROPERTIES"] as $prop) {
		if($prop["CODE"] == "OBJECT" && !empty($prop["VALUE"]))
			$objectsIds[] = $prop["VALUE"];
	}
	unset($prop);
}
unset($item);

if(!empty($objectsIds)) {
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($objectsIds)), false, false, array("ID", "IBLOCK_ID", "NAME", "PROPERTY_PHONE_SMS", "PROPERTY_EMAIL_EMAIL"));
	while($arElement = $rsElements->GetNext()) {
		$arObjects[$arElement["ID"]] = array(
			"NAME" => $arElement["NAME"],
			"PHONE_SMS" => !empty($arElement["PROPERTY_PHONE_SMS_VALUE"]),
			"EMAIL_EMAIL" => !empty($arElement["PROPERTY_EMAIL_EMAIL_VALUE"])
		);
	}
	unset($arElement, $rsElements);
	
	if(!empty($arObjects)) {
		foreach($arResult["ITEMS"] as &$item) {
			foreach($item["PROPERTIES"] as &$prop) {
				if($prop["CODE"] == "OBJECT" && !empty($prop["VALUE"])) {
					if(array_key_exists($prop["VALUE"], $arObjects))
						$prop["FULL_VALUE"] = $arObjects[$prop["VALUE"]];
				}
			}
			unset($prop);
		}
		unset($item);
	}
	unset($arObjects);
}
unset($objectsIds);

//DISPLAY_PROPERTIES//
foreach($arResult["ITEMS"] as &$item) {
	if(!empty($item["DISPLAY_PROPERTIES"])) {
		foreach($item["DISPLAY_PROPERTIES"] as &$property) {
			if($property["CODE"] == "BRAND") {
				continue;
			} elseif($property["CODE"] == "COLLECTION") {
				$property["DISPLAY_VALUE"] = strip_tags($property["DISPLAY_VALUE"]);
				$rsElements = CIBlockElement::GetList(array(), array("ID" => $property["VALUE"], "IBLOCK_ID" => $property["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "CODE", "NAME"));	
				while($obElement = $rsElements->GetNextElement()) {
					$arElement = $obElement->GetFields();
					$arElement["PROPERTIES"] = $obElement->GetProperties();
					foreach($arElement["PROPERTIES"] as $arCollectProp) {
						if($arCollectProp["CODE"] == "BRAND" && !empty($arCollectProp["VALUE"])) {
							$rsBrand = CIBlockElement::GetList(array(), array("ID" => $arCollectProp["VALUE"], "IBLOCK_ID" => $arCollectProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL"));
							if($arBrand = $rsBrand->GetNext()) {
								$property["DISPLAY_VALUE"] = "<a href='".$arBrand["~DETAIL_PAGE_URL"].$arElement["CODE"]."/'>".$arElement["NAME"]."</a>";
							}
							unset($arBrand, $rsBrand);
						}
					}
					unset($arCollectProp);
				}
				unset($arElement, $obElement, $rsElements);
			} else {
				$property["DISPLAY_VALUE"] = is_array($property["DISPLAY_VALUE"]) ? implode(" / ", $property["DISPLAY_VALUE"]) : strip_tags($property["DISPLAY_VALUE"]);
			}
		}
		unset($property);
	}
}
unset($item);

//RATING_REVIEWS_COUNT//
if($arParams["USE_REVIEW"] != "N" && $arParams["REVIEWS_IBLOCK_ID"] > 0) {
	$itemParentIds = array();

	foreach($arResult["ITEMS"] as $item) {
		$itemParentIds[] = $item["PARENT_ID"];
		
		$ratingSum[$item["PARENT_ID"]] = 0;
		$reviewsCount[$item["PARENT_ID"]] = 0;
	}
	unset($item);

	if(count($itemParentIds) > 0) {
		$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"], "PROPERTY_PRODUCT_ID" => array_unique($itemParentIds)), false, false, array("ID", "IBLOCK_ID"));
		while($obElement = $rsElements->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arProps = $obElement->GetProperties();

			$ratingSum[$arProps["PRODUCT_ID"]["VALUE"]] += $arProps["RATING"]["VALUE_XML_ID"];
			
			$reviewsCount[$arProps["PRODUCT_ID"]["VALUE"]]++;
		}
		unset($arProps, $arElement, $obElement, $rsElements);

		$reviewsDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("CATALOG_REVIEW"), Loc::getMessage("CATALOG_REVIEWS_1"), Loc::getMessage("CATALOG_REVIEWS_2"));

		foreach($arResult["ITEMS"] as &$item) {
			$item["RATING_VALUE"] = $reviewsCount[$item["PARENT_ID"]] > 0 ? sprintf("%.1f", round($ratingSum[$item["PARENT_ID"]] / $reviewsCount[$item["PARENT_ID"]], 1)) : 0;
			$item["REVIEWS_COUNT"] = $reviewsCount[$item["PARENT_ID"]];
			$item["REVIEWS_DECLENSION"] = $reviewsDeclension->get($reviewsCount[$item["PARENT_ID"]]);
		}
		unset($item, $reviewsDeclension);
	}
	unset($reviewsCount, $ratingSum, $itemParentIds);
}

//MEASURE//
$itemIds = $arMeasureList = array();

foreach($arResult["ITEMS"] as $item) {
	$itemIds[] = $item["ID"];
}
unset($item);

if(count($itemIds) > 0) {
	$arMeasureList = Bitrix\Catalog\ProductTable::getCurrentRatioWithMeasure($itemIds);

	foreach($arResult["ITEMS"] as &$item) {
		if(array_key_exists($item["ID"], $arMeasureList))
			$item["MEASURE"] = $arMeasureList[$item["ID"]]["MEASURE"];
	}
	unset($item);
}
unset($arMeasureList, $itemIds);

//COMPARE_SECTIONS//
// Группируем по корневому разделу каталога (DEPTH_LEVEL=1),
// чтобы не смешивать проекторы, щелевые лампы и т.п. в одной таблице.
$arResult["COMPARE_SECTIONS"] = array();
$arResult["COMPARE_SECTION_ID"] = 0;

if(!empty($arResult["ITEMS"])) {
	$sectionIds = array();
	foreach($arResult["ITEMS"] as $item) {
		$sectionId = (int)$item["IBLOCK_SECTION_ID"];
		if($sectionId > 0)
			$sectionIds[$sectionId] = $sectionId;
	}
	unset($item);

	$rootBySection = array();
	if(!empty($sectionIds)) {
		foreach($sectionIds as $sectionId) {
			$root = array("ID" => 0, "NAME" => Loc::getMessage("CATALOG_COMPARE_NO_SECTION"));
			$rsPath = CIBlockSection::GetNavChain($arParams["IBLOCK_ID"], $sectionId, array("ID", "NAME", "DEPTH_LEVEL"));
			while($path = $rsPath->Fetch()) {
				if((int)$path["DEPTH_LEVEL"] === 1) {
					$root = array(
						"ID" => (int)$path["ID"],
						"NAME" => $path["NAME"]
					);
					break;
				}
			}
			unset($path, $rsPath);
			if($root["ID"] <= 0) {
				$rsSection = CIBlockSection::GetList(
					array(),
					array("ID" => $sectionId, "IBLOCK_ID" => $arParams["IBLOCK_ID"]),
					false,
					array("ID", "NAME")
				);
				if($section = $rsSection->Fetch()) {
					$root = array(
						"ID" => (int)$section["ID"],
						"NAME" => $section["NAME"]
					);
				}
				unset($section, $rsSection);
			}
			$rootBySection[$sectionId] = $root;
		}
		unset($sectionId);
	}

	$sections = array();
	foreach($arResult["ITEMS"] as $item) {
		$sectionId = (int)$item["IBLOCK_SECTION_ID"];
		$root = isset($rootBySection[$sectionId])
			? $rootBySection[$sectionId]
			: array("ID" => 0, "NAME" => Loc::getMessage("CATALOG_COMPARE_NO_SECTION"));
		$rootId = (int)$root["ID"];
		if(!isset($sections[$rootId])) {
			$sections[$rootId] = array(
				"ID" => $rootId,
				"NAME" => $root["NAME"],
				"COUNT" => 0,
				"ITEM_IDS" => array()
			);
		}
		$sections[$rootId]["COUNT"]++;
		$sections[$rootId]["ITEM_IDS"][$item["ID"]] = true;
	}
	unset($item, $rootBySection);

	uasort($sections, function($a, $b) {
		return strcmp($a["NAME"], $b["NAME"]);
	});

	$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
	$activeSectionId = (int)$request->get("COMPARE_SECTION");
	if($activeSectionId <= 0 || !isset($sections[$activeSectionId])) {
		$activeSectionId = 0;
		$maxCount = -1;
		foreach($sections as $section) {
			if((int)$section["COUNT"] > $maxCount) {
				$maxCount = (int)$section["COUNT"];
				$activeSectionId = (int)$section["ID"];
			}
		}
		unset($section, $maxCount);
	}

	$filteredItems = array();
	foreach($arResult["ITEMS"] as $item) {
		if(isset($sections[$activeSectionId]["ITEM_IDS"][$item["ID"]]))
			$filteredItems[] = $item;
	}
	unset($item);
	$arResult["ITEMS"] = $filteredItems;
	unset($filteredItems);

	$arResult["COMPARE_SECTIONS"] = $sections;
	$arResult["COMPARE_SECTION_ID"] = $activeSectionId;
	unset($sections);

	// После фильтра по категории убираем пустые для этой группы строки характеристик
	if(!empty($arResult["SHOW_PROPERTIES"])) {
		foreach($arResult["SHOW_PROPERTIES"] as $code => $prop) {
			$empty = true;
			foreach($arResult["ITEMS"] as $item) {
				$value = isset($item["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? $item["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
					: "";
				if((is_array($value) && !empty($value)) || (!is_array($value) && (string)$value !== "")) {
					$empty = false;
					break;
				}
			}
			unset($item, $value);
			if($empty)
				unset($arResult["SHOW_PROPERTIES"][$code]);
		}
		unset($code, $prop, $empty);
	}
	if(!empty($arResult["SHOW_OFFER_PROPERTIES"])) {
		foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code => $prop) {
			$empty = true;
			foreach($arResult["ITEMS"] as $item) {
				$value = isset($item["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])
					? $item["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]
					: "";
				if((is_array($value) && !empty($value)) || (!is_array($value) && (string)$value !== "")) {
					$empty = false;
					break;
				}
			}
			unset($item, $value);
			if($empty)
				unset($arResult["SHOW_OFFER_PROPERTIES"][$code]);
		}
		unset($code, $prop, $empty);
	}
	if(!empty($arResult["SHOW_FIELDS"])) {
		foreach($arResult["SHOW_FIELDS"] as $code => $fieldCode) {
			if($code == "NAME" || $code == "PREVIEW_PICTURE" || $code == "DETAIL_PICTURE")
				continue;
			$empty = true;
			foreach($arResult["ITEMS"] as $item) {
				$value = isset($item["FIELDS"][$code]) ? $item["FIELDS"][$code] : "";
				if((is_array($value) && !empty($value)) || (!is_array($value) && (string)$value !== "")) {
					$empty = false;
					break;
				}
			}
			unset($item, $value);
			if($empty)
				unset($arResult["SHOW_FIELDS"][$code]);
		}
		unset($code, $fieldCode, $empty);
	}
}