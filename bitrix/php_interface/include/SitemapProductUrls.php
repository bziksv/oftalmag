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

	public static function isValidSlug(string $slug): bool
	{
		$slug = trim($slug);
		return $slug !== '' && (bool)preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_\-]*$/', $slug);
	}

	/**
	 * Предпочтительный SEO-slug: валидный PROPERTY_code, иначе CODE.
	 */
	public static function preferredSlug(string $code, string $propertyCode): string
	{
		$propertyCode = trim($propertyCode);
		$code = trim($code);
		if (self::isValidSlug($propertyCode)) {
			return $propertyCode;
		}
		if (self::isValidSlug($code)) {
			return $code;
		}
		return '';
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

		$map = self::buildSlugRewriteMap();
		if ($map === []) {
			return $stats;
		}

		$files = glob($documentRoot . '/sitemap*iblock*' . self::CATALOG_IBLOCK_ID . '*.xml') ?: [];
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
	 * @return array<string, string> any known slug (CODE or PROPERTY, even broken) => preferred slug
	 */
	public static function buildSlugRewriteMap(): array
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
			['ID', 'CODE', 'NAME', 'PROPERTY_' . self::PROPERTY_CODE]
		);

		while ($row = $res->Fetch()) {
			$code = trim((string)($row['CODE'] ?? ''));
			$seo = trim((string)($row['PROPERTY_CODE_VALUE'] ?? ''));
			$preferred = self::preferredSlug($code, $seo);
			if ($preferred === '') {
				continue;
			}
			if ($code !== '') {
				$map[$code] = $preferred;
			}
			if ($seo !== '') {
				$map[$seo] = $preferred;
			}
			// Частый мусор Bitrix «Генерация кода из названия» в конце slug
			if ($seo !== '' && !self::isValidSlug($seo)) {
				$stripped = trim(preg_replace('/\s*Генерация кода из названия\s*/ui', '', $seo));
				if ($stripped !== '' && $stripped !== $seo) {
					$map[$stripped] = $preferred;
				}
			}
		}

		return $map;
	}

	/**
	 * @deprecated use buildSlugRewriteMap
	 * @return array<string, string> CODE => PROPERTY_code
	 */
	public static function buildCodeToSeoMap(): array
	{
		return self::buildSlugRewriteMap();
	}

	/**
	 * @param array<string, string> $map slug => preferred
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
		$validTargets = array_fill_keys(array_values($map), true);

		// <url>…<loc>https://host/product/SLUG/</loc>…</url>
		$xml = preg_replace_callback(
			'#<url>\s*<loc>(https?://[^<]+/product/([^/<]+)/?)</loc>(.*?)</url>#si',
			static function (array $m) use ($map, $validTargets, &$result) {
				$fullUrl = $m[1];
				$slugRaw = $m[2];
				$slug = rawurldecode($slugRaw);
				$tail = $m[3];

				$preferred = null;
				if (array_key_exists($slug, $map)) {
					$preferred = $map[$slug];
				} elseif (!self::isValidSlug($slug)) {
					$stripped = trim(preg_replace('/\s*Генерация кода из названия\s*/ui', '', $slug));
					if ($stripped !== '' && array_key_exists($stripped, $map)) {
						$preferred = $map[$stripped];
					} else {
						// модель вида cab-4000 в битом slug
						if (preg_match_all('/[a-z]{2,}\-\d+/i', $slug, $mm)) {
							foreach ($mm[0] as $token) {
								$token = strtolower($token);
								foreach ($map as $from => $to) {
									if (stripos($from, $token) !== false || stripos($to, $token) !== false) {
										$preferred = $to;
										break 2;
									}
								}
							}
						}
					}
				} elseif (isset($validTargets[$slug])) {
					return $m[0];
				}

				if ($preferred === null || $preferred === '') {
					if (!self::isValidSlug($slug)) {
						$result['removed']++;
						return '';
					}
					return $m[0];
				}

				if ($preferred === $slug) {
					return $m[0];
				}

				$newUrl = preg_replace(
					'#/product/' . preg_quote($slugRaw, '#') . '/?#',
					'/product/' . rawurlencode($preferred) . '/',
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

		$xml = preg_replace("/\n{3,}/", "\n\n", $xml);

		if ($xml !== $original) {
			file_put_contents($file, $xml);
			$result['changed'] = true;
		}

		return $result;
	}
}
