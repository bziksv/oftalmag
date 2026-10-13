<?php

namespace Oftalmag;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\EventResult;
use Bitrix\Main\Loader;

/**
 * После генерации SEO-sitemap (админка seo_sitemap_edit) подменяет
 * /product/{CODE}/ на /product/{PROPERTY_code}/ для инфоблока каталога.
 *
 * Роутер карточек открывает страницы только по PROPERTY_code.
 */
class SitemapProductUrls
{
	public const CATALOG_IBLOCK_ID = 26;
	public const PROPERTY_CODE = 'code';

	public static function onSitemapAfterUpdate(Event $event): EventResult
	{
		$fields = $event->getParameter('fields');
		if (!is_array($fields) || !array_key_exists('DATE_RUN', $fields)) {
			return new EventResult();
		}

		self::rewriteCatalogSitemapFiles();

		return new EventResult();
	}

	/**
	 * Переписать XML-файлы sitemap каталога в корне сайта.
	 *
	 * @return array{files:int,replaced:int,removed:int}
	 */
	public static function rewriteCatalogSitemapFiles(?string $documentRoot = null): array
	{
		$documentRoot = rtrim($documentRoot ?? (string)Application::getDocumentRoot(), '/');
		$stats = ['files' => 0, 'replaced' => 0, 'removed' => 0];

		$map = self::buildCodeToSeoMap();
		if ($map === []) {
			return $stats;
		}

		$files = glob($documentRoot . '/sitemap*iblock*' . self::CATALOG_IBLOCK_ID . '*.xml') ?: [];
		// На случай нестандартного имени из настроек SEO
		$extra = $documentRoot . '/sitemap-iblock-' . self::CATALOG_IBLOCK_ID . '.xml';
		if (is_file($extra) && !in_array($extra, $files, true)) {
			$files[] = $extra;
		}
		$extra2 = $documentRoot . '/sitemap_iblock_' . self::CATALOG_IBLOCK_ID . '.xml';
		if (is_file($extra2) && !in_array($extra2, $files, true)) {
			$files[] = $extra2;
		}

		foreach ($files as $file) {
			$result = self::rewriteFile($file, $map);
			if ($result['changed']) {
				$stats['files']++;
				$stats['replaced'] += $result['replaced'];
				$stats['removed'] += $result['removed'];
			}
		}

		return $stats;
	}

	/**
	 * @return array<string, string> CODE => PROPERTY_code
	 */
	public static function buildCodeToSeoMap(): array
	{
		if (!Loader::includeModule('iblock')) {
			return [];
		}

		$map = [];
		$res = \CIBlockElement::GetList(
			[],
			[
				'IBLOCK_ID' => self::CATALOG_IBLOCK_ID,
				'ACTIVE' => 'Y',
				'ACTIVE_DATE' => 'Y',
			],
			false,
			false,
			['ID', 'CODE', 'PROPERTY_' . self::PROPERTY_CODE]
		);

		while ($row = $res->Fetch()) {
			$code = trim((string)($row['CODE'] ?? ''));
			if ($code === '') {
				continue;
			}

			// GetList + PROPERTY_code → PROPERTY_CODE_VALUE
			$seo = trim((string)($row['PROPERTY_CODE_VALUE'] ?? ''));
			$map[$code] = $seo;
		}

		return $map;
	}

	/**
	 * @param array<string, string> $map
	 * @return array{changed:bool,replaced:int,removed:int}
	 */
	public static function rewriteFile(string $file, array $map): array
	{
		$result = ['changed' => false, 'replaced' => 0, 'removed' => 0];
		if (!is_file($file) || !is_readable($file)) {
			return $result;
		}

		$xml = file_get_contents($file);
		if ($xml === false || $xml === '') {
			return $result;
		}

		$original = $xml;

		// <url>…<loc>https://host/product/SLUG/</loc>…</url>
		$xml = preg_replace_callback(
			'#<url>\s*<loc>(https?://[^<]+/product/([^/<]+)/?)</loc>(.*?)</url>#si',
			static function (array $m) use ($map, &$result) {
				$fullUrl = $m[1];
				$slug = rawurldecode($m[2]);
				$tail = $m[3];

				if (!array_key_exists($slug, $map)) {
					return $m[0];
				}

				$seo = $map[$slug];
				if ($seo === '') {
					$result['removed']++;
					return '';
				}

				if ($seo === $slug) {
					return $m[0];
				}

				$newUrl = preg_replace(
					'#/product/' . preg_quote($m[2], '#') . '/?#',
					'/product/' . rawurlencode($seo) . '/',
					$fullUrl,
					1
				);
				$result['replaced']++;

				return '<url><loc>' . $newUrl . '</loc>' . $tail . '</url>';
			},
			$xml
		);

		if (!is_string($xml)) {
			return $result;
		}

		// Убрать пустые строки после удаления url-блоков
		$xml = preg_replace("/\n{3,}/", "\n\n", $xml);

		if ($xml !== $original) {
			file_put_contents($file, $xml);
			$result['changed'] = true;
		}

		return $result;
	}
}
