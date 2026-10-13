<?
use Bitrix\Main;

// Class autoload
require __DIR__ . '/autoload.php';

$eventManager = Main\EventManager::getInstance();

$eventManager->addEventHandler('main', 'OnUserTypeBuildList', ['lib\UserType\CUserTypeSmartFilter', 'GetUserTypeDescription']);

AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("OnAfterIBlockElementAddClass", "OnAfterIBlockElementAddHandler"));
class OnAfterIBlockElementAddClass
{
    public static function OnAfterIBlockElementAddHandler(&$arFields)
    {
		if($arFields['RESULT']){
				$properties = [];
				$res = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields["ID"], "sort", "asc", ["CODE" => "CML2_ATTRIBUTES"]);
				while ($ob = $res->GetNext())
				{
					$name = trim(strip_tags($ob['DESCRIPTION']));
					$value = trim(strip_tags($ob['VALUE']));
					$alias = Cutil::translit($name, "ru", ["max_len" => 40]);
					
					$arFieldsProperty = [
					  "NAME" => $name,
					  "CODE" => $alias,
					  "IBLOCK_ID" => $arFields['IBLOCK_ID'],
					];
					
					if (CIBlockProperty::GetList([], ["IBLOCK_ID" => $arFields['IBLOCK_ID'], "CODE" => $alias])->SelectedRowsCount() <= 0)
						(new CIBlockProperty)->Add($arFieldsProperty);
					
					$properties[$arFieldsProperty["CODE"]] = $value;
				}
				
			CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, $properties);
		}
    }
}

function isWrapAttr($current, $target)
{
	if($current < $target)
		return true;
		
	return false;
}

function getCurrSection()
{
	global $APPLICATION;
	
	$dir = $APPLICATION->GetCurDir();
	
	if (str_starts_with($dir, '/catalog/')) 
	{
		$path = explode('/', substr($dir, 1, -1));
		
		if(isset($path[1]))
		{
			$code = $path[1];
			
			$db_list = CIBlockSection::GetList([], ['IBLOCK_ID' => 26, 'CODE' => $code], false, ['IBLOCK_ID', 'ID', 'CODE', 'NAME', 'UF_*']);
			if($ar_result = $db_list->GetNext())
			{
				$ar_result["URL"] = 'catalog';
				return $ar_result;
			}
		}
	}

	if (str_starts_with($dir, '/product/')) 
	{
		$path = explode('/', substr($dir, 1, -1));
		
		if(isset($path[1]))
		{
			$code = $path[1];

			$db_list = CIBlockSection::GetList([], ['IBLOCK_ID' => 26, '!UF_HIDE_MENU_INDEX' =>  false], false, ['IBLOCK_ID', 'ID', 'CODE', 'NAME', 'UF_*']);
			if($ar_result = $db_list->GetNext())
			{
				$aRRresult = $ar_result;
			}

			$aRRresult["URL"] = 'product';
			return $aRRresult;


			// $arSelect = Array("ID", "NAME", "IBLOCK_SECTION_ID");
			// $arFilter = ['IBLOCK_ID' => 26, 'CODE' => $code];
			// $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
			// if($ob = $res->GetNextElement())
			// {
			// 	$arFields = $ob->GetFields();
			// 	if($arFields["IBLOCK_SECTION_ID"]){
			// 		$db_list = CIBlockSection::GetList([], ['IBLOCK_ID' => 26, 'ID' => $arFields["IBLOCK_SECTION_ID"]], false, ['IBLOCK_ID', 'ID', 'CODE', 'NAME', 'UF_*']);
			// 		if($ar_result = $db_list->GetNext())
			// 		{
			// 			$ar_result["URL"] = 'product';
			// 			return $ar_result;
			// 		}
			// 	}
			// }

		}
	}

	return null;
}

// Страница успеха заказа: не терять доступ после истечения PHP-сессии
require_once __DIR__.'/include/OrderAccess.php';
AddEventHandler('main', 'OnBeforeProlog', ['Oftalmag\\OrderAccess', 'onBeforeProlog']);
AddEventHandler('sale', 'OnSaleComponentOrderOneStepComplete', ['Oftalmag\\OrderAccess', 'onOrderComplete']);

require_once __DIR__.'/include/OrderConsent.php';
AddEventHandler('sale', 'OnSaleComponentOrderProperties', ['Oftalmag\\OrderConsent', 'onOrderProperties']);

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/LegalRedirect.php';
AddEventHandler('main', 'OnBeforeProlog', ['Oftalmag\\LegalRedirect', 'onBeforeProlog']);

require_once __DIR__.'/include/SitemapProductUrls.php';
$eventManager->addEventHandler(
	'',
	'\\Bitrix\\Seo\\Sitemap\\Internals\\SitemapTable::OnAfterUpdate',
	['Oftalmag\\SitemapProductUrls', 'onSitemapAfterUpdate']
);

// После enext IMG_LAZYLOAD (SORT=100): один data-lazyload-src + обязательный src.
// Важно: AddEventHandler($mod,$ev,$cb,$SORT=100,$FULL_PATH=false) — 4-й аргумент SORT.
AddEventHandler('main', 'OnEndBufferContent', static function (&$content) {
	if (!is_string($content) || $content === '' || strpos($content, 'data-lazyload-src') === false) {
		return;
	}
	$pixel = '/bitrix/images/arturgolubev.lazyload/pixel.gif';
	$content = preg_replace_callback(
		'/<img\b[^>]*>/i',
		static function ($m) use ($pixel) {
			$tag = $m[0];
			if (!preg_match('/(?:^|\s)data-lazyload-src\s*=/i', $tag)) {
				return $tag;
			}

			$lazyUrls = [];
			if (preg_match_all('/(?:^|\s)data-lazyload-src\s*=\s*([\'"])(.*?)\1/i', $tag, $lm)) {
				foreach ($lm[2] as $url) {
					$url = trim($url);
					if ($url !== '') {
						$lazyUrls[] = $url;
					}
				}
			}
			$lazy = '';
			foreach ($lazyUrls as $url) {
				if (strpos($url, 'pixel.gif') === false) {
					$lazy = $url;
					break;
				}
			}
			if ($lazy === '' && $lazyUrls) {
				$lazy = $lazyUrls[0];
			}

			$src = '';
			// Не путать с data-lazyload-src (\b ложно матчит «src» внутри имени атрибута).
			if (preg_match('/(?:^|\s)src\s*=\s*([\'"])(.*?)\1/i', $tag, $sm)) {
				$src = trim($sm[2]);
			}
			if ($src === '' || strpos($src, 'pixel.gif') !== false) {
				$src = $pixel;
			}

			$rest = preg_replace('/\s*(?:src|data-lazyload-src)\s*=\s*([\'"]).*?\1/i', '', $tag);
			$rest = preg_replace('/^<img\b/i', '', $rest);
			$rest = preg_replace('/\s*\/?>$/', '', $rest);
			$out = '<img src="'.htmlspecialcharsbx($src).'"';
			if ($lazy !== '') {
				$out .= ' data-lazyload-src="'.htmlspecialcharsbx($lazy).'"';
			}
			$out .= $rest.'>';
			return $out;
		},
		$content
	);
}, 50000);


