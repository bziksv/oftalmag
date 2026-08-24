<?php
/**
 * Обновляет URL соглашений Bitrix UserConsent на PNG из legal/config.php.
 */
declare(strict_types=1);

$docRoot = dirname(__DIR__);
$config = include $docRoot . '/local/php_interface/include/legal/config.php';
$images = $config['images'];

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

$updated = 0;

if (!empty($images['consent'])) {
	$stmt = $mysqli->prepare('UPDATE b_consent_agreement SET URL = ? WHERE ID = 1 AND URL <> ?');
	$stmt->bind_param('ss', $images['consent'], $images['consent']);
	$stmt->execute();
	$updated += $stmt->affected_rows;
	echo "Agreement ID=1 -> {$images['consent']}\n";
}

foreach ($images as $key => $url) {
	$stmt = $mysqli->prepare('UPDATE b_consent_agreement SET URL = ? WHERE URL <> ? AND (NAME LIKE ? OR CODE LIKE ?)');
	$like = '%' . $key . '%';
	$stmt->bind_param('ssss', $url, $url, $like, $like);
	$stmt->execute();
	if ($stmt->affected_rows > 0) {
		$updated += $stmt->affected_rows;
		echo "Updated {$stmt->affected_rows} agreement(s) for {$key} -> {$url}\n";
	}
}

echo "Updated {$updated} agreement URL(s) total\n";
