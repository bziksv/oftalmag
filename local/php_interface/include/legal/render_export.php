<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

function oftalmagLegalExportDocuments(): array
{
	return [
		'personal_data' => [
			'file' => 'personal_data_content.php',
			'image' => 'politics-oftalmag.png',
		],
		'consent' => [
			'file' => 'consent_content.php',
			'image' => 'consent-oftalmag.png',
		],
		'cookie' => [
			'file' => 'cookie_content.php',
			'image' => 'cookies-oftalmag.png',
		],
		'recommendation' => [
			'file' => 'recommendation_content.php',
			'image' => 'recommendations-oftalmag.png',
		],
	];
}

function oftalmagRenderLegalExportPage(string $docKey): void
{
	$documents = oftalmagLegalExportDocuments();
	if (!isset($documents[$docKey])) {
		http_response_code(404);
		echo 'Unknown document';
		return;
	}

	$contentInclude = $documents[$docKey]['file'];
	$cssPath = SITE_TEMPLATE_PATH . '/css/legal.css';

	header('Content-Type: text/html; charset=UTF-8');
	header('X-Robots-Tag: noindex, nofollow');

	echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8">';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
	echo '<link rel="stylesheet" href="' . htmlspecialcharsbx($cssPath) . '">';
	echo '<style>
		html, body { margin: 0; padding: 0; background: #fff; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; text-rendering: optimizeLegibility; }
		.legal-export { box-sizing: border-box; width: 1000px; max-width: 100%; margin: 0; padding: 36px 40px 40px; color: #253746; font-size: 16px; line-height: 1.65; }
		.legal-export .legal-doc p { margin: 0 0 14px; }
		.legal-export .legal-doc__related { margin-bottom: 24px; font-size: 15px; }
		.legal-export .legal-table { font-size: 14px; }
	</style></head><body>';
	echo '<div class="legal-export">';

	include $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/' . $contentInclude;

	echo '</div></body></html>';
}
