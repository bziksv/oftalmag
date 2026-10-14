<?php
/**
 * Товары активны, но лежат в деактивированных разделах → 404 на /catalog/...
 *
 * - активирует oftalmologicheskie-instrumenty-pakistan (1532)
 * - переносит товары из старого Surgicon (1525) в новый (1535)
 * - переименовывает CODE старого 1525, чтобы не конфликтовал с -r
 * - активирует lineyka-skiaskopicheskaya-orion-medik (1451)
 *
 * Usage:
 *   php scripts/fix-inactive-catalog-sections.php
 *   php scripts/fix-inactive-catalog-sections.php --apply
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$apply = in_array('--apply', $argv ?? [], true);

$_SERVER['DOCUMENT_ROOT'] = $root;
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_NO_ACCELERATOR_RESET', true);

require $root . '/bitrix/modules/main/include/prolog_before.php';

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
	fwrite(STDERR, "iblock required\n");
	exit(1);
}

$iblockId = 26;

/**
 * @param array<string,mixed> $fields
 */
function oftalmagFixSection(int $id, array $fields, bool $apply): void
{
	$sec = CIBlockSection::GetList([], ['ID' => $id, 'CHECK_PERMISSIONS' => 'N'], false, array_merge(['ID', 'NAME', 'CODE', 'ACTIVE'], array_keys($fields)))->GetNext();
	if (!$sec) {
		echo "SKIP section $id missing\n";
		return;
	}
	$need = [];
	foreach ($fields as $k => $v) {
		$cur = (string)($sec[$k] ?? '');
		if ($cur !== (string)$v) {
			$need[$k] = $v;
		}
	}
	if ($need === []) {
		echo "OK section $id {$sec['CODE']} already fine\n";
		return;
	}
	echo ($apply ? 'FIX' : 'DRY') . " section $id {$sec['CODE']}: " . json_encode($need, JSON_UNESCAPED_UNICODE) . "\n";
	if ($apply) {
		$bs = new CIBlockSection();
		if (!$bs->Update($id, $need)) {
			fwrite(STDERR, "Update section $id failed: " . $bs->LAST_ERROR . "\n");
		}
	}
}

// 1) Родитель Pakistan
oftalmagFixSection(1532, ['ACTIVE' => 'Y'], $apply);

// 2) Новый Surgicon -r должен быть активен (уже Y), старый — другой CODE
oftalmagFixSection(1535, ['ACTIVE' => 'Y'], $apply);
oftalmagFixSection(1525, [
	'ACTIVE' => 'N',
	'CODE' => 'oftalmologiya-surgicon-pakistan-old',
], $apply);

// 3) Линейка Orion
oftalmagFixSection(1451, ['ACTIVE' => 'Y'], $apply);

// 4) Перенос товаров 1525 → 1535
$rs = CIBlockElement::GetList(
	[],
	['IBLOCK_ID' => $iblockId, 'SECTION_ID' => 1525, 'INCLUDE_SUBSECTIONS' => 'N', 'CHECK_PERMISSIONS' => 'N'],
	false,
	false,
	['ID', 'NAME', 'CODE']
);
$moved = 0;
while ($el = $rs->GetNext()) {
	$id = (int)$el['ID'];
	$groups = [];
	$gRes = CIBlockElement::GetElementGroups($id, true);
	while ($g = $gRes->Fetch()) {
		$gid = (int)$g['ID'];
		if ($gid === 1525) {
			$gid = 1535;
		}
		$groups[$gid] = $gid;
	}
	if (!isset($groups[1535])) {
		$groups[1535] = 1535;
	}
	echo ($apply ? 'MOVE' : 'DRY') . " element $id → sections " . implode(',', $groups) . " ({$el['NAME']})\n";
	if ($apply) {
		CIBlockElement::SetElementSection($id, array_values($groups));
		$moved++;
	}
}
echo $apply ? "Moved: $moved\n" : "Dry-run done (re-run with --apply)\n";

if ($apply) {
	$GLOBALS['CACHE_MANAGER']->CleanAll();
	BXClearCache(true);
	echo "cache cleared\n";
}
