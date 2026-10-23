<?php

namespace Oftalmag;

use Bitrix\Main\Application;
use Bitrix\Main\UserConsent\Consent;
use CEnext;

/**
 * Обязательное согласие на обработку ПДн при регистрации (/personal/?register=yes).
 */
class RegisterConsent
{
	private const ERROR_MESSAGE = 'Необходимо дать согласие на обработку персональных данных.';
	private const INPUT_NAME = 'USER_AGREEMENT';

	public static function onBeforeUserRegister(array &$arFields)
	{
		if (!self::isConsentRequired()) {
			return true;
		}

		$request = Application::getInstance()->getContext()->getRequest();
		if ($request->get(self::INPUT_NAME) === 'Y') {
			return true;
		}

		global $APPLICATION;
		$APPLICATION->ThrowException(self::ERROR_MESSAGE);

		return false;
	}

	public static function onAfterUserRegister(array &$arFields): void
	{
		$userId = (int)($arFields['USER_ID'] ?? 0);
		if ($userId <= 0) {
			return;
		}

		$agreementId = self::getAgreementId();
		if ($agreementId <= 0) {
			return;
		}

		$request = Application::getInstance()->getContext()->getRequest();
		if ($request->get(self::INPUT_NAME) !== 'Y') {
			return;
		}

		Consent::addByContext(
			$agreementId,
			'main/reg',
			'register',
			['USER_ID' => $userId]
		);
	}

	private static function isConsentRequired(): bool
	{
		if (!class_exists('CEnext')) {
			return false;
		}

		$settings = CEnext::GetFrontParametrsValues(SITE_ID);

		return ($settings['FORMS_USER_CONSENT'] ?? 'N') === 'Y';
	}

	private static function getAgreementId(): int
	{
		if (!class_exists('CEnext')) {
			return 0;
		}

		$settings = CEnext::GetFrontParametrsValues(SITE_ID);

		return (int)($settings['FORMS_USER_CONSENT_ID'] ?? 0);
	}
}
