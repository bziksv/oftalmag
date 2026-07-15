<?
namespace Arturgolubev\Cssinliner;

use \Arturgolubev\Cssinliner\Webp;
use \Arturgolubev\Cssinliner\Unitools as UTools;

class OptimizeProcess {
	const ITERATION_MAX = 200;
	
	static function getImageRowCount(){
		$connection = \Bitrix\Main\Application::getConnection();
		$fullCount = 0;
		$sql = "SELECT `CONTENT_TYPE`, COUNT(*) as `CNT` FROM b_file WHERE `CONTENT_TYPE` IN ('image/png', 'image/jpeg') GROUP BY `CONTENT_TYPE`;";
		$recordset = $connection->query($sql);
		while ($record = $recordset->fetch()){
			$fullCount += $record['CNT'];
		}
	
		return $fullCount;
	}
	
	static function oneRowOptimize($result){
		$connection = \Bitrix\Main\Application::getConnection();
		
		$result['next'] = $result['now'] = [];
		
		$sql = "SELECT * FROM b_file
			WHERE `CONTENT_TYPE` IN ('image/png', 'image/jpeg') AND `ID` > ".$result['start']."
			ORDER BY ID ASC
			LIMIT 1;";
			
		$recordset = $connection->query($sql);
		while ($record = $recordset->fetch()){
			$result['cnt']++;
			$result['last'] = $result['start'] = $record['ID'];
			
			$path = \CFile::GetPath($record['ID']);
			
			// echo '<pre>'; print_r($path); echo '</pre>';
			// echo '<pre>'; print_r($record); echo '</pre>';
			// die();
			
			if(Webp::check($path)){
				$result['skip']++;
			}else{
				if(Webp::make($path)){
					$result['optimize']++;
				}else{
					$result['optimize_faled'] = 1;
					$result['skip']++;
				}
			}
			
			$result['now']['path'] = $path;
		}
		
		return $result;
	}
	
	static function getNextNeedOptimize($result){
		$connection = \Bitrix\Main\Application::getConnection();
		
		$res = [];
		
		$sql = "SELECT * FROM b_file
			WHERE `CONTENT_TYPE` IN ('image/png', 'image/jpeg') AND `ID` > ".$result['start']."
			ORDER BY ID ASC
			LIMIT ".self::ITERATION_MAX.";";
			
		$recordset = $connection->query($sql);
		while ($record = $recordset->fetch()){
			// echo '<pre>'; print_r($record); echo '</pre>';
			
			$path = \CFile::GetPath($record['ID']);
			if(!Webp::check($path)){
				$res['path'] = $path;
				$res['size'] = ceil(filesize($_SERVER['DOCUMENT_ROOT'].$path) / 1024);
				$res['prop'] = getimagesize($_SERVER['DOCUMENT_ROOT'].$path);
				break;
			}
		}
		
		return $res;
	}
	
}