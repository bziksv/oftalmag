<?php

namespace Oftalmag;

use Bitrix\Main\HttpRequest;

/**
 * Обязательное согласие на обработку ПДн при оформлении заказа (sale.order.ajax).
 */
class OrderConsent
{
	private const ERROR_MESSAGE = 'Необходимо дать согласие на обработку персональных данных.';

	public static function onOrderProperties(
		array &$arUserResult,
		HttpRequest $request,
		array &$arParams,
		array &$arResult
	): void {
		if (($arParams['USER_CONSENT'] ?? 'N') !== 'Y') {
			return;
		}

		if (!self::isSaveOrderAjax($request, $arParams)) {
			return;
		}

		if ($request->get('USER_CONSENT') !== 'Y') {
			self::addError($arResult);
		}
	}

	private static function isSaveOrderAjax(HttpRequest $request, array $arParams): bool
	{
		if ($request->get('action') === 'saveOrderAjax') {
			return true;
		}

		$actionVariable = (string)($arParams['ACTION_VARIABLE'] ?? 'soa-action');

		return $request->get($actionVariable) === 'saveOrderAjax';
	}

	private static function addError(array &$arResult): void
	{
		$arResult['ERROR'] ??= [];
		if (!in_array(self::ERROR_MESSAGE, $arResult['ERROR'], true)) {
			$arResult['ERROR'][] = self::ERROR_MESSAGE;
		}

		$arResult['ERROR_SORTED'] ??= [];
		$arResult['ERROR_SORTED']['MAIN'] ??= [];
		if (!in_array(self::ERROR_MESSAGE, $arResult['ERROR_SORTED']['MAIN'], true)) {
			$arResult['ERROR_SORTED']['MAIN'][] = self::ERROR_MESSAGE;
		}
	}
}
