<?
global $USER;
if(!is_object($USER)){
	$USER = new \CUser();
}
if($USER->IsAdmin()){
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/arturgolubev.cssinliner/menu.php");

	$arSubmenu[] = array(
		'text' => GetMessage("ARTURGOLUBEV_CSSINLINER_SUBMENU_SETTINGS"),
		'more_url' => array(),
		'url' => '/bitrix/admin/settings.php?lang=ru&mid=arturgolubev.cssinliner',
		'icon' => 'sys_menu_icon',
	);

	$arSubmenu[] = array(
		'text' => GetMessage("ARTURGOLUBEV_CSSINLINER_SUBMENU_IMAGE_OPTIMIZE"),
		'more_url' => array(),
		'url' => '/bitrix/admin/arturgolubev_cssinliner_image_optimize.php',
		'icon' => 'sys_menu_icon',
	);

	$aMenu = array(
		'parent_menu' => 'global_menu_services',
		'section' => 'ARTURGOLUBEV_CSSINLINER',
		'sort' => 1,
		'text' => GetMessage("ARTURGOLUBEV_CSSINLINER_MENU_MAIN"),
		'icon' => 'arturgolubev_cssinliner_icon_main',
		'items_id' => 'arci_icon_main',
		'items' => $arSubmenu,
	);


	return $aMenu;
}