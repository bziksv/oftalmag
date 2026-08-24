<?php

/**
 * 301 с устаревших URL (корневые JPG/PNG, HTML /legal/*, опечатки) на актуальные PNG.
 *
 * @return array<string, string> path => target path
 */
function oftalmagLegalRedirectMap(): array
{
	if (!defined('B_PROLOG_INCLUDED')) {
		define('B_PROLOG_INCLUDED', true);
	}

	$config = include __DIR__ . '/config.php';
	$images = $config['images'];

	return [
		// Старые файлы в корне сайта
		'/politics.jpg' => $images['personal_data'],
		'/politika-ispolzovanija-cookies-oftalmag.jpg' => $images['cookie'],
		'/compliance.png' => $images['consent'],

		// Опечатка / старый путь в upload
		'/upload/politics-lormag.png' => $images['personal_data'],
		'/upload/politics.png' => $images['personal_data'],

		// Удалённые HTML-страницы /legal/*
		'/legal/oftalmag-politika-personalnyh-dannyh/' => $images['personal_data'],
		'/legal/oftalmag-soglasie-obrabotki-pd/' => $images['consent'],
		'/legal/oftalmag-politika-cookie/' => $images['cookie'],
		'/legal/oftalmag-pravila-rekomendatelnyh-tehnologiy/' => $images['recommendation'],
	];
}

function oftalmagLegalRedirectTarget(string $requestUri): ?string
{
	$path = (string)(parse_url($requestUri, PHP_URL_PATH) ?: '/');
	if ($path === '') {
		$path = '/';
	}

	if (preg_match('#/index\.php$#', $path)) {
		$path = preg_replace('#/index\.php$#', '/', $path);
	}

	$map = oftalmagLegalRedirectMap();
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

	return null;
}

function oftalmagLegalRedirectPerform(string $requestUri): bool
{
	$target = oftalmagLegalRedirectTarget($requestUri);
	if ($target === null) {
		return false;
	}

	http_response_code(301);
	header('Location: ' . $target, true, 301);
	exit;
}
