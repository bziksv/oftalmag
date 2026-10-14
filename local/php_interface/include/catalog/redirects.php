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

		// Старый код раздела без -r → актуальный Surgicon
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

	// /catalog/product/{slug}/ → /product/{slug}/
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

	// Не редиректить на тот же path (защита от петли).
	$path = (string)(parse_url($requestUri, PHP_URL_PATH) ?: '/');
	$targetPath = (string)(parse_url($target, PHP_URL_PATH) ?: $target);
	if (rtrim($path, '/') === rtrim($targetPath, '/')) {
		return false;
	}

	$query = parse_url($requestUri, PHP_URL_QUERY);
	if (is_string($query) && $query !== '') {
		$target .= (strpos($target, '?') !== false ? '&' : '?') . $query;
	}

	if (isset($_SERVER['HTTP_HOST']) && $target !== '' && $target[0] === '/') {
		$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
			|| ((string)($_SERVER['SERVER_PORT'] ?? '') === '443');
		$target = ($https ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $target;
	}

	if (function_exists('LocalRedirect')) {
		LocalRedirect($target, true, '301 Moved Permanently');
	}

	http_response_code(301);
	header('Location: ' . $target, true, 301);
	exit;
}
