<?php

namespace Oftalmag;

/**
 * В каталоге oftalmag артикул хранится в CML2_ARTICLE (iblock 26),
 * а шаблон enext везде ждёт ARTNUMBER (демо-свойство офферов).
 */
class ArticleProperty
{
	public static function normalizeBasketGrid(array &$arResult): void
	{
		if(!empty($arResult["GRID"]["ROWS"]) && is_array($arResult["GRID"]["ROWS"])) {
			foreach($arResult["GRID"]["ROWS"] as &$row) {
				self::fillArticleColumn($row);
			}
			unset($row);
		}

		if(empty($arResult["GRID"]["HEADERS"]) || !is_array($arResult["GRID"]["HEADERS"])) {
			return;
		}

		$hasArtnumber = false;
		$cml2Key = null;
		foreach($arResult["GRID"]["HEADERS"] as $key => $header) {
			if(!is_array($header) || empty($header["id"])) {
				continue;
			}
			if($header["id"] === "PROPERTY_ARTNUMBER_VALUE") {
				$hasArtnumber = true;
			}
			if($header["id"] === "PROPERTY_CML2_ARTICLE_VALUE") {
				$cml2Key = $key;
			}
		}

		if($cml2Key === null) {
			return;
		}

		if(!$hasArtnumber) {
			$arResult["GRID"]["HEADERS"][$cml2Key]["id"] = "PROPERTY_ARTNUMBER_VALUE";
		} else {
			unset($arResult["GRID"]["HEADERS"][$cml2Key]);
			$arResult["GRID"]["HEADERS"] = array_values($arResult["GRID"]["HEADERS"]);
		}
	}

	public static function fillArticleColumn(array &$item): void
	{
		$article = isset($item["PROPERTY_ARTNUMBER_VALUE"]) ? trim((string)$item["PROPERTY_ARTNUMBER_VALUE"]) : "";
		$cml2 = isset($item["PROPERTY_CML2_ARTICLE_VALUE"]) ? trim((string)$item["PROPERTY_CML2_ARTICLE_VALUE"]) : "";

		if(($article === "" || $article === "-") && $cml2 !== "" && $cml2 !== "-") {
			$item["PROPERTY_ARTNUMBER_VALUE"] = $cml2;
		}
	}

	public static function getElementArticle(int $iblockId, int $elementId): string
	{
		if($iblockId <= 0 || $elementId <= 0 || !\Bitrix\Main\Loader::includeModule("iblock")) {
			return "";
		}

		foreach(["ARTNUMBER", "CML2_ARTICLE"] as $code) {
			$res = \CIBlockElement::GetProperty($iblockId, $elementId, [], ["CODE" => $code]);
			if($prop = $res->Fetch()) {
				$value = trim((string)($prop["VALUE"] ?? ""));
				if($value !== "") {
					return $value;
				}
			}
		}

		return "";
	}
}
