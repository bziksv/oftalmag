<?php
/**
 * Fix PROPERTY_code values that are not valid URL slugs
 * (spaces, Cyrillic, leftover "Генерация кода из названия").
 * Также патчит sitemap-iblock-26.xml (old slug → new).
 *
 * Usage (from site root):
 *   php scripts/fix-bad-product-codes.php
 *   php scripts/fix-bad-product-codes.php --apply
 *
 * Работает через mysqli (.local/db.env), без bitrix prolog.
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$apply = in_array('--apply', $argv ?? [], true);

$env = [
	'DB_HOST' => '127.0.0.1',
	'DB_LOGIN' => 'root',
	'DB_PASSWORD' => '',
	'DB_NAME' => 'oftalmag_ru',
	'DB_PORT' => '3306',
];
$envFile = $root . '/.local/db.env';
if (is_file($envFile)) {
	foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
		$line = trim($line);
		if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
			continue;
		}
		[$k, $v] = explode('=', $line, 2);
		$env[trim($k)] = trim($v, " \t\"'");
	}
} elseif (is_file($root . '/bitrix/.settings.php')) {
	/** @var array $settings */
	$settings = include $root . '/bitrix/.settings.php';
	$conn = $settings['connections']['value']['default'] ?? [];
	$env['DB_HOST'] = (string)($conn['host'] ?? '127.0.0.1');
	$env['DB_LOGIN'] = (string)($conn['login'] ?? '');
	$env['DB_PASSWORD'] = (string)($conn['password'] ?? '');
	$env['DB_NAME'] = (string)($conn['database'] ?? '');
	if (strpos($env['DB_HOST'], ':') !== false) {
		[$h, $p] = explode(':', $env['DB_HOST'], 2);
		$env['DB_HOST'] = $h;
		$env['DB_PORT'] = $p;
	}
} else {
	fwrite(STDERR, "Missing .local/db.env and bitrix/.settings.php\n");
	exit(1);
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

$catalogIblockId = 26;
$propId = 0;
$res = $mysqli->query("SELECT ID FROM b_iblock_property WHERE IBLOCK_ID={$catalogIblockId} AND CODE='code' LIMIT 1");
if ($res && ($row = $res->fetch_row())) {
	$propId = (int)$row[0];
}
if ($propId <= 0) {
	fwrite(STDERR, "PROPERTY code not found in iblock {$catalogIblockId}\n");
	exit(1);
}

$isValid = static function (string $s): bool {
	return $s !== '' && (bool)preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_\-]*$/', $s);
};

$sql = "
	SELECT e.ID, e.NAME, e.CODE, IFNULL(ep.ID, 0) AS EP_ID, IFNULL(ep.VALUE, '') AS PROP
	FROM b_iblock_element e
	LEFT JOIN b_iblock_element_property ep
		ON ep.IBLOCK_ELEMENT_ID = e.ID AND ep.IBLOCK_PROPERTY_ID = {$propId}
	WHERE e.IBLOCK_ID = {$catalogIblockId}
";
$res = $mysqli->query($sql);
if (!$res) {
	fwrite(STDERR, $mysqli->error . "\n");
	exit(1);
}

$fixed = 0;
$listed = 0;
$replacements = []; // old prop => new slug

while ($row = $res->fetch_assoc()) {
	$prop = trim((string)$row['PROP']);
	if ($prop === '' || $isValid($prop)) {
		continue;
	}
	$listed++;
	$fallback = trim((string)$row['CODE']);
	if (!$isValid($fallback)) {
		echo "SKIP id={$row['ID']} name={$row['NAME']} bad prop=" . json_encode($prop, JSON_UNESCAPED_UNICODE) . " no fallback CODE\n";
		continue;
	}
	echo ($apply ? 'FIX' : 'DRY') . " id={$row['ID']} => {$fallback} (was " . json_encode($prop, JSON_UNESCAPED_UNICODE) . ")\n";
	$replacements[$prop] = $fallback;
	if ($apply) {
		$epId = (int)$row['EP_ID'];
		if ($epId > 0) {
			$stmt = $mysqli->prepare('UPDATE b_iblock_element_property SET VALUE = ? WHERE ID = ?');
			$stmt->bind_param('si', $fallback, $epId);
			$stmt->execute();
			$stmt->close();
		} else {
			$stmt = $mysqli->prepare(
				'INSERT INTO b_iblock_element_property (IBLOCK_PROPERTY_ID, IBLOCK_ELEMENT_ID, VALUE) VALUES (?, ?, ?)'
			);
			$eid = (int)$row['ID'];
			$stmt->bind_param('iis', $propId, $eid, $fallback);
			$stmt->execute();
			$stmt->close();
		}
		$fixed++;
	}
}

echo $apply ? "Updated DB: {$fixed}\n" : "Would update DB: {$listed} (re-run with --apply)\n";

// Sitemap: подмена битых /product/…/ (и после --apply по карте old→new)
require_once $root . '/bitrix/php_interface/include/SitemapProductUrls.php';

$map = [];
$sql = "
	SELECT e.CODE, IFNULL(ep.VALUE, '')
	FROM b_iblock_element e
	LEFT JOIN b_iblock_element_property ep
		ON ep.IBLOCK_ELEMENT_ID = e.ID AND ep.IBLOCK_PROPERTY_ID = {$propId}
	WHERE e.IBLOCK_ID = {$catalogIblockId} AND e.ACTIVE = 'Y'
";
$res = $mysqli->query($sql);
while ($row = $res->fetch_row()) {
	$code = trim((string)$row[0]);
	$seo = trim((string)$row[1]);
	$preferred = Oftalmag\SitemapProductUrls::preferredSlug($code, $seo);
	if ($preferred === '') {
		continue;
	}
	if ($code !== '') {
		$map[$code] = $preferred;
	}
	if ($seo !== '') {
		$map[$seo] = $preferred;
	}
}
foreach ($replacements as $old => $new) {
	$map[$old] = $new;
	$stripped = trim(preg_replace('/\s*Генерация кода из названия\s*/ui', '', $old));
	if ($stripped !== '' && $stripped !== $old) {
		$map[$stripped] = $new;
	}
}
$mysqli->close();

$files = array_unique(array_merge(
	glob($root . '/sitemap*iblock*26*.xml') ?: [],
	[$root . '/sitemap-iblock-26.xml', $root . '/sitemap_iblock_26.xml']
));

$sitemapStats = ['files' => 0, 'replaced' => 0, 'removed' => 0];
foreach ($files as $file) {
	if (!is_file($file)) {
		continue;
	}
	if (!$apply) {
		// dry-run: только показать, что нашлось бы
		$xml = file_get_contents($file);
		if ($xml !== false && preg_match_all('#/product/([^/<]+)/?#', $xml, $mm)) {
			foreach ($mm[1] as $raw) {
				$slug = rawurldecode($raw);
				if (!Oftalmag\SitemapProductUrls::isValidSlug($slug) || isset($replacements[$slug])) {
					echo "SITEMAP would fix: " . json_encode($slug, JSON_UNESCAPED_UNICODE) . "\n";
				}
			}
		}
		continue;
	}
	$result = Oftalmag\SitemapProductUrls::rewriteFile($file, $map);
	if ($result['changed']) {
		$sitemapStats['files']++;
		$sitemapStats['replaced'] += $result['replaced'];
		$sitemapStats['removed'] += $result['removed'];
		echo 'sitemap updated: ' . basename($file) . "\n";
	}
}

if ($apply) {
	echo 'Sitemap: ' . json_encode($sitemapStats, JSON_UNESCAPED_UNICODE) . "\n";
}
