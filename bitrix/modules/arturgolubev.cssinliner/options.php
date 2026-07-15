<?
use \Arturgolubev\Cssinliner\Settings as Settings;
use \Arturgolubev\Cssinliner\Unitools as UTools;
use \Arturgolubev\Cssinliner\Tools;
use \Arturgolubev\Cssinliner\Webp;

$module_id = 'arturgolubev.cssinliner';
$module_name = str_replace('.', '_', $module_id);

if(!CModule::IncludeModule($module_id)){
	include 'autoload.php';
}

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/options.php");

global $USER, $APPLICATION;
if (!$USER->IsAdmin()) return;

// webp test
$webpWork = Webp::libTest();
if($webpWork){
	exec(Webp::getWebpPath().' -version', $webpLibVersion);
}

$arWebpAlgoritm = [
	'base' => GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_ALGORITM_STANDART"),
	'' => GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_ALGORITM_LOSSLESS"),
];


$arType = array(
	"sddefault" => GetMessage("ARTURGOLUBEV_CSSINLINER_PREVIEW_TYPE_SD")." (sddefault)",
	"hqdefault" => GetMessage("ARTURGOLUBEV_CSSINLINER_PREVIEW_TYPE_HQ")." (hqdefault)",
	"mqdefault" => GetMessage("ARTURGOLUBEV_CSSINLINER_PREVIEW_TYPE_MQ")." (mqdefault)",
	"maxresdefault" => GetMessage("ARTURGOLUBEV_CSSINLINER_PREVIEW_TYPE_MAX")." (maxresdefault)",
);

if($webpLibVersion[0]){
	$arWebpLibVersion = explode('.', $webpLibVersion[0]);
	if($arWebpLibVersion[1] >= 6){
		$arWebpAlgoritm['near'] =  GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_ALGORITM_NEARLESS");
	}
}

$siteList = Settings::getSites();

$arOptions = array(
    "main" => array(),
	"help" => array()
);
$arTabs = array();

$arOptions["main"][] = array("disable", GetMessage("ARTURGOLUBEV_CSSINLINER_ENABLE"), "N", array("checkbox"));
$arOptions["main"][] = GetMessage("ARTURGOLUBEV_CSSINLINER_WORKING_WITH_STYLE");
$arOptions["main"][] = array("styles_work_mode", GetMessage("ARTURGOLUBEV_CSSINLINER_STYLES_WORK_MODE"), "unite", array("selectbox", array(
	"unite" => GetMessage("ARTURGOLUBEV_CSSINLINER_STYLES_WORK_MODE_UNITE"),
	"inline" => GetMessage("ARTURGOLUBEV_CSSINLINER_STYLES_WORK_MODE_INLINE"),
)));

