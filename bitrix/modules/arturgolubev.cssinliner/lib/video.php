<?
namespace Arturgolubev\Cssinliner;

class Video {
	static function getRutubePreview($ytSrc){
		$obCache = new \CPHPCache();
		
		if($obCache->InitCache(\CArturgolubevCssinliner::CACHE_TIME, md5($ytSrc), '/arturgolubev.cssinliner/'.\CArturgolubevCssinliner::CACHE_VERSION.'_'.SITE_ID.'/rutube')){
			$vars = $obCache->GetVars();
			$videoInfo = $vars['videoInfo'];
		}elseif($obCache->StartDataCache()){
			$videoInfo = file_get_contents('https://rutube.ru/api/video/'.$ytSrc);
			if($videoInfo){
				$videoInfo = \Bitrix\Main\Web\Json::Decode($videoInfo);
			}
			
			if(is_array($videoInfo) && $videoInfo['thumbnail_url']){
				$obCache->EndDataCache(array('videoInfo' => $videoInfo));
			}else{
				$obCache->AbortDataCache();
			}
		}

		return $videoInfo;
	}

	static function getFrameStyle($ifr){
		$width = self::getFrameWidth($ifr);
		$height = self::getFrameHeight($ifr);
		
		$style = '';
		if($width || $height){
			$style .= 'style="'.($width ? 'width:'.$width.';' : '').' '.($height ? 'height:'.$height.';' : '').' "';

			if(strstr($height, 'px')){
				$style .= ' data-fh="'.IntVal($height).'"';
			}
			if(strstr($width, 'px')){
				$style .= ' data-fw="'.IntVal($width).'"';
			}
		}
		
		return $style;
	}
	
	static function getFrameWidth($ifr){
		preg_match_all('/width=[\",\'](.*)[\",\']/iU', $ifr, $tmp);
		$width = $tmp[1][0];
		
		if($width && !strstr($width, '%') && !strstr($width, 'px')) $width .= 'px';
		
		return $width;
	}
	
	static function getFrameHeight($ifr){
		preg_match_all('/height=[\",\'](.*)[\",\']/iU', $ifr, $tmp);
		$height = $tmp[1][0];
		
		if($height && !strstr($height, '%') && !strstr($height, 'px')) $height .= 'px';
		
		return $height;
	}
}