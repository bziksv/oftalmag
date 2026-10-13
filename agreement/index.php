<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Соглашение на обработку персональных данных | Интернет-магазин медицинского оборудования Оftalmag");
$APPLICATION->SetTitle("Соглашение на обработку персональных данных");

use Bitrix\Main\Context;
use Bitrix\Main\UserConsent\Agreement;

$id = Context::getCurrent()->getRequest()->get("id");
$agreementText = "";

if ($id) {
	$agreement = new Agreement($id);
	if ($agreement->isExist() && $agreement->isActive()) {
		$agreement->setReplace(array("fields" => array("Имя", "Телефон", "IP-адрес")));
		$agreementText = $agreement->getText();
	}
}

if ($agreementText !== "") {
	?><p><?=nl2br(htmlspecialcharsbx($agreementText))?></p><?
} else {
	CHTTP::SetStatus("404 Not Found");
	?><p>Соглашение не найдено.</p><?
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
