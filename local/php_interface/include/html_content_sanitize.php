<?php
/**
 * Soft HTML cleanup for iblock DETAIL/PREVIEW text and property values.
 * Fixes common validator issues without rewriting editorial content wholesale.
 */
if (!function_exists('oftalmagIsValidProductCodeSlug')) {
	/**
	 * PROPERTY_code suitable for /product/{slug}/ (no spaces/Cyrillic/"Генерация кода…").
	 */
	function oftalmagIsValidProductCodeSlug($code)
	{
		$code = trim((string)$code);
		return $code !== '' && (bool)preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_\-]*$/', $code);
	}
}

if (!function_exists('oftalmagRemoveDuplicateProductHeading')) {
	/**
	 * Drop <h2>…</h2> that repeats the product name (H1 from page title / breadcrumb).
	 */
	function oftalmagRemoveDuplicateProductHeading($html, $productName)
	{
		if (!is_string($html) || $html === '' || !is_string($productName) || trim($productName) === '') {
			return $html;
		}
		$normalize = static function ($s) {
			$s = html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5, 'UTF-8');
			$s = preg_replace('/\s+/u', ' ', trim($s));
			return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
		};
		$target = $normalize($productName);
		if ($target === '') {
			return $html;
		}
		return preg_replace_callback(
			'/<h2\b[^>]*>([\s\S]*?)<\/h2>/iu',
			static function ($m) use ($normalize, $target) {
				return $normalize($m[1]) === $target ? '' : $m[0];
			},
			$html
		);
	}
}

if (!function_exists('oftalmagEscapeBareLt')) {
	function oftalmagEscapeBareLt($value)
	{
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = oftalmagEscapeBareLt($v);
			}
			return $value;
		}
		if (!is_string($value) || $value === '' || strpos($value, '<') === false) {
			return $value;
		}
		// Escape "<" that is not the start of a real HTML tag (e.g. "<4 нсек", "(<1 сек)").
		return preg_replace('/<(?![\/!?a-zA-Z])/', '&lt;', $value);
	}
}

if (!function_exists('oftalmagSanitizeContentHtml')) {
	function oftalmagSanitizeContentHtml($html)
	{
		if (!is_string($html) || $html === '') {
			return $html;
		}

		// Decode entity-encoded HTML (possibly double-encoded: &amp;lt;ul&amp;gt;).
		for ($i = 0; $i < 5; $i++) {
			if (strpos($html, '&lt;') === false && strpos($html, '&gt;') === false && strpos($html, '&amp;') === false) {
				break;
			}
			$decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
			if ($decoded === $html) {
				break;
			}
			$html = $decoded;
		}

		// Typos: </li<</ul>, </li<<, end tags with attributes.
		$html = preg_replace('/<\/([a-zA-Z][a-zA-Z0-9]*)<+/', '</$1>', $html);
		$html = preg_replace('/<\/([a-zA-Z][a-zA-Z0-9]*)\s+[^>]*>/', '</$1>', $html);

		// </p></ul><li> → </p><ul><li>
		$html = preg_replace('/<\/p>\s*<\/ul>\s*(<li\b)/i', '</p><ul>$1', $html);

		// Leading stray closers / trailing stray </span>
		$html = preg_replace('/^\s*<\/(p|span|div|li|ul|ol)>/i', '', $html);
		$html = preg_replace('/<\/span>\s*$/i', '', $html);

		// <ul>Label<li>… → <ul><li>Label</li><li>…
		$html = preg_replace('/(<ul\b[^>]*>)\s*([^<\s][^<]*?)\s*(<li\b)/iu', '$1<li>$2</li>$3', $html);

		// Missing open <li>: …</li>text;</li> or <ul>text;</li>
		$html = preg_replace('/(<ul\b[^>]*>\s*)([^<\n][^<]*<\/li>)/iu', '$1<li>$2', $html);
		$html = preg_replace('/(<\/li>\s*)([^<\s][^<]*<\/li>)/iu', '$1<li>$2', $html);
		$html = preg_replace('/<li[^>]*>\s*<\/li>\s*([^<]+)<\/li>/iu', '<li>$1</li>', $html);
		$html = preg_replace('/<li[^>]*>\s*<\/li>\s*/i', '', $html);

		// <hN><p>title</p>…blocks…</hN> → <hN>title</hN>…blocks…
		$html = preg_replace_callback(
			'/<h([1-6])\b([^>]*)>([\s\S]*?)<\/h\1>/i',
			static function ($m) {
				$inner = $m[3];
				if (!preg_match('/<(ul|ol|div|table)\b/i', $inner) && substr_count(strtolower($inner), '<p') <= 1) {
					// Simple <hN><p>…</p></hN>
					if (preg_match('/^\s*<p\b[^>]*>([\s\S]*?)<\/p>\s*$/i', $inner, $im)) {
						return '<h'.$m[1].$m[2].'>'.trim($im[1]).'</h'.$m[1].'>';
					}
					return $m[0];
				}
				if (preg_match('/^\s*<p\b[^>]*>([\s\S]*?)<\/p>\s*([\s\S]*)$/i', $inner, $im)) {
					return '<h'.$m[1].$m[2].'>'.trim(strip_tags($im[1])).'</h'.$m[1].'>'.$im[2];
				}
				return $inner;
			},
			$html
		);

		// <li>…</li><ul>…</ul> → <li>…<ul>…</ul></li>
		$prev = null;
		while ($prev !== $html) {
			$prev = $html;
			$html = preg_replace(
				'/(<li\b[^>]*>)([\s\S]*?)<\/li>\s*(<ul\b[\s\S]*?<\/ul>)/i',
				'$1$2$3</li>',
				$html
			);
		}

		// Drop empty list placeholders left inside broken headings.
		$html = preg_replace('/<ul\b[^>]*>\s*<\/ul>/i', '', $html);

		return oftalmagEscapeBareLt($html);
	}
}
