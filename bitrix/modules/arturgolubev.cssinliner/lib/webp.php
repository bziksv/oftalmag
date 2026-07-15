<?
namespace Arturgolubev\Cssinliner;

use \Bitrix\Main\IO\File;

use \Arturgolubev\Cssinliner\Unitools as UTools;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/arturgolubev.cssinliner/include.php");

// https://developers.google.com/speed/webp/docs/cwebp
class Webp {
	const BASE_PATH = '/upload/cssinliner_webp';
	const TEST_IMAGE_PATH = '/bitrix/modules/arturgolubev.cssinliner/_selftest/dog.png';
	
	const DEBUG = 0;
	
	const MAX_WORK_TIME = 5;
	
	public static $allowExt = array(
		'jpg', 'jpeg', 'png',
		'JPG', 'JPEG', 'PNG',
	);
	
	static function checkApplyWebp($checkAdmin = 1){ // for outer checks
		if(UTools::isAdminPage() || UTools::getSetting('disable') == 'Y' || UTools::getSetting('disabled_'.SITE_ID) == 'Y') return 0;
		
		if($checkAdmin){
			if(UTools::isAdmin()) return 0;
		}
		
		return (UTools::getSiteSetting('webp') == 'Y');
	}
	
	static function getWebpPath(){
		$path = UTools::getSetting('webp_path');
		if(!$path)
			$path = 'cwebp';
		
		return $path;
	}
	
	static function libTest(){
		$start = microtime(true);
		
		$a = self::make(self::TEST_IMAGE_PATH);
		if($a && File::isFileExists($_SERVER["DOCUMENT_ROOT"].$a)){			
			if(self::DEBUG){
				echo '<pre>'; print_r('Test file size: ' . filesize($_SERVER["DOCUMENT_ROOT"].$a) / 1024); echo '</pre>';
				echo '<pre>'; print_r('TestImage create time: '.round(microtime(true) - $start, 4).'s'); echo '</pre>';
				
				$testOpt = 4;
				$testQt = 75;
				
				$times = [
					'base' => [
						'command' => '-m '.$testOpt.' -q '.$testQt,
						'end_file' => 'dog_base_'.$testOpt.'_'.$testQt.'.webp',
					],
					'nearless0' => [
						'command' => '-near_lossless 0 -m '.$testOpt.' -q '.$testQt,
						'end_file' => 'dog_nearless0_'.$testOpt.'_'.$testQt.'.webp',
					],
					'nearless100' => [
						'command' => '-near_lossless 100 -m '.$testOpt.' -q '.$testQt,
						'end_file' => 'dog_nearless100_'.$testOpt.'_'.$testQt.'.webp',
					],
					'lossless' => [
						'command' => '-lossless -m '.$testOpt.' -q '.$testQt,
						'end_file' => 'dog_loseless_'.$testOpt.'_'.$testQt.'.webp',
					],
				];
				
				$basePath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/arturgolubev.cssinliner/_selftest/';
				
				
				foreach($times as $k=>$v){
					// echo '<pre>'; print_r($k); echo '</pre>';
					// echo '<pre>'; print_r($v); echo '</pre>';
					
					$full_c = self::getWebpPath().' -mt '.$v['command'].' "'.$basePath.'test.jpg" -o "'.$basePath.$v['end_file'].'";';
					
					$start = microtime(true);
					exec($full_c);
					
					$times[$k]['fc'] = $full_c;
					$times[$k]['time'] = round(microtime(true) - $start, 2);
					$times[$k]['weight'] = round(filesize($basePath.$v['end_file']) / 1024, 2);
				}
				
				echo '<pre>'; print_r($times); echo '</pre>';
			}
			unlink($_SERVER["DOCUMENT_ROOT"].$a);
			return 1;
		}else{
			// todo deactivete
		}
		
		return 0;
	}
	
