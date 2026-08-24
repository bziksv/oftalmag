<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

require_once __DIR__ . '/third_parties_render.php';

$withOperatorPrefix = !empty($withOperatorPrefix);
if (!isset($thirdParties)) {
	$thirdParties = oftalmagLegalThirdPartiesData();
}

foreach ($thirdParties['services'] as $service) {
	if (empty($service['items'])) {
		continue;
	}
	foreach ($service['items'] as $item) {
		echo '<li' . legal_li_attr() . '>'
			. oftalmagLegalRenderThirdPartyItemLine($item, $withOperatorPrefix ? $service : null)
			. ';</li>';
	}
}
