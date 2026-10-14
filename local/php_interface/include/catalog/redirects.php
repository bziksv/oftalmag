<?php

/**
 * 301 со старых/отключённых URL каталога на рабочие.
 *
 * @return array<string, string> path => target path
 */
function oftalmagCatalogRedirectMap(): array
{
	return [
		// Старый ЧПУ карточки enext: /catalog/product/{CODE}/
		'/catalog/product/linza-goldmana-trekhzerkalnaya-goniolinza-gonioskop/'
			=> '/product/linza-goldmana-trekhzerkalnaya/',

		// Деактивированный подраздел → живой родитель
		'/catalog/lineyka-skiaskopicheskaya-orion-medik/'
			=> '/catalog/lineyki-skiaskopicheskie/',

		// Старый код раздела без -r
		'/catalog/oftalmologiya-surgicon-pakistan/'
			=> '/catalog/oftalmologiya-surgicon-pakistan-r/',
	];
}

function oftalmagCatalogRedirectTarget(string $requestUri): ?string
{
	$path = (string)(parse_url($requestUri, PHP_URL_PATH) ?: '/');
	if ($path === '') {
		$path = '/';
	}
	if (preg_match('#/index\.php$#', $path)) {
		$path = preg_replace('#/index\.php$#', '/', $path);
	}

	$map = oftalmagCatalogRedirectMap();
	$candidates = [$path];
	if (str_ends_with($path, '/')) {
		$candidates[] = rtrim($path, '/') ?: '/';
	} else {
		$candidates[] = $path . '/';
	}

	foreach ($candidates as $candidate) {
		if (isset($map[$candidate])) {
			return $map[$candidate];
		}
	}

	// Универсально: /catalog/product/{slug}/ → /product/{slug}/
	// (карточки давно на /product/; slug уточнит product/index.php)
	if (preg_match('#^/catalog/product/([^/]+)/?$#', $path, $m)) {
		return '/product/' . rawurlencode(rawurldecode($m[1])) . '/';
	}

	return null;
}

function oftalmagCatalogRedirectPerform(string $requestUri): bool
{
	$target = oftalmagCatalogRedirectTarget($requestUri);
	if ($target === null) {
		return false;
	}

	$query = parse_url($requestUri, PHP_URL_QUERY);
	if (is_string($query) && $query !== '') {
		$target .= (str_contains($target, '?') ? '&' : '?') . $query;
	}

	http_response_code(301);
	header('Location: ' . $target, true, 301);
	exit;
}
