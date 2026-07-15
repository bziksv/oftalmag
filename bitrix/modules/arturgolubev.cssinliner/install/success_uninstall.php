<?if(!check_bitrix_sessid()) return;?>

<?global $APPLICATION;
$APPLICATION->SetTitle(GetMessage("ARTURGOLUBEV_CSSINLINER_UNINSTALL_SUCCESS", array("#MODULE_NAME#" => GetMessage("arturgolubev.cssinliner_MODULE_NAME"))));
?>

<?echo CAdminMessage::ShowNote(GetMessage("ARTURGOLUBEV_CSSINLINER_UNINSTALL_SUCCESS", array("#MODULE_NAME#"=>GetMessage("arturgolubev.cssinliner_MODULE_NAME"))));?>

<div><?=GetMessage("ARTURGOLUBEV_CSSINLINER_WHAT_DO_TEXT_UN");?></div>