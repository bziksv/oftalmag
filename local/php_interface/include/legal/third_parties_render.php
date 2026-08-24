<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

require_once __DIR__ . '/legal_export_helpers.php';

function oftalmagLegalThirdPartiesData(): array
{
	static $data = null;
	if ($data === null) {
		$data = include __DIR__ . '/third_parties_data.php';
	}

	return $data;
}

function oftalmagLegalRenderThirdPartyUrlsText(array $item): string
{
	$links = [];
	foreach ($item['urls'] as $url) {
		$links[] = '<a href="' . legal_h($url) . '" target="_blank" rel="noopener">'
			. legal_var($url) . '</a>';
	}

	return implode(', ', $links) . ' — ' . legal_var($item['text']);
}

function oftalmagLegalRenderThirdPartyItemLine(array $item, ?array $service = null): string
{
	$line = oftalmagLegalRenderThirdPartyUrlsText($item);
	if ($service === null) {
		return $line;
	}

	$prefix = legal_var($service['name']);
	if (!empty($service['inn'])) {
		$prefix .= ' (ИНН ' . legal_var($service['inn']) . ')';
	}

	return $prefix . ' — ' . $line;
}
