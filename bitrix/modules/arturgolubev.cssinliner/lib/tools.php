<?
namespace Arturgolubev\Cssinliner;

class Tools {
	static function onComposite(){
		return file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/html_pages/.enabled");
	}
	static function fileNoEmpty($path){
		$r = 0;
		
		$file = new \Bitrix\Main\IO\File($_SERVER["DOCUMENT_ROOT"].$path);	
		if($file->isExists()){
			if($file->getSize() > 0){
				$r = 1;
			}
		}
		
		return $r;
	}
	static function isBot(){
		// return 1; //todo
		return (strpos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') !== false);
	}

	static function isAuthorized(){
		global $USER;
		if(!is_object($USER)) return false;

		return $USER->IsAuthorized();
	}
}