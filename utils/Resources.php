<?php
require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dirAplicacion'] . '/svc/impl/TextResourcesSvcImpl.php';
require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dirWeb'] .  "/utils/phpfastcache/phpfastcache.php";
phpFastCache::setup("storage","file");
phpFastCache::setup("path", $GLOBALS['pathWeb']);


  class Resources{
  	
   private static $DIA = 86400;
   


   /**
    * encuentra expresiones entre corchetes dentro de la cadena, 
    * (por ejemplo, "blah blah [dirWeb] blah") y las reemplaza por el valor de esa variable en el array $GLOBALS
    * (por ejemplo "blah blah https://localhost/petzyngaweb blah").
    * @param unknown $siteString
    * @return mixed
    */
   private static function replaceWithGLobals($siteString){
   	 preg_match('/(\[[^]]*?[^]]*?\])/m', $siteString, $matches);
   	 foreach ($matches as $match){
   	 	$key = trim($match, '[]');
   		$siteString = str_replace($match, $GLOBALS[$key], $siteString);
   	 }
   	 return $siteString;
   }
    
  	 
   private static function getTextWithoutParameters($key){
       $res=__c()->get($key); 
  	   if ($res==null){
  	 		$svc = new TextResourcesSvcImpl();
  	 		$bean = $svc->obtienePorKey($key);
  	 		__c()->set($key, $bean->getText(), Resources::$DIA);
  	 		$res = $bean->getText();
  	 	}
  	 	$res = Resources::replaceWithGLobals($res);
  	 	return $res;
    }  	

    public static function purge(){
      __c()->clean();
      echo "cach� cleaned!";
    }
    
    /*
     * same as getKey, but with parameter replacements.  "blah blah {1} blah"
    */
    public static function getText(){
    	$args=func_get_args();
    	$res = Resources::getTextWithoutParameters($args[0]);
    	if (count($args>1)){
    	  for ($i=1; $i< func_num_args(); $i++){
    		$token="{". $i . "}";
    		$res = str_replace($token, func_get_arg($i),  $res);
    	  }
    	}
    	return $res;
    }
    
    /*
     * almacena cualquier cosa en el cach�, especialmente objetos
    */
    public static function set($key, $object){
//       echo "*******************************************************<br/>";
//       echo "before" . var_dump($cache); 
      __c()->set($key, $object, Resources::$DIA);
//       echo "*******************************************************<br/>";
//       echo "after" . var_dump($cache); 
    }
    
    /*
     * obtiene cualquier cosa del cach�, si est�. De lo cotrario devuelve null
    */
    public static function get($key){
//       if ( __c()->isExisting($key)){
        return __c()->get($key);
//       }else{
//       	return null;
//       }
    }
    
    /*
     * Obtiene un array de teasers, y de �l un elemento al azar
    */
    public static function getTeasers($key){
       $arr=__c()->get($key); 
  	   if ($arr==null){
  	 		$svc = new TextResourcesSvcImpl();
  	 		$arr = $svc->selTeasers($key);
  	 		__c()->set($key, $arr, Resources::$DIA);
  	 	}
  	 	$index=rand(0, count($arr)-1);
  	 	$arrIndexed=array_values($arr);
  	 	$bean = $arrIndexed[$index];
  	 	$result =  $bean->getText();
  	 	return $result;
    }     
    
    
    
    
    
	 
  }
?>