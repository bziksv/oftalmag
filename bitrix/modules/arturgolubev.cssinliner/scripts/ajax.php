<?
use \Arturgolubev\Cssinliner\Webp;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC','Y');
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
define('PERFMON_STOP', true);

set_time_limit(30); 
@ignore_user_abort(true);
define("LANG", "ru"); 

$module_id = 'arturgolubev.cssinliner';
if(!CModule::IncludeModule($module_id)) die('No module');

$action = $_REQUEST['action'];

// echo '<pre>'; print_r($_REQUEST); echo '</pre>';

if($action == 'convert_webp'){
	$path = $_REQUEST['path'];
	$images = $_REQUEST['images'];
	
	if(is_array($images) && count($images)){
		$siteDomain = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
		
		$timeStart = microtime(true);
		
		$images = array_unique($images);
		foreach($images as $image){
			if((microtime(true) - $timeStart) > Webp::MAX_WORK_TIME) break;
			
			if(strpos($image, $siteDomain) !== false){
				$image = str_replace($siteDomain, '', $image);
			}
			
			$image = Webp::imageUrlPrepare($image, $path);
			
			Webp::make($image);
			
			// echo '<pre>'; print_r($image); echo '</pre>';
		}
		
		// echo 'Convert Work Time: '.(microtime(true) - $timeStart);
	}
}