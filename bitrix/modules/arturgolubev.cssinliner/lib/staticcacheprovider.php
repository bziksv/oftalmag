<?
namespace Arturgolubev\Cssinliner;

class StaticCacheProvider extends \Bitrix\Main\Data\StaticCacheProvider {
	// abstract public function isCacheable();
	// abstract public function setUserPrivateKey();
	// abstract public function getCachePrivateKey();
	// abstract public function onBeforeEndBufferContent();

    public function isCacheable(){
        return true;
    }
	
    public function setUserPrivateKey(){
        \CHTMLPagesCache::setUserPrivateKey(self::makeCacheName(), 0);
    }
	
    public function getCachePrivateKey(){
        return self::makeCacheName();
    }

    public function onBeforeEndBufferContent(){
		
    }

   public static function makeCacheName(){
		$key = 'standart';
		$agent = $_SERVER['HTTP_USER_AGENT'];
		
		// bots
		if(strpos($agent, 'Lighthouse') !== false){
			// AddMessage2Log($agent, 'Lighthouse visit '.$_SERVER['REMOTE_ADDR'], 0);
			return 'lighthouse';
		}
		
		$arIndexBot = ['YandexBot', 'Googlebot', 'YandexMetrika', 'YaDirectFetcher', 'YandexMobileBot', 'SemrushBot', 'AhrefsBot', 'bingbot'];
		foreach($arIndexBot as $bot){
			if(strpos($agent, $bot) !== false){
				// AddMessage2Log($agent, 'Bot visit '.$_SERVER['REMOTE_ADDR'], 0);
				return $key;
			}
		}
		
		// iPhones
		if(strpos($agent, 'iPhone')){
			preg_match_all('/iPhone.*Version\/(\d+)\./',$agent,$matches);
			$version = intval($matches[1][0]);
			
			if($version < 14){
				$key = 'no_webp';
			}
			
			// AddMessage2Log($agent, 'iPhone visit '.$_SERVER['REMOTE_ADDR'].' key ='.$key, 0);
			
			return $key;
		}
		
		// check by accept
		$cookieName = 'agci_httpaccept';
		if($_COOKIE[$cookieName]){
			$accept = $_COOKIE[$cookieName];
		}else{
			if($_SERVER['HTTP_ACCEPT'] != '*/*'){
				setcookie($cookieName, $_SERVER['HTTP_ACCEPT'], time()+3600000, "/", $_SERVER['SERVER_NAME']);
			}
			$accept = $_SERVER['HTTP_ACCEPT'];
		}
		
		if($accept != '*/*' && strpos($accept, 'image/webp') === false){
			$key = 'no_webp';
		}
		
		// AddMessage2Log($agent, 'USER AGENT NOW '.$_SERVER['REMOTE_ADDR'], 0);
		// AddMessage2Log($accept, 'Accept work. Page = '.$key, 0);
		
		return $key;
    }

    public static function setCacheName(){
        \CHTMLPagesCache::setUserPrivateKey(self::makeCacheName(), 0);
        return new self();
    }
}
?>