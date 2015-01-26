<?php


// header("Content-Type: text/plain; charset=utf-8");

class BreedersList {
	private $svc;
	private $svcZips;
	private static $tamPagina = 12;
	private $countryUrl;
	private $distanceUnit;
	private $conversionFactor;
	private $headerTextKey;
	private $headerTitleKey;
	private $metaDescriptionKey;
	private $metaKeywordsKey;
	

	public function __construct($countryUrl, $headerTitleKey, $headerTextKey,  $metaDescriptionKey, $metaKeywordsKey,  $distanceUnit, $conversionFactor, $svc, $svcZips){
		$this->svc = $svc;
		$this->svcZips = $svcZips;
		$this->countryUrl = $countryUrl;
		$this->headerTitleKey = $headerTitleKey;
		$this->headerTextKey = $headerTextKey;
		$this->metaDescriptionKey = $metaDescriptionKey;
		$this->metaKeywordsKey = $metaKeywordsKey;
		$this->distanceUnit = $distanceUnit;
		$this->conversionFactor = $conversionFactor;
	}
	
	
	public function iniciaAvanzada(){
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		$_REQUEST['start']=0;
		$_REQUEST['country']=$this->countryUrl;
		
		
		$this->listaAvanzada();
	}
	
	public function iniciaRegional(){
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		$_REQUEST['start']=0;
		$_REQUEST['country']=$this->countryUrl;
	
	
		$this->listaRegional();
	}	
	
    
    private function recogeVariable ($varName){
    	$ret=null;
    	if (isset($_REQUEST[$varName])){
    		$ret=$_REQUEST[$varName];
    	}
    	$_REQUEST[$varName]=$ret;
    	
    	return $ret;
    }
	
    public function listaRegional(){
    	$breeders=$this->svc->selTodosWeb(null, null, null, 0, 0, null, null, 0, 10000);
    	$amountOfShelters=$this->svc->selTodosWebCuenta(null, null, null, 0, 0, null, null);
    	$firstAreas = $this->svc->selFirstAreas();
    	
     	//echo "firstArea=" . $firstArea . " secondArea=" . $secondArea . " amount=" . $amountOfShelters . "  specialBreedId=" . $specialBreedId; 
    	
    	//create a map with areas=>array of shelters
    	$mapAreas=array();
    	foreach ($breeders as $breeder){
    		if (!isset($mapAreas[$breeder->getAdminArea1()])){
    			$mapAreas[$breeder->getAdminArea1()]=array();
    		}
    		$mapAreas[$breeder->getAdminArea1()][]=$breeder;
    	}
    	
    	$arrayAreas=array_keys($mapAreas);
    	sort($arrayAreas);
    	
    	
    	$_REQUEST['country'] = $this->countryUrl;
    	$headerTitleKey = $this->headerTitleKey;
    	$headerTextKey = $this->headerTextKey;
    	$metaDescriptionKey = $this->metaDescriptionKey;
    	$metaKeywordsKey = $this->metaKeywordsKey;
    	require 'application/views/breeders/regionallist/header.php';
    	require 'application/views/breeders/regionallist/index.php';
    	require 'application/views/_templates/footer.php';  
    }
    
    public function listaAvanzada(){
    	$zipCode         = $this->recogeVariable("zipCode");
    	$breederName     = $this->recogeVariable("breederName");
    	$distance        = $this->recogeVariable("distance");
    	$specialBreedId  = $this->recogeVariable("specialBreedId");
    	$dogBreedName    = $this->recogeVariable("dogBreedName");
    	$firstArea       = $this->recogeVariable("firstArea");
    	$secondArea      = $this->recogeVariable("secondArea");
    
    
    	//     	echo " el countryUrl es = " . $this->countryUrl;
    
    	$latitude = 0;
    	$longitude = 0;
    	//si el zipCode existe, transformarlo en latitud y longitud
    	if (!empty($zipCode)){
    		$svcZips = new ZipsGenericoSvcImpl();
    		$zipBean = $svcZips->obtienePorCodigo(strtoupper($this->countryUrl), $zipCode);
    		$latitude= $zipBean->getLatitude();
    		$longitude = $zipBean->getLongitude();
    	}
    	 
    	 
    	if (!isset($_REQUEST['start'])){
    		$_REQUEST['start']=0;
    	}
    	$start=$_REQUEST['start'];
    	 
    	 
    	$breeders=$this->svc->selTodosWeb($breederName, $firstArea, $secondArea, $latitude, $longitude, $distance, $specialBreedId, $start, self::$tamPagina);
    	$amountOfShelters=$this->svc->selTodosWebCuenta($breederName, $firstArea, $secondArea, $latitude, $longitude, $distance, $specialBreedId);
    	$firstAreas = $this->svc->selFirstAreas();
    	 
    	//echo "firstArea=" . $firstArea . " secondArea=" . $secondArea . " amount=" . $amountOfShelters . "  specialBreedId=" . $specialBreedId;
    	 
    	 
    	$_REQUEST['hayAnterior']= ($_REQUEST['start']  > 0);
    	$_REQUEST['haySiguiente'] =($amountOfShelters > ($_REQUEST['start'] + self::$tamPagina));
    	 
    
    	$_REQUEST['country'] = $this->countryUrl;
    	$distanceUnit= $this->distanceUnit;
    	$conversionFactor= $this->conversionFactor;
    	$headerTitleKey = $this->headerTitleKey;
    	$headerTextKey = $this->headerTextKey;
    	$metaDescriptionKey = $this->metaDescriptionKey;
    	$metaKeywordsKey = $this->metaKeywordsKey;
    	require 'application/views/breeders/list/headerBreedersIndex.php';
    	require 'application/views/breeders/list/index.php';
    	require 'application/views/_templates/footer.php';
    }    
    
    
    public function siguiente(){
    	$_REQUEST['start'] = $_REQUEST['start'] + self::$tamPagina;
    	$this->listaAvanzada();
    }
    
    public function anterior(){
    	$_REQUEST['start']= $_REQUEST['start']- self::$tamPagina;;
    	$this->listaAvanzada();
    }
    

}
