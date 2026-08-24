<?php
/**
 * Меняет только href cookies в MAINTEXT модуля niges.cookiesaccept.
 * Текст уведомления не трогаем.
 */
declare(strict_types=1);

$docRoot = dirname(__DIR__);
$moduleId = 'niges.cookiesaccept';
$optionName = 'MAINTEXT';
$replacements = [
	'/legal/oftalmag-politika-cookie/' => '/upload/cookies-oftalmag.png',
	'/upload/cookies-oftalmag.png' => '/upload/cookies-oftalmag.png',
];
$newHref = '/upload/cookies-oftalmag.png';
$siteId = 's1';

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = (int) (getenv('DB_PORT') ?: 3306);
$dbName = getenv('DB_NAME') ?: 'oftalmag_ru';
$dbLogin = getenv('DB_LOGIN') ?: '';
$dbPassword = getenv('DB_PASSWORD') ?: '';

if ($dbLogin === '' || $dbPassword === '') {
	fwrite(STDERR, "DB credentials missing. Set DB_LOGIN/DB_PASSWORD or use .local/db.env\n");
	exit(1);
}

$mysqli = new mysqli($dbHost, $dbLogin, $dbPassword, $dbName, $dbPort);
if ($mysqli->connect_errno) {
	fwrite(STDERR, "DB connect failed: {$mysqli->connect_error}\n");
	exit(1);
}
$mysqli->set_charset('utf8mb4');

function clearOptionCacheFiles(string $docRoot, string $needle): int
{
	$cacheGlob = $docRoot . '/bitrix/managed_cache/MYSQL/b_option/*/*.php';
	$cacheFiles = glob($cacheGlob) ?: [];
	$removed = 0;

	foreach ($cacheFiles as $cacheFile) {
		$content = @file_get_contents($cacheFile);
		if ($content === false || strpos($content, $needle) === false) {
			continue;
		}
		if (@unlink($cacheFile)) {
			$removed++;
		}
	}

	return $removed;
}

$updated = 0;
$stmt = $mysqli->prepare(
	'SELECT SITE_ID, VALUE FROM b_option_site WHERE MODULE_ID = ? AND NAME = ?'
);
$stmt->bind_param('ss', $moduleId, $optionName);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
	$value = (string) $row['VALUE'];
	$newValue = $value;
	foreach ($replacements as $oldHref => $targetHref) {
		if (strpos($newValue, $oldHref) !== false) {
			$newValue = str_replace($oldHref, $targetHref, $newValue);
		}
	}
	if ($newValue === $value) {
		continue;
	}
	$rowSiteId = (string) $row['SITE_ID'];

	$update = $mysqli->prepare(
		'UPDATE b_option_site SET VALUE = ? WHERE MODULE_ID = ? AND NAME = ? AND SITE_ID = ?'
	);
	$update->bind_param('ssss', $newValue, $moduleId, $optionName, $rowSiteId);
	$update->execute();
	$updated++;
	echo "Updated MAINTEXT in b_option_site for site_id={$rowSiteId}\n";
}

if ($updated === 0) {
	$check = $mysqli->prepare(
		'SELECT VALUE FROM b_option_site WHERE MODULE_ID = ? AND NAME = ? AND SITE_ID = ? LIMIT 1'
	);
	$check->bind_param('sss', $moduleId, $optionName, $siteId);
	$check->execute();
	$checkResult = $check->get_result();
	$row = $checkResult->fetch_assoc();

	if ($row === null) {
		fwrite(STDERR, "MAINTEXT not found in b_option_site for module {$moduleId}, site {$siteId}\n");
		exit(1);
	}

	if (strpos((string) $row['VALUE'], $newHref) !== false) {
		echo "MAINTEXT in b_option_site already points to {$newHref}\n";
	} elseif (strpos((string) $row['VALUE'], '/legal/oftalmag-politika-cookie/') === false
		&& strpos((string) $row['VALUE'], '/upload/cookies-oftalmag.png') === false) {
		fwrite(STDERR, "MAINTEXT found but does not contain expected cookie href.\n");
		exit(1);
	}
}

$removed = clearOptionCacheFiles($docRoot, '/upload/cookies-oftalmag.png');
if ($removed === 0) {
	$removed = clearOptionCacheFiles($docRoot, '/legal/oftalmag-politika-cookie/');
}
if ($removed === 0) {
	$removed = clearOptionCacheFiles($docRoot, $moduleId);
}
echo "Removed {$removed} b_option cache file(s)\n";
echo "Done.\n";