$arOptions["main"][] = GetMessage("ARTURGOLUBEV_CSSINLINER_WEBP_MAIN_SETTINGS");
$arOptions["main"][] = array("webp_ctype", GetMessage("ARTURGOLUBEV_CSSINLINER_WEBP_CHECK_TYPE"), "", array("selectbox", array(
	'' => GetMessage("ARTURGOLUBEV_CSSINLINER_WEBP_CHECK_TYPE_HIT"),
	'post' => GetMessage("ARTURGOLUBEV_CSSINLINER_WEBP_CHECK_TYPE_POST"),
)));
$arOptions["main"][] = array("webp_algoritm", GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_ALGORITM"), "base", array("selectbox", $arWebpAlgoritm));
$arOptions["main"][] = array("webp_qt", GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_QT"), "100", array("selectbox", array(100 => '100%', 95 => '95%', 90 => '90%', 85 => '85%', 80 => '80%', 75 => '75% '.GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_OP_REK"), 70 => '70%', 60 => '60%', 50 => '50%', 40 => '40%', 30 => '30%', 20 => '20%', 10 => '10%')));
$arOptions["main"][] = array("webp_cm", GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_OP"), "4", array("selectbox", array(6 => '6', 5 => '5', 4 => '4 '.GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_OP_REK"), 3 => '3', 2 => '2', 1 => '1')));
$arOptions["main"][] = array("webp_path", GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_PATH"), "", array("text"));


$arTabs[] = array("DIV" => "inliner_tab", "TAB" => GetMessage("ARTURGOLUBEV_CSSINLINER_CSS_INLINE_TAB"), "TITLE" => GetMessage("ARTURGOLUBEV_CSSINLINER_CSS_INLINE_TAB"), "OPTIONS"=>"main");

if(count($siteList))
{
	foreach($siteList as $arSite)
	{
		$tabCode = "main_".$arSite["ID"];
		$arOptions[$tabCode] = array();
		
		$arOptions[$tabCode][] = array("disabled_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_DISABLED_SITE")." <b>".$arSite["NAME"]." [".$arSite["ID"]."]</b>:", "N", array("checkbox"));
		
		$arOptions[$tabCode][] = GetMessage("ARTURGOLUBEV_CSSINLINER_WORKING_WITH_STYLE");
			$arOptions[$tabCode][] = array("optimize_css_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_STYLES_WORK_ON"), "N", array("checkbox"));
			$arOptions[$tabCode][] = array("outer_style_inline_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_OUTER_STYLE_INLINE"), "N", array("checkbox"));
			$arOptions[$tabCode][] = array("use_compress_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_COMPRESS"), "N", array("checkbox"));
			$arOptions[$tabCode][] = array("exceptions_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_EXCEPTIONS"), "", array("textarea",5,40));
			$arOptions[$tabCode][] = array("use_font_display_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_FONT_DISPLAY"), "N", array("checkbox"));
			$arOptions[$tabCode][] = array("del_open_sans_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_DELETE_OPEN_SANS"), "N", array("checkbox"));
		
		$arOptions[$tabCode][] = GetMessage("ARTURGOLUBEV_CSSINLINER_IMAGE_OPTIMIZATION");
		if($webpWork){
			$lastOptimize = COption::GetOptionString($module_id, 'last_optimize');
			if(!$lastOptimize){
				$arOptions[$tabCode][] = array("statictext", GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_OPTIMIZE"), GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_OPTIMIZE_VALUE"), array("statictext"));
			}
			
			$arOptions[$tabCode][] = array("webp_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP"), "N", array("checkbox"));

			$arOptions[$tabCode][] = array("webp_skip_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_SKIP"), "data-webp-skip", array("textarea", 4, 40), false, GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_SKIP_HINT"));
			
			if(UTools::getSettingDB('webp_ctype') == 'post'){
				$arOptions[$tabCode][] = array("webp_attr_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_ATTR"), "data-src\ndata-bg\ndata-background", array("textarea", 4, 40), false, GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_ATTR_HINT"));
			}else{
				$arOptions[$tabCode][] = array("webp_regex_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_REGEX"), "", array("textarea", 4, 40), false, GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP_REGEX_HINT"));
			}
		}else{
			$arOptions[$tabCode][] = array("statictext", GetMessage("ARTURGOLUBEV_CSSINLINER_USE_WEBP"), GetMessage("ARTURGOLUBEV_CSSINLINER_NOTE_CWEBP_NOT_FOUND"), array("statictext"));
		}
		
		$arOptions[$tabCode][] = GetMessage("ARTURGOLUBEV_CSSINLINER_VIDEO_OPTIMIZATION");
		$arOptions[$tabCode][] = array("video_optimization_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_VIDEO_OPTIMIZATION"), "N", array("checkbox"));
		$arOptions[$tabCode][] = array("video_previewtype_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_VIDEO_PREVIEWTYPE"), "N", array("selectbox", $arType));

		$arOptions[$tabCode][] = GetMessage("ARTURGOLUBEV_CSSINLINER_JS_OPTIMIZATION");
		// $arOptions[$tabCode][] = array("enable_jsopt_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_ENABLE_JS_OPTIMIZE"), "N", array("checkbox"));
		$arOptions[$tabCode][] = array("js_passive_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_USE_JS_PASSIVE"), "N", array("checkbox"));
			
		$path = "/bitrix/tools/arturgolubev.cssinliner/".$arSite["ID"]."/lazyjs.php";
		$file = new \Bitrix\Main\IO\File($_SERVER["DOCUMENT_ROOT"].$path);
		if(!$file->isExists()){
			$file->putContents('');
		}
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$path)){
			$arOptions[$tabCode][] = array("statictext", GetMessage("ARTURGOLUBEV_CSSINLINER_LAZY_JS_FILE"), GetMessage("ARTURGOLUBEV_CSSINLINER_LAZY_JS_FILE_VALUE", array("#PATH#" => urlencode($path))), array("statictext"), false, GetMessage( "ARTURGOLUBEV_CSSINLINER_LAZY_JS_FILE_HINT"));
		}
		
		$path = "/bitrix/tools/arturgolubev.cssinliner/".$arSite["ID"]."/actionjs.php";
		$file = new \Bitrix\Main\IO\File($_SERVER["DOCUMENT_ROOT"].$path);
		if(!$file->isExists()){
			$file->putContents('');
		}
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$path)){
			$arOptions[$tabCode][] = array("statictext", GetMessage("ARTURGOLUBEV_CSSINLINER_ACTION_JS_FILE"), GetMessage("ARTURGOLUBEV_CSSINLINER_ACTION_JS_FILE_VALUE", array("#PATH#" => urlencode($path))), array("statictext"), false, GetMessage( "ARTURGOLUBEV_CSSINLINER_ACTION_JS_FILE_HINT"));
		}
		
		
		$arOptions[$tabCode][] = GetMessage("ARTURGOLUBEV_CSSINLINER_BOT_VERSON");
		
		$arOptions[$tabCode][] = array("bot_clear_system_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_BOT_CLEAR_SYSTEM"), "N", array("checkbox"));
		$path = "/bitrix/tools/arturgolubev.cssinliner/".$arSite["ID"]."/nobotjs.php";
		$file = new \Bitrix\Main\IO\File($_SERVER["DOCUMENT_ROOT"].$path);
		if(!$file->isExists()){
			$file->putContents('');
		}
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$path)){
			$arOptions[$tabCode][] = array("statictext", GetMessage("ARTURGOLUBEV_CSSINLINER_NOBOT_JS_FILE"), GetMessage("ARTURGOLUBEV_CSSINLINER_NOBOT_JS_FILE_VALUE", array("#PATH#" => urlencode($path))), array("statictext"), false, GetMessage( "ARTURGOLUBEV_CSSINLINER_NOBOT_JS_FILE_HINT"));
			
			/* if(!Tools::onComposite()){
			}else{
				$arOptions[$tabCode][] = array("statictext", GetMessage("ARTURGOLUBEV_CSSINLINER_NOBOT_JS_FILE"), GetMessage("ARTURGOLUBEV_CSSINLINER_BOT_MODE_HTMLCACHE", array("#PATH#" => urlencode($path))), array("statictext"), false, GetMessage( "ARTURGOLUBEV_CSSINLINER_NOBOT_JS_FILE_HINT"));
			} */
		}

		$arOptions[$tabCode][] = GetMessage("ARTURGOLUBEV_CSSINLINER_MAIN_OPTIMIZATION");
		$arOptions[$tabCode][] = array("preconnect_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_PRECONNECT"), "", array("textarea",5,40));
		$arOptions[$tabCode][] = array("preloading_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_PRELOADING"), "", array("textarea",5,40));

		$arOptions[$tabCode][] = GetMessage("ARTURGOLUBEV_CSSINLINER_ASPRO_OPTIMIZATION");
		// $arOptions[$tabCode][] = array("aspro_theme_cache_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_ASPRO_THEME_CACHE"), "N", array("checkbox"));
		$arOptions[$tabCode][] = array("aspro_script_mooving_".$arSite["ID"], GetMessage("ARTURGOLUBEV_CSSINLINER_ASPRO_SCRIPT_MOOVING"), "N", array("checkbox"));
		
		$arTabs[] = array("DIV" => $module_name."_main_".$arSite["ID"], "TAB" => $arSite["NAME"].' ['.$arSite["ID"].']', "TITLE" => GetMessage("ARTURGOLUBEV_CSSINLINER_SETTING_FOR").$arSite["NAME"].' ['.$arSite["ID"].']', "OPTIONS"=> $tabCode);
	}
}