	static function getPathInfo($path){
		$info = pathinfo($path);
		if(self::checkCyrilic($path)){
			$info['filename'] = str_replace(array($info['dirname'].'/', '.'.$info['extension']), '', $path);
			$info['basename'] = str_replace(array($info['dirname'].'/'), '', $path);
		}

		/* // todo new pathinfo
		$file = new File($_SERVER["DOCUMENT_ROOT"].$path);
		$arInfo = [
			'dirname' => str_replace($_SERVER["DOCUMENT_ROOT"], '', $file->getDirectoryName()),
			'basename' => $file->getName(),
			'extension' => $file->getExtension(),
			'filename' => str_replace('.'.$file->getExtension(), '', $file->getName()),
		];
		*/
		
		if(substr($info['dirname'], 0, 1) != '/'){
			$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
			$info['dirname'] = $request->getRequestedPageDirectory().'/'.$info['dirname'];
			
			$info['new_path'] = $info['dirname'].'/'.$info['basename'];
		}
		
		$info['optimized_dir'] = self::BASE_PATH.((substr($info["dirname"], 0, 8) == '/upload/') ? substr($info["dirname"], 7) : $info["dirname"]);
		
		$info["optimized_filename"] = str_replace(['%25', '%', ' '], '_', $info["filename"]);
		$info['optimized_img'] = $info["optimized_dir"] . DIRECTORY_SEPARATOR . $info["optimized_filename"] .'.webp';
		

		// $file = new File($_SERVER["DOCUMENT_ROOT"].$path);
		// echo '<pre>'; print_r($file->getPhysicalPath()); echo '</pre>';

		$info['exist'] = [
			'base' => File::isFileExists($_SERVER["DOCUMENT_ROOT"].$path),
			'webp' => File::isFileExists($_SERVER["DOCUMENT_ROOT"].$info['optimized_img']),
		];

		return $info;
	}
		static function checkCyrilic($text){
			return preg_match('/['.GetMessage("ARTURGOLUBEV_CSSINLINER_CYRILIC_REGULAR").']/i'.BX_UTF_PCRE_MODIFIER, $text);
		}
	static function imageUrlPrepare($path, $basepath = ''){
		$pathInfo = self::getPathInfo($path);
		
		if($pathInfo['new_path']){
			$path = $pathInfo['new_path'];
		}
		
		if($pathInfo["dirname"] == '.'){
			if(!$basepath){
				global $APPLICATION;
				$basepath = $APPLICATION->GetCurDir();
			}
			$path = $basepath.$path;
		}
		
		if(strpos($pathInfo["filename"], '%') !== false){
			$path = urldecode($path);
		}
		
		return $path;
	}
	
	static function check($path){
		$path = self::imageUrlPrepare($path);
		$pathInfo = self::getPathInfo($path);
		
		if(!$pathInfo['exist']['base'] || !in_array($pathInfo["extension"], self::$allowExt))
			return 0;
		
		return $pathInfo['exist']['webp'];
	}
	
	static function make($path, $convert = 1){
		$path = self::imageUrlPrepare($path);
		$pathInfo = self::getPathInfo($path);

		if(!$pathInfo['exist']['base'] || !in_array($pathInfo["extension"], self::$allowExt))
			return 0;
		
		if($pathInfo['exist']['webp']){
			return $pathInfo['optimized_img'];
		}
		
		if(!$convert)
			return 0;
		
		if($pathInfo["optimized_dir"]){
			CheckDirPath($_SERVER["DOCUMENT_ROOT"].$pathInfo["optimized_dir"].'/');
		}
		
		return \CArturgolubevCssinliner::createWebpImage($path, $pathInfo['optimized_img'], self::getParams());
	}
	
	static function getParams(){
		if(isset(UTools::$storage['ci_webp']['params'])){
			$params = UTools::$storage['ci_webp']['params'];
		}else{
			$params = array(
				'path' => self::getWebpPath(),
				'type' => UTools::getSetting('webp_algoritm'),
				'quality' => (intval(UTools::getSetting('webp_qt'))) ? intval(UTools::getSetting('webp_qt')) : 100,
				'compression' => (strlen(UTools::getSetting('webp_cm'))) ? intval(UTools::getSetting('webp_cm')) : 4,
			);
			
			$params['cm_params'] = [
				'-mt'
			];
			
			if($params['type'] == 'base'){
				
			}elseif($params['type'] == 'near'){
				$params['cm_params'][] = '-near_lossless 0';
			}else{
				$params['cm_params'][] = '-lossless';
			}
			
			$params['cm_params'][] = '-m '.$params["compression"];
			$params['cm_params'][] = '-q '.$params["quality"];
			
			$params['cm_params_str'] = implode(' ', $params['cm_params']);
			
			// echo '<pre>'; print_r($params); echo '</pre>';
			
			UTools::setStorage("ci_webp", 'params', $params);
		}
		
		return $params;
	}

	static function serverRequest($request){
		exec($request);
	}
}