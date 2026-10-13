<?php
/**
 * Fix PROPERTY_code values that are not valid URL slugs
 * (spaces, Cyrillic, leftover "Генерация кода из названия").
 *
 * Usage (from site root):
 *   php scripts/fix-bad-product-codes.php
 *   php scripts/fix-bad-product-codes.php --apply
 */
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_NO_ACCELERATOR_RESET', true);

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
	fwrite(STDERR, "iblock module required\n");
	exit(1);
}

$apply = in_array('--apply', $argv ?? [], true);
$catalogIblockId = (int)(\Bitrix\Main\Config\Option::get('catalog', 'default_product_iblock') ?: 0);
if ($catalogIblockId <= 0) {
	$catalogIblockId = 26; // enext_catalog_s1 on oftalmag
}

$rs = CIBlockElement::GetList(
	[],
	['IBLOCK_ID' => $catalogIblockId, '!PROPERTY_code' => false],
	false,
	false,
	['ID', 'NAME', 'CODE', 'PROPERTY_code']
);

$fixed = 0;
$listed = 0;
while ($row = $rs->GetNext()) {
	$prop = trim((string)$row['PROPERTY_CODE_VALUE']);
	if ($prop === '' || preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_\-]*$/', $prop)) {
		continue;
	}
	$listed++;
	$fallback = trim((string)$row['CODE']);
	if ($fallback === '' || !preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_\-]*$/', $fallback)) {
		echo "SKIP id={$row['ID']} name={$row['NAME']} bad prop=".json_encode($prop, JSON_UNESCAPED_UNICODE)." no fallback CODE\n";
		continue;
	}
	echo ($apply ? 'FIX' : 'DRY')." id={$row['ID']} => {$fallback} (was ".json_encode($prop, JSON_UNESCAPED_UNICODE).")\n";
	if ($apply) {
		CIBlockElement::SetPropertyValuesEx((int)$row['ID'], $catalogIblockId, ['code' => $fallback]);
		$fixed++;
	}
}

echo $apply ? "Updated: {$fixed}\n" : "Would update: {$listed} (re-run with --apply)\n";