$tabControl = new CAdminTabControl("tabControl", $arTabs);

// ****** SaveBlock
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply)>0 && check_bitrix_sessid())
{
	$oldAlgoritm = COption::GetOptionString($module_id, 'webp_algoritm');
	
	if($oldAlgoritm != $_REQUEST['webp_algoritm'] && is_dir($_SERVER["DOCUMENT_ROOT"].Webp::BASE_PATH)){		
		$ffs = scandir($_SERVER["DOCUMENT_ROOT"].Webp::BASE_PATH);
		if(count($ffs)>3){
			CAdminNotify::Add(array('MESSAGE' => GetMessage("ARTURGOLUBEV_CSSINLINER_CHANGE_WEBP_ALGORITM"),  'TAG' => $module_name."_webp_change", 'MODULE_ID' => $module_id, 'ENABLE_CLOSE' => 'Y'));
		}
	}
	
	if(UTools::onComposite()){
		CAdminNotify::Add(array('MESSAGE' => GetMessage("ARTURGOLUBEV_CSSINLINER_CLEAR_CACHE"),  'TAG' => $module_name."_clear_cache", 'MODULE_ID' => $module_id, 'ENABLE_CLOSE' => 'Y'));
	}
		
	if($_REQUEST['disable'] == 'Y'){
		UnRegisterModuleDependences('main', 'OnGetStaticCacheProvider', $module_id, '\Arturgolubev\Cssinliner\StaticCacheProvider', 'setCacheName');
	}else{
		RegisterModuleDependences('main', 'OnGetStaticCacheProvider', $module_id, '\Arturgolubev\Cssinliner\StaticCacheProvider', 'setCacheName', 510);
	}

	foreach ($arOptions as $aOptGroup) {
		foreach ($aOptGroup as $option) {
			__AdmSettingsSaveOption($module_id, $option);
		}
	}
	
    if (strlen($Update) > 0 && strlen($_REQUEST["back_url_settings"]) > 0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
}
?>

<?
// warnings
$arSearchNoteSettings = array();
if(COption::GetOptionString('main', "optimize_css_files") == "Y")
	$arSearchNoteSettings[] = GetMessage("ARTURGOLUBEV_CSSINLINER_MAINMODULE_OPTIMIZE_CSS");
	
if(COption::GetOptionString('main', "move_js_to_body") != "Y")
	$arSearchNoteSettings[] = GetMessage("ARTURGOLUBEV_CSSINLINER_NOTE_MAIN_JS");

if(count($arSearchNoteSettings)>0){
	CAdminMessage::ShowMessage(array("DETAILS"=>GetMessage("ARTURGOLUBEV_CSSINLINER_ERROS_SETTING_MESSAGE_START").implode('<br>', $arSearchNoteSettings), "MESSAGE" => GetMessage("ARTURGOLUBEV_CSSINLINER_ERROS_SETTING_TITLE"), "HTML"=>true));
}

$allow_url_fopen = ini_get("allow_url_fopen");
if(!$allow_url_fopen){
	CAdminMessage::ShowMessage(array("DETAILS"=>GetMessage("ARTURGOLUBEV_CSSINLINER_ALLOW_URL_FOPEN_NOT_FOUND_TEXT"), "MESSAGE" => GetMessage("ARTURGOLUBEV_CSSINLINER_ALLOW_URL_FOPEN_NOT_FOUND"), "HTML"=>true));
}

if(!CModule::IncludeModule($module_id)){
	CAdminMessage::ShowMessage(array("DETAILS"=>GetMessage("ARTURGOLUBEV_CSSINLINER_DEMO_IS_EXPIRED"), "HTML"=>true));
}
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
	<?$tabControl->Begin();?>
	
	<?foreach($arTabs as $key=>$tab):
		$tabControl->BeginNextTab();
			Settings::showSettingsList($module_id, $arOptions, $tab);
	endforeach;?>
	
	<?$tabControl->Buttons();?>
		<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
				
		<?if(strlen($_REQUEST["back_url_settings"])>0):?>
			<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
		<?endif?>
		
		<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
</form>

<?Settings::showInitUI();?>


<div class="help_note_wrap">
	<?= BeginNote();?>
		<p class="admin-info" style="font-weight: 500; font-size: 16px;"><?=GetMessage("ARTURGOLUBEV_CSSINLINER_HELP_TAB_ADMIN_INFO")?></p><br/>
		<p class="title"><?=GetMessage("ARTURGOLUBEV_CSSINLINER_HELP_TAB_TITLE")?></p>
		<p><?=GetMessage("ARTURGOLUBEV_CSSINLINER_HELP_TAB_VALUE")?></p>
		
		<?if(is_array($webpLibVersion) && $webpLibVersion[0]):?>
			--
			<p><?=GetMessage("ARTURGOLUBEV_CSSINLINER_HELP_CWEBP_VERSION")?> <?=$webpLibVersion[0]?></p>
		<?endif;?>
	<?= EndNote();?>
</div>
