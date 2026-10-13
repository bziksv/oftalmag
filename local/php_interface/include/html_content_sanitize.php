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

if (!function_exists('oftalmagNormalizeHeadingLevels')) {
	/**
	 * Avoid skipped heading levels under page H1 (H1→H3/H4, H2→H4).
	 * 1) Compress used levels so the shallowest content heading maps to H2.
	 * 2) Walk document order and clamp jumps larger than +1 from the previous heading.
	 *
	 * @param int $baseLevel Previous level before content (1 = page H1).
	 */
	function oftalmagNormalizeHeadingLevels($html, $baseLevel = 1)
	{
		if (!is_string($html) || $html === '' || !preg_match('/<h[1-6]\b/i', $html)) {
			return $html;
		}
		$baseLevel = max(1, min(6, (int)$baseLevel));

		preg_match_all('/<h([1-6])\b/i', $html, $found);
		$used = array_values(array_unique(array_map('intval', $found[1] ?? [])));
		sort($used, SORT_NUMERIC);
		$map = [];
		$next = 2;
		foreach ($used as $level) {
			if ($level < 2) {
				$map[$level] = 2;
				continue;
			}
			$map[$level] = min(6, $next);
			$next++;
		}
		if ($map) {
			$html = preg_replace_callback(
				'/<h([1-6])(\b[^>]*)>([\s\S]*?)<\/h\1>/i',
				static function ($m) use ($map) {
					$level = $map[(int)$m[1]] ?? (int)$m[1];
					return '<h'.$level.$m[2].'>'.$m[3].'</h'.$level.'>';
				},
				$html
			);
		}

		$prev = $baseLevel;
		return preg_replace_callback(
			'/<h([1-6])(\b[^>]*)>([\s\S]*?)<\/h\1>/i',
			static function ($m) use (&$prev) {
				$level = (int)$m[1];
				if ($level > $prev + 1) {
					$level = $prev + 1;
				}
				$prev = $level;
				return '<h'.$level.$m[2].'>'.$m[3].'</h'.$level.'>';
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

		// Typos: </li<</ul> / </li<</ul> → </li></ul>
		$html = preg_replace('/<\/([a-zA-Z][a-zA-Z0-9]*)<(?=<\/)/', '</$1>', $html);
		$html = preg_replace('/<\/([a-zA-Z][a-zA-Z0-9]*)<+\//', '</$1></', $html);
		$html = preg_replace('/<\/([a-zA-Z][a-zA-Z0-9]*)\s+[^>]*>/', '</$1>', $html);
		$html = preg_replace('/(?<=[>\s])\/(ul|ol|li)>/i', '</$1>', $html);

		// Typo: </li><ul> instead of </li></ul> (empty/trailing nested open).
		$html = preg_replace('/<\/li>\s*<ul\b[^>]*>\s*(<\/(?:div|td|p|section|article)|$)/i', '</li></ul>$1', $html);
		$html = preg_replace('/<\/li>\s*<ul\b[^>]*>\s*<\/ul>/i', '</li>', $html);

		// <ol><ul>…</ul></ol> (ul — прямой потомок ol) → убрать лишнюю обёртку ul.
		$html = preg_replace_callback(
			'/<ol\b([^>]*)>\s*<ul\b[^>]*>([\s\S]*?)<\/ul>\s*<\/ol>/i',
			static function ($m) {
				return '<ol'.$m[1].'>'.$m[2].'</ol>';
			},
			$html
		);

		// </p></ul><li> → </p><ul><li>
		$html = preg_replace('/<\/p>\s*<\/ul>\s*(<li\b)/i', '</p><ul>$1', $html);

		// Leading text / inline HTML ending with stray </p> (alkom: text<br>…</p>).
		if (preg_match('/^(\s*)([\s\S]*?)<\/p>/u', $html, $m)
			&& !preg_match('/<p\b/i', $m[2])
			&& !preg_match('/<(div|h[1-6]|ul|ol|table|section|article)\b/i', $m[2])
		) {
			$html = $m[1].'<p>'.$m[2].'</p>'.substr($html, strlen($m[0]));
		} elseif (preg_match('/^(\s*)([^<\s][^<]*?)(?=\s*<h[1-6]|\s*<ul|\s*<ol|\s*<div|\s*<p|$)/u', $html, $m)) {
			$lead = trim($m[2]);
			if ($lead !== '') {
				$html = $m[1].'<p>'.$lead.'</p>'.substr($html, strlen($m[0]));
			}
		}

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

		// Orphan <li>…</li> without <ul>/<ol>:
		// - чистый фрагмент (свойства товара) → обернуть целиком;
		// - статья/описание с <p>/<hN>/table → обернуть только подряд идущие <li>.
		if (preg_match('/<li\b/i', $html) && !preg_match('/<(ul|ol)\b/i', $html)) {
			if (preg_match('/<(p|h[1-6]|div|table|section|article)\b/i', $html)) {
				$html = preg_replace(
					'/((?:<li\b[\s\S]*?<\/li>\s*)+)/iu',
					'<ul>$1</ul>',
					$html
				);
			} else {
				$html = '<ul>'.$html.'</ul>';
			}
		}

		// <hN><p>title</p>…blocks…</hN> → <hN>title</hN>…blocks…
		$html = preg_replace_callback(
			'/<h([1-6])\b([^>]*)>([\s\S]*?)<\/h\1>/i',
			static function ($m) {
				$inner = $m[3];
				if (!preg_match('/<(ul|ol|div|table)\b/i', $inner) && substr_count(strtolower($inner), '<p') <= 1) {
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

		// <li>текст</li><ul>…</ul> → <li>текст<ul>…</ul></li>
		// Не трогаем <li>, внутри которых уже есть вложенные списки/li.
		// Важно: до вставки </li> перед </ol>, иначе получится </ul></li></li></ol>.
		$prev = null;
		while ($prev !== $html) {
			$prev = $html;
			$html = preg_replace(
				'/(<li\b[^>]*>)((?:(?!<li\b|<ul\b|<ol\b)[\s\S])*?)<\/li>\s*(<ul\b[\s\S]*?<\/ul>)/i',
				'$1$2$3</li>',
				$html
			);
		}

		// После nest: <ul></ol> без </li> → </ul></li></ol>
		$html = preg_replace('/<\/ul>\s*(<\/ol>)/i', '</ul></li>$1', $html);
		$html = preg_replace('/<\/li>\s*<\/li>(\s*<\/ol>)/i', '</li>$1', $html);

		// Drop empty list placeholders left inside broken headings.
		$html = preg_replace('/<ul\b[^>]*>\s*<\/ul>/i', '', $html);

		// Contiguous heading outline under page H1.
		$html = oftalmagNormalizeHeadingLevels($html, 1);

		return oftalmagEscapeBareLt($html);
	}
}
