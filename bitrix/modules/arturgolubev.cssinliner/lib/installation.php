<?
if (!class_exists('agInstaHelperCssinliner')){
	class agInstaHelperCssinliner {
		static function IncludeAdminFile($m, $p){
			global $APPLICATION, $DOCUMENT_ROOT;
			$APPLICATION->IncludeAdminFile($m, $DOCUMENT_ROOT.$p);
		}
		
		static function addGadgetToDesctop($gadget_id){
			if(!defined("NO_INSTALL_MWATCHER") && class_exists('CUserOptions')){
				$desctops = \CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
				if(is_array($desctops) && !empty($desctops[0])){
					$skip = 0;
					foreach($desctops[0]['GADGETS'] as $gid => $gsett){
						if(strstr($gid, $gadget_id)) $skip = 1;
					}
					
					if(!$skip){
						foreach($desctops[0]['GADGETS'] as $gid => $gsett){
							if($gsett['COLUMN'] == 0){
								$desctops[0]['GADGETS'][$gid]['ROW']++;
							}
						}
						
						$gid_new = $gadget_id."@".rand();
						$desctops[0]['GADGETS'][$gid_new] = array('COLUMN' => 0, 'ROW' => 0);
						
						\CUserOptions::SetOption('intranet', '~gadgets_admin_index', $desctops, false, false);
					}
				}
			}
		}
	}
}
?>