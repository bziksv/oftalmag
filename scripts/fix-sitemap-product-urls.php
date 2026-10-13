<?php
/**
 * CLI: переписать URL товаров в sitemap-*iblock*26*.xml на PROPERTY_code.
 * Работает без bitrix prolog (достаточно .local/db.env).
 *
 * Usage: php scripts/fix-sitemap-product-urls.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$envFile = $root . '/.local/db.env';
if (!is_file($envFile)) {
	fwrite(STDERR, "Missing .local/db.env\n");
	exit(1);
}

$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
	$line = trim($line);
	if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
		continue;
	}
	[$k, $v] = explode('=', $line, 2);
	$env[trim($k)] = trim($v, " \t\"'");
}

$mysqli = new mysqli(
	$env['DB_HOST'] ?? '127.0.0.1',
	$env['DB_LOGIN'] ?? 'root',
	$env['DB_PASSWORD'] ?? '',
	$env['DB_NAME'] ?? 'oftalmag_ru',
	(int)($env['DB_PORT'] ?? 3306)
);
if ($mysqli->connect_errno) {
	fwrite(STDERR, 'MySQL: ' . $mysqli->connect_error . "\n");
	exit(1);
}
$mysqli->set_charset('utf8mb4');

$propId = 0;
$res = $mysqli->query("SELECT ID FROM b_iblock_property WHERE IBLOCK_ID=26 AND CODE='code' LIMIT 1");
if ($res && ($row = $res->fetch_row())) {
	$propId = (int)$row[0];
}
if ($propId <= 0) {
	fwrite(STDERR, "PROPERTY code not found in iblock 26\n");
	exit(1);
}

$map = [];
$sql = "
	SELECT e.CODE, IFNULL(ep.VALUE, '')
	FROM b_iblock_element e
	LEFT JOIN b_iblock_element_property ep
		ON ep.IBLOCK_ELEMENT_ID = e.ID AND ep.IBLOCK_PROPERTY_ID = {$propId}
	WHERE e.IBLOCK_ID = 26 AND e.ACTIVE = 'Y'
";
$res = $mysqli->query($sql);
while ($row = $res->fetch_row()) {
	$code = trim((string)$row[0]);
	if ($code !== '') {
		$map[$code] = trim((string)$row[1]);
	}
}
$mysqli->close();

require_once $root . '/bitrix/php_interface/include/SitemapProductUrls.php';

$stats = ['files' => 0, 'replaced' => 0, 'removed' => 0, 'map' => count($map)];
$files = array_unique(array_merge(
	glob($root . '/sitemap*iblock*26*.xml') ?: [],
	[
		$root . '/sitemap-iblock-26.xml',
		$root . '/sitemap_iblock_26.xml',
	]
));

foreach ($files as $file) {
	if (!is_file($file)) {
		continue;
	}
	$result = Oftalmag\SitemapProductUrls::rewriteFile($file, $map);
	if ($result['changed']) {
		$stats['files']++;
		$stats['replaced'] += $result['replaced'];
		$stats['removed'] += $result['removed'];
		echo 'updated: ' . basename($file) . "\n";
	}
}

echo json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), PHP_EOL;
