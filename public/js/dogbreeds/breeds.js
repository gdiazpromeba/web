(function(){


  var app = angular.module('breeds', ['ngSanitize']);
  
  
  
  app.controller('PicTableController', ['$scope', '$rootScope', '$log', '$http', function($scope, $rootScope, $log, $http){
	  
	  $scope.init = function(){
		$log.info("en init"); 
		$scope.page=1;
		$scope.connectorUrl = Global.dirCms + '/svc/conector/dogBreeds.php/seleccionaNg';
		$scope.formParams={};
	  };
	  
	  $scope.init();
	  
	  $scope.$watch(function() {
		  return $scope.page;
		}, function(newValue, oldValue) {
			$scope.page=newValue;
			$scope.callService(false);
	  });	 
	  
	  /**
	   * el parámetro "reset" indica si se debe correr el cursor a la primera página 
	   * Eso debe ocurrir sólo cuando el servicio se llama a raíz de haber pulsado "Buscar".
	   * Los "callService" producidos por la navegación del cursor no deben actualizar la current_page
	   */
	  $scope.callService = function(reset){
			var url=$scope.buildUrl();
		    $http.get(url).
		    success(function(data, status, headers, config) {
			  $scope.tableData=data.data;
			  if ($(".pagination").length){//esto es al principio, por si no encuentra todavía el control
			    var pageCount=$scope.calculatePages(data.size);
			    $(".pagination").jqPagination('option', 'max_page', pageCount);//el recálculo ocurre siempre
			    if (reset){
			    	$(".pagination").jqPagination('option', 'current_page', 1);
			    }
			  }else{
				  alert('no pagination found');
			  }
		    }).
		    error(function(data, status, headers, config) {
			  alert('there was a problem');
		    });		    	
	   };
	    
	   $scope.calculatePages = function(itemCount){
            var pageCount;
            var division= itemCount /15;
            if (division > Math.floor(division)){
              pageCount = Math.floor(division) + 1;
            }else{
         	 pageCount = Math.floor(division);  
            }	
            return pageCount;
	   };
	  
	    $scope.buildUrl=function(){
	    	var url=$scope.connectorUrl;
	    	url +='?start=' + (($scope.page-1) * 15);
	    	if (typeof($scope.formParams.letraInicial)!='undefined'){
	    		url +='&inicial=' + $scope.formParams.letraInicial;	
	    	}
	    	if (typeof($scope.formParams.nombreOParte)!='undefined'){
	    		url +='&nombreOParte=' + $scope.formParams.nombreOParte;	
	    	}
	    	if (typeof($scope.formParams.selDogSize)!='undefined'){
	    		url +='&size=' + $scope.formParams.selDogSize;	
	    	}	    	
	    	if (typeof($scope.formParams.selDogFeeding)!='undefined'){
	    		url +='&alimentacion=' + $scope.formParams.selDogFeeding;	
	    	}	
	    	if (typeof($scope.formParams.selUpkeep)!='undefined'){
	    		url +='&upkeep=' + $scope.formParams.selUpkeep;	
	    	}	    	
	    	$log.info(url);
	    	return url;
	    };
	  
	    $rootScope.$on('buttonClicked', function($event, $formParams){
	    	$scope.formParams=$formParams;  //paso una variable "formParams" también a este controller
	    	$scope.callService(true);
	    	
	    });
	    
	    $scope.itemClicked=function(nameEncoded){
		  $rootScope.$broadcast('itemClicked', nameEncoded);
	    }
	    
	    
    
    //$log.info("la página es " + $scope.page);
  
    
  }]);
  
  app.controller('ParameterController', ['$scope',  '$rootScope', function($scope, $rootScope){
	  $scope.formParams={};
	  
	  $scope.buttonClick=function(){
		  $rootScope.$broadcast('buttonClicked', $scope.formParams);
	  }
	  
	  $scope.reset=function(){
		  $scope.formParams={};
	  }	  
	  
  }]); 
  

  
  

  app.controller('DetailCtrl', ['$scope',  '$rootScope', '$http',  function($scope, $rootScope, $http){
	  $scope.details={};
	  $scope.visible=false;
	  $scope.tabsClicked=[false, true, false, false, false, false];
	  $scope.tabNumber=1;
	  
	  $scope.isVisible=function(){
		  $scope.visible;
	  }
	  
	  $scope.populateDetails = function(nameEncoded){
		  var url=$scope.buildUrl(nameEncoded);
		  $http.get(url).
		    success(function(data, status, headers, config) {
			  $scope.details=data;
			  $scope.rankingText=$scope.details.friendlyText;
		    }).
		    error(function(data, status, headers, config) {
		    	 alert("there was a problem calling the details' service.\nUrl:=" + url);
		  });		    	
	  }	 
	  
	  $scope.buildUrl=function(nameEncoded){
      	var dataString = 'nombreCodificado='+ nameEncoded;
    	var url= Global.dirCms + '/svc/conector/dogBreeds.php/obtienePorNombreCodificado?' + dataString;		  
		return url;
	  }
	  
	 $rootScope.$on('itemClicked', function($event, nameEncoded){
		 $scope.populateDetails(nameEncoded);
		 $scope.visible=true;
	 });
	 
	 $scope.setTab = function(value){
		 for (var i=1; i<=5; i++){
			 	  $scope.tabsClicked[i]=false;	
	     };
		 $scope.tabsClicked[value]=true;
		 $scope.tabNumber=value;
		 switch(value){
		 case 1:
			 $scope.rankingText=$scope.details.friendlyText;
			 break;
		 case 2:
			 $scope.rankingText=$scope.details.activeText;
			 break;
		 case 3:
			 $scope.rankingText=$scope.details.healthyText;
			 break;
		 case 4:
			 $scope.rankingText=$scope.details.guardianText;
			 break;
		 case 5:
			 $scope.rankingText=$scope.details.groomingText;
			 break;
		 }
	 }
	 
	 $scope.closeButtonClicked = function(){
		 $rootScope.$broadcast('detailsClosedEvent');
	 }
	  
	  
  }]);  
  app.controller('PicTableControllerAlpha', ['$scope', '$rootScope', '$log', '$http', function($scope, $rootScope, $log, $http){
	  
	     console.log('hola controller');
	     
		  

		  $scope.init = function(){
			  console.log('en init');
				var url= Global.dirCms + '/svc/conector/dogBreeds.php/seleccionaNgAlpha';
			    $http.get(url).
			    success(function(data, status, headers, config) {
				  $scope.bloques=data.bloques;
			    }).
			    error(function(data, status, headers, config) {
				  alert('there was a problem');
			    });		    	
		   };
		    

		    
		    $scope.itemClicked=function(nameEncoded){
			  $rootScope.$broadcast('itemClicked', nameEncoded);
		    }
		    
		    
	    
	    //$log.info("la página es " + $scope.page);
	  
	    
	  }]);
	  

	   
  

	  app.directive('dogBreedDetails', function() {
		  return {
			  restrict : 'E',		  
			  templateUrl : Global.dirAplicacion + "/public/js/dogbreeds/dog-breed-details.html",
		  }
		});
	  
	  
	  app.controller('DogGroupCtrl', ['$scope',  '$rootScope', '$http',  function($scope, $rootScope, $http){
		  $scope.data={};
		  
		  $scope.tipoMuestra="";
		  
		  $scope.init = function(dogGroup, dogBreed){
			  var url;
			  if (dogBreed!=null){
				  $scope.tipoMuestra="breed";
				  //uses the detail controller
				  return true;
			  }else if (dogGroup==null){
				  $scope.tipoMuestra="groups";
				  url= Global.dirCms + '/svc/conector/dogBreeds.php/selDogBreedGroups';
			  }else{
				  $scope.tipoMuestra="group";
				  url= Global.dirCms + '/svc/conector/dogBreeds.php/getDogBreedGroup?group=' + dogGroup;
			  }
			  $http.get(url).
			    success(function(data, status, headers, config) {
				  $scope.data=data;
			    }).
			    error(function(data, status, headers, config) {
				  alert("there was a problem calling the details' service");
			  });		    	
		  }
		  
		  $rootScope.$on('detailsClosedEvent', function($event){
		    window.history.back();
		  });
	  }]);
		

  
  
  
})();