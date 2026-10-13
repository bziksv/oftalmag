<?php
/**
 * Soft HTML cleanup for iblock DETAIL/PREVIEW text and property values.
 * Fixes common validator issues without rewriting editorial content wholesale.
 */
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

		// </p></ul><li> → </p><ul><li>
		$html = preg_replace('/<\/p>\s*<\/ul>\s*(<li\b)/i', '</p><ul>$1', $html);

		// <h2><p>…</p></h2> → <h2>…</h2>
		$html = preg_replace(
			'/<h([1-6])\b([^>]*)>\s*<p\b[^>]*>([\s\S]*?)<\/p>\s*<\/h\1>/i',
			'<h$1$2>$3</h$1>',
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

		return oftalmagEscapeBareLt($html);
	}
}
