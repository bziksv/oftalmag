<?php

namespace Oftalmag;

class LegalRedirect
{
	public static function onBeforeProlog(): void
	{
		if (defined('ADMIN_SECTION') && ADMIN_SECTION === true) {
			return;
		}

		if (PHP_SAPI === 'cli') {
			return;
		}

		$uri = (string)($_SERVER['REQUEST_URI'] ?? '');
		if ($uri === '' || str_starts_with($uri, '/bitrix/admin/')) {
			return;
		}

		require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/redirects.php';
		oftalmagLegalRedirectPerform($uri);
	}
}
