<?
use \Arturgolubev\Cssinliner\Webp;
use \Arturgolubev\Cssinliner\OptimizeProcess;
use \Arturgolubev\Cssinliner\Unitools as UTools;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$module_id = 'arturgolubev.cssinliner';
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/options.php");

$APPLICATION->SetTitle(GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_TITLE"));


if(CModule::IncludeModule($module_id)):
	\Bitrix\Main\UI\Extension::load("ui.progressbar");
	
	$action = $_REQUEST['action'];
	CJSCore::Init(array("ag_cssinliner_admin_image"));


	if($action == 'optimize'){		
		$APPLICATION->RestartBuffer();
		
		$result = array();
		$result['start'] = IntVal($_REQUEST['last']);
		$result['last'] = $result['optimize'] = $result['skip'] = $result['cnt'] = 0;
		$result['message'] = '';
		
		$time_start = microtime(true); 
		
		for($i = 0; $i < OptimizeProcess::ITERATION_MAX; $i++){
			if((microtime(true) - $time_start) > Webp::MAX_WORK_TIME) break;
			
			$result = OptimizeProcess::oneRowOptimize($result);
			
			if($result['optimize']) break;
		}
		
		$result['end_time'] = round((microtime(true) - $time_start), 4);
		
		if(!$result['last']){
			UTools::setSetting('last_optimize', date('d.m.Y H:s'));
			$result['message'] = GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_END");
		}else{
			$result['next'] = OptimizeProcess::getNextNeedOptimize($result);
		}
		
		ob_start();
			if(!$result['last']){
				$progress_title = GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_END");
			}else{
				$progress_title = GetMessage('ARTURGOLUBEV_CSSINLINER_OPTIMIZE_PROCESS_START');
			}
		
			CAdminMessage::ShowMessage(array(
				"TYPE" => "PROGRESS",
				"MESSAGE" => $progress_title,
				"DETAILS" => GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_CURRENT_OF", array('#full#' => $_REQUEST['full_count'], '#progress#' => $_REQUEST['progress'])).
					'<br/><br/> #PROGRESS_BAR# <br/>'.
					'<div class="">'.GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_OPTIMIZED", array('#optimized#' => $_REQUEST['now_optimized'])).'</div>'.
					'<div class="">'.GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_NOT_OPTIMIZED", array('#skiped#' => $_REQUEST['now_skiped'])).'</div>',
				"HTML" => true,
				"PROGRESS_TOTAL" => $_REQUEST['full_count'],
				"PROGRESS_VALUE" => $_REQUEST['progress'],
			));
			
			
			$result['progress'] = ob_get_contents();
		ob_end_clean();
		
		/* if($last > 30){
			asd();
			die();
		} */
		
		echo \Bitrix\Main\Web\Json::encode($result);
		die();
	}

	$webpCheck = Webp::libTest();
	$fullCount = OptimizeProcess::getImageRowCount();
	$lastOptimizeDate = UTools::getSetting('last_optimize');
?>
	<div class="agci_admin_page">
		<div class="line"><?=GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_DESCRIPTION")?></div>
		<div class="line"><?=GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_IMAGE_TABLE_SIZE", array('#full#' => $fullCount))?></div>
		
		<?if($webpCheck):?>
			<?if($lastOptimizeDate):?>
				<div class="line"><?=GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_LAST_RUN", array('#last#' => $lastOptimizeDate))?></div>
			<?endif;?>
			
			<div class="">
				<span class="js-agci-start-optimize btn main-grid-buttons" data-atext="<?=GetMessage('ARTURGOLUBEV_CSSINLINER_OPTIMIZE_START_OPTIMIZE_BTN_NEXT')?>"><?=GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_START_OPTIMIZE_BTN")?></span>
				<span class="js-agci-stop-optimize btn main-grid-buttons" style="display: none;"><?=GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_STOP_OPTIMIZE_BTN")?></span>
			</div>
			
			<div class="js-agci-optimize-table optimize-table" style="display: none;">
				<?
				CAdminMessage::ShowMessage(array(
					"TYPE" => "PROGRESS",
					"MESSAGE" => $progress_title,
					"DETAILS" => GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_CURRENT_OF", array('#full#' => $fullCount, '#progress#' => 0)).
						'<br/><br/> #PROGRESS_BAR# <br/>'.
						'<div class="">'.GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_OPTIMIZED", array('#optimized#' => 0)).'</div>'.
						'<div class="">'.GetMessage("ARTURGOLUBEV_CSSINLINER_OPTIMIZE_NOT_OPTIMIZED", array('#skiped#' => 0)).'</div>',
					"HTML" => true,
					"PROGRESS_TOTAL" => $fullCount,
					"PROGRESS_VALUE" => 0,
				));
				?>
			</div>
			
			<script>
				var full_image_count = <?=$fullCount?>;
				var now_image_progress = 0;
				var now_optimized = 0;
				var now_skiped = 0;
			</script>
		<?else:?>
			<div class="line"><b><?=GetMessage("ARTURGOLUBEV_CSSINLINER_NOTE_CWEBP_NOT_FOUND")?></b></div>
		<?endif;?>
	</div>
<?else:?>
	<?CAdminMessage::ShowMessage(array("DETAILS"=>GetMessage("ARTURGOLUBEV_CSSINLINER_DEMO_IS_EXPIRED"), "HTML"=>true));?>
<?endif;?>

<?require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');?>