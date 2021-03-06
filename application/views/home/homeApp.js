(function(){


  var app = angular.module('homeApp', []);
  

  app.controller('HomePageCtrl', ['$scope',  '$rootScope', '$http', '$sce',   function($scope, $rootScope, $http, $sce){

	  
	  $scope.init = function(tipoMuestra, nombreRazaCodificado){
		  $scope.tipoMuestra = tipoMuestra;
		  $scope.nombreRazaCodificado = nombreRazaCodificado;
		  if (tipoMuestra=='contenido'){
			  $scope.trataContenido();
			  $scope.contenidoVisible = true;
			  $scope.detailVisible = false;
		  }else if (tipoMuestra ='detalleRaza'){
			  $scope.populateDetails(nombreRazaCodificado);
			  $scope.contenidoVisible = false;
			  $scope.detailVisible = true;
		  }
	  }
	  
	  $scope.trataContenido = function(){
		  var url= Global.dirCms + '/svc/conector/frontPage.php/readDatos';
		  $http.get(url).
		    success(function(data, status, headers, config) {
				  $scope.datos=data;
				  $scope.trataHtml();
			    }).
			    error(function(data, status, headers, config) {
			    	 alert("there was a problem getting featured breeds.\nUrl:=" + url);
		   });		  
	  }	 
	  

     
      


      /**
       * el html tiene que ser transformado en "confiable". Las urls, lo mismo.
       * El servicio $sce ya viene dispobible, "sanitize" no hace falta más.
       */
      $scope.trataHtml = function(){
    	  $scope.datos.homePageHeader = $sce.trustAsHtml($scope.datos.homePageHeader);
    	  for (var i=0; i< $scope.datos.videoUrls.length; i++){
    		  var a=   $scope.datos.videoUrls[i];
    		  var b = $sce.trustAsResourceUrl(a);
    		  $scope.datos.videoUrls[i]= b; 
    	  }
    	  for (var i=0; i< $scope.datos.featuredBreeds.length; i++){
    		  var a=   $scope.datos.featuredBreeds[i].link;
    		  var b = $sce.trustAsResourceUrl(a);
    		  $scope.datos.featuredBreeds[i].link= b; 
    	  }    	  
      }
      
      
      //la parte de detalles
	  $scope.details={};
	  $scope.tabsClicked=[false, true, false, false, false, false];
	  $scope.tabNumber=1;
	  
	  
	  $scope.populateDetails = function(nameEncoded){
		  var url=$scope.buildDetailsUrl(nameEncoded);
		  $http.get(url).
		    success(function(data, status, headers, config) {
			  $scope.details=data;
			  $scope.rankingText=$scope.details.friendlyText;
		    }).
		    error(function(data, status, headers, config) {
		    	 alert("there was a problem calling the details' service.\nUrl:=" + url);
		  });		    	
	  }	 
	  
	  $scope.buildDetailsUrl=function(nameEncoded){
      	var dataString = 'nombreCodificado='+ nameEncoded;
    	var url= Global.dirCms + '/svc/conector/dogBreeds.php/obtienePorNombreCodificado?' + dataString;		  
		return url;
	  }
	  

	 
	 
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
      
      
	  
  }]);  
  
  
  
  
  
  /**
   * copia exacta de la que está en breeds
   */
  app.directive('dogBreedDetailsEmbebido', function() {
	  return {
		  restrict : 'E',		  
		  templateUrl : Global.dirAplicacion + "/public/js/dogbreeds/dog-breed-details-embebido.html",
	  }
	});
  
  
  /**
   * copia exacta de lo que hay en breeds.js
   */
  app.controller('DetailCtrl', ['$scope',  '$rootScope', '$http', '$log',  function($scope, $rootScope, $http, $log){
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
  
 
  
})();