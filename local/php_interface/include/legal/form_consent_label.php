<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$legalConfig = include $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/config.php';
?>
Нажимая на эту кнопку, я даю свое <a target="_blank" href="<?= htmlspecialcharsbx($legalConfig['images']['consent']) ?>">согласие на обработку персональных данных</a> и соглашаюсь с условиями <a target="_blank" href="<?= htmlspecialcharsbx($legalConfig['images']['personal_data']) ?>">политики обработки персональных данных</a>.
