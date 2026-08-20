<?php

namespace Oftalmag;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\Cookie;

/**
 * Доступ к странице успешного оформления после истечения PHP-сессии.
 *
 * sale.order.ajax показывает заказ только если USER_ID совпадает
 * или ID есть в $_SESSION['SALE_ORDER_ID']. После таймаута сессии
 * гость/авто-регистрация видит «Заказ не найден», хотя заказ в БД есть.
 */
class OrderAccess
{
	public const COOKIE_NAME = 'OFT_ORDER_ACCESS';
	public const TTL = 2592000; // 30 дней
	public const MAX_IDS = 40;

	public static function onBeforeProlog(): void
	{
		self::restoreToSession();
	}

	/**
	 * @param int|string $orderId
	 * @param array $arOrder
	 * @param array $arParams
	 */
	public static function onOrderComplete($orderId, $arOrder = [], $arParams = []): void
	{
		self::remember((int)$orderId);
	}

	public static function remember(int $orderId): void
	{
		if($orderId <= 0)
			return;

		$ids = self::readIds();
		$ids[] = $orderId;
		$ids = array_values(array_unique(array_map('intval', $ids)));
		$ids = array_values(array_filter($ids, static function($id) {
			return $id > 0;
		}));
		if(count($ids) > self::MAX_IDS)
			$ids = array_slice($ids, -self::MAX_IDS);

		self::writeCookie($ids);
		self::mergeIntoSession($ids);
	}

	public static function restoreToSession(): void
	{
		$ids = self::readIds();
		if(!empty($ids))
			self::mergeIntoSession($ids);
	}

	/**
	 * @param int[] $ids
	 */
	protected static function mergeIntoSession(array $ids): void
	{
		try
		{
			$session = Application::getInstance()->getSession();
		}
		catch(\Throwable $e)
		{
			return;
		}

		if(!$session->isAccessible())
			return;

		$existing = $session->get('SALE_ORDER_ID');
		if(!is_array($existing))
			$existing = [];

		$merged = array_values(array_unique(array_map('intval', array_merge($existing, $ids))));
		$session->set('SALE_ORDER_ID', $merged);
	}

	/**
	 * @return int[]
	 */
	protected static function readIds(): array
	{
		$raw = isset($_COOKIE[self::COOKIE_NAME]) ? (string)$_COOKIE[self::COOKIE_NAME] : '';
		if($raw === '')
			return [];

		$parts = explode('.', $raw, 2);
		if(count($parts) !== 2)
			return [];

		[$payload, $sign] = $parts;
		if($payload === '' || $sign === '')
			return [];

		$expected = hash_hmac('sha256', $payload, self::getSecret());
		if(!hash_equals($expected, $sign))
			return [];

		$decoded = json_decode(base64_decode($payload, true) ?: '', true);
		if(!is_array($decoded) || empty($decoded['ids']) || !is_array($decoded['ids']))
			return [];

		$exp = isset($decoded['exp']) ? (int)$decoded['exp'] : 0;
		if($exp > 0 && $exp < time())
			return [];

		return array_values(array_filter(array_map('intval', $decoded['ids']), static function($id) {
			return $id > 0;
		}));
	}

	/**
	 * @param int[] $ids
	 */
	protected static function writeCookie(array $ids): void
	{
		$payloadData = [
			'ids' => array_values($ids),
			'exp' => time() + self::TTL,
		];
		$payload = base64_encode(json_encode($payloadData, JSON_UNESCAPED_UNICODE));
		$value = $payload.'.'.hash_hmac('sha256', $payload, self::getSecret());

		$_COOKIE[self::COOKIE_NAME] = $value;

		$cookie = new Cookie(self::COOKIE_NAME, $value, time() + self::TTL);
		$cookie->setHttpOnly(true);
		$cookie->setPath('/');
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
			|| (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
			|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'))
		{
			$cookie->setSecure(true);
		}

		try
		{
			Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
		}
		catch(\Throwable $e)
		{
			setcookie(self::COOKIE_NAME, $value, [
				'expires' => time() + self::TTL,
				'path' => '/',
				'httponly' => true,
				'samesite' => 'Lax',
			]);
		}
	}

	protected static function getSecret(): string
	{
		$secret = (string)Option::get('main', '~crypto', '');
		if($secret === '')
			$secret = (string)Option::get('main', 'server_uniq_id', '');
		if($secret === '')
			$secret = 'oftalmag-order-access';

		return $secret;
	}
}
