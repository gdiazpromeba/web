<?php require_once $GLOBALS['pathWeb']  . '/utils/Resources.php';?>
<!DOCTYPE html>
<html lang="en" ng-app="geoFlatList">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Petzynga</title>
    <meta name="description" content="<?php echo Resources::getText($metaDescriptionKey); ?>" />
    <meta name="keywords" content="<?php echo Resources::getText($metaKeywordsKey); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- css -->
    <link href="<?php echo URL; ?>public/css/estilo.css" rel="stylesheet"/>
    <link href="<?php echo URL; ?>public/css/estiloShelters.css" rel="stylesheet"/>
    <?php if ($GLOBALS['env']=="qa") echo  "<link href='" . URL . "public/css/estiloQa.css' rel='stylesheet'/> "; ?>
    
    <!--  google maps -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <?php require_once 'configJs.php';?> 
    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>    
    
    <!--  jqPagination pagination plugin -->
	<link rel="stylesheet" href="<?php echo URL; ?>public/csspagination/jqpagination.css" />
	<script src="<?php echo URL; ?>public/jspagination/jquery.jqpagination.js"></script>    
	
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.11/angular.min.js"></script>
    <!--  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"> -->
    <script src="https://rawgithub.com/gsklee/ngStorage/master/ngStorage.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['dirWeb']; ?>/application/views/shelters/regionallist/app.js"></script>  	
    
    <script>
      function loadSecondArea(country,firstArea){
        var dataString = 'country='+ country +'&firstArea='+ firstArea;
        //$("#"+loadType+"_loader").show();
        //$("#"+loadType+"_loader").fadeIn(400).
        //html('Please wait... <img src="image/loading.gif" />');
		$.ajax({
		     type: "POST",
		     url: Global.dirCms + "/svc/conector/areas.php/selSegundasAreas",
		     data: dataString,
		     username: 'nina',
		     password: 'nina',
		     cache: false,
		     success: function(result){
		       //$("#"+loadType+"_loader").hide();
		       $("#secondArea").html("");
		       $("#secondArea").append(result);
               //force selection
	    	   var lastSecondArea='<?php echo $_REQUEST['secondArea']; ?>';
	    	   if (lastSecondArea!='' && lastSecondArea !=null && lastSecondArea!=undefined){
	             var secondAreas=document.frmBusqueda.secondArea;
	             //alert('el combo tiene ' + secondArea.options.length + '  elementos, y el �ltimo seleccionado es ' + lastSecondArea);
	    		 for (var i = 0; i < secondArea.options.length; i++) {
	    		   if (secondArea.options[i].value == lastSecondArea) {
	    			   secondArea.selectedIndex = i;
	    			   break;
	    		   }
	    		 }
	    	   }
		     }
		 });
      }


      function selectedFirstArea(firstArea){
          var country=document.frmBusqueda.country.value;
    	  if(firstArea!=""){
    	    loadSecondArea(country, firstArea);
    	  }else{
    	    $("#secondArea").html("<option value=''></option>");
    	  }
       }
       /**
       * after the page loaded, checks if there was a firstArea selected.
       * If there was, reloads the combo of second areas.
       * And, if there was a previous selection in this second combo, forces it again.
       */
       function checkSecondArea(){
    	   var firstArea = document.frmBusqueda.firstArea.value;
    	   if (firstArea!='' && firstArea !=null && firstArea!=undefined){
    		   var country=document.frmBusqueda.country.value;
    		   loadSecondArea(country,firstArea);
    	   }
       }

      

    	
</script>    
    

    <script>
	  function initializeMap(data, country) {
		  var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
		  var mapOptions = {
		    zoom: 4
		  }
		  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		  var bounds = new google.maps.LatLngBounds();
		  var infowindow = new google.maps.InfoWindow({maxWidth: 400}); 

		  var locations = [];
          data.forEach(function(entry){
            var address;
            if (entry.poBox!=null){
                address = entry.poBox;
                address = address.toString();
             }else if (entry.streetAddress!=null){
                address = entry.streetAddress;
             }
             address = address.replace(/(\r\n|\n|\r)/gm,"<br/>");
        			  
        	var item=new Array(entry.name, address, entry.latitude, entry.longitude, entry.urlEncoded);
        	locations.push(item);
          });	         		 
			
	      for (i = 0; i < locations.length; i++) {  
	        marker = new google.maps.Marker({
	          position: new google.maps.LatLng(locations[i][2], locations[i][3]),
	          map: map
	        });
	        bounds.extend(marker.position);
	        google.maps.event.addListener(marker, 'click', (function(marker, i) {
	            return function() {
		          var contentString  ="<div style='font-weight:bold'>" + locations[i][0] + "</div><br/>";
		          contentString +="<div style='font-color:gray'>" + locations[i][1] + "</div>";
		          contentString +="<br/>";
		          contentString +="<a class='detailMapInfoBox' href='" + Global.dirAplicacion + "/shelters/info/" + country + locations[i][4] + "'>Details</a> \n";
	              infowindow.setContent(contentString);
	              infowindow.open(map, marker);
	            }
	          })(marker, i));	        
	      }
	      map.fitBounds(bounds);
	  }
		
    </script>   
    

    <script>

      $(document).ready(function() {
    	  $.when(checkSecondArea())
    	    .then(function(){

        	      //showPage(1);
    	    });
          


		   $("#firstArea").change(function(){
                var country= document.getElementById("country").value;
                var first  = document.getElementById("firstArea").value;
          	    if(first!=""){
          	      loadSecondArea(country, first);
          	    }else{
          	       $("#secondArea").html("<option value=''></option>");
          	    }
		   });            
          
          /**
           * after the page loaded, checks if there was a firstArea selected.
           * If there was, reloads the combo of second areas.
           * And, if there was a previous selection in this second combo, forces it again.
           */
           function checkSecondArea(){
        	   var firstArea = document.getElementById("firstArea").value;
        	   if (firstArea!='' && firstArea !=null && firstArea!=undefined){
        		   var country=document.getElementById("country").value;
        		   loadSecondArea(country,firstArea);
        	   }
           }
      
   		$('.pagination').jqPagination({
			link_string	: '/?page={page_number}',
			max_page	: 40,
			paged		: function(page) {
				$("#hiddenPageNumber").val(page);
				$("#hiddenPageNumber").trigger('input');
			}
		});

//     	   var updateMaxPage = true;

// 		   function showPage(page) {
// 			   var frm = document.frmBusqueda;
// 			   var specialBreedId = frm.specialBreedId.value;
// 			   var shelterName = frm.shelterName.value;
// 			   var zipCode = frm.zipCode.value;
// 			   var specialBreedId = frm.specialBreedId.value;
// 			   var firstArea = frm.firstArea.value;
// 			   var secondArea = frm.secondArea.value;

// 			   var params='?start=' + ((page-1) * 12);
// 			   params+='&limit=12';
// 			   if (specialBreedId!='') params+='&specialBreedId=' + specialBreedId;
// 			   if (shelterName!='') params+='&shelterName=' + shelterName;
// 			   if (zipCode!='') params+='&zipCode=' + zipCode;
// 			   if (specialBreedId!='') params+='&specialBreedId=' + specialBreedId;
// 			   if (firstArea!='') params+='&firstArea=' + firstArea;
// 			   if (secondArea!='') params+='&secondArea=' + secondArea;
			   
// 			   $("#regionalTable tr").remove();
                
//			   var selectionUrl =
// 			   $.getJSON( selectionUrl, function( respuesta ) {
// 				   initialize(respuesta.data);
//                    var rowCount = respuesta.total;
//                    updatePaginatorMaxPage(rowCount);
// 				   $.each( respuesta.data, function( key, val ) {
//                      var html  =  "<tr>";
//                      html += "       <td class='shelterContainer'>" + val.name + "</td>"; 
//                      if ($.trim($("#zipCode").val()).length == 0){
//                        html += "     <td class='locacion'>" + val.adminArea2  +", " +  val.adminArea1 + "</td>";
//                      }else{
//                     	 html += "   <td>";
//                     	 html += "     <table><tr>";
//                     	 html += "       <tr>";
//                     	 html += "         <td class='locacion'>" + val.adminArea2 +", " +  val.adminArea1 +  "</td>";
//						 html += "         <td class='distancia'>"  + val.distanceMiles.toFixed(1) + '<?php echo " " . $distanceUnit; ?>' +  "</td>";
// 						 html += "       </tr>";
// 						 html += "     </table>";
// 						 html += "   </td>";
// 					 }
// 					 var urlEncoded =  Global.dirAplicacion + "/shelters/info/" + document.frmBusqueda.country.value + "/" + val.urlEncoded;
//                      html += "       <td>  <a class='btnMoreDetails w90' href='#' onclick=navega('" +  urlEncoded + "')>Details</a></td>";
// 					 html += "</tr>";
// 					 $('#regionalTable > tbody:last').append(html);
// 				   });
// 			   });

// 		   };

// 		   function updatePaginatorMaxPage(rowCount){
// 			   if (updateMaxPage){
//                  var pageCount;
//                  var division= rowCount /12;
//                  if (division > Math.floor(division)){
//                    pageCount = Math.floor(division) + 1;
//                  }else{
//           	       pageCount = Math.floor(division);  
//                  }
//                  $('.pagination').jqPagination('option', 'max_page', pageCount);
// 			   }
//                updateMaxPage = false;
// 	       };	


           $('#dogBreedName').autocomplete({
               source: Global.dirCms + "/svc/conector/dogBreeds.php/selNombres",
               username: 'nina',
               password: 'nina',
//                'beforeSend': function(xhr) {
//              	  //xhr.setRequestHeader("Authentication", "nina:nina")); 
//                },
               minLength: 2,
               select: function( event, ui ) {
                   $("#specialBreedId").val(ui.item.id);
               }
           });


           function loadSecondArea(country,firstArea){
              var dataString = 'country='+ country +'&firstArea='+ firstArea;
       		  $.ajax({
       		     type: "POST",
       		     url: Global.dirCms + "/svc/conector/areas.php/selSegundasAreasShelters",
       		     data: dataString,
       		     username: 'nina', 
       		     password: 'nina',
       		     async: false,
       		     cache: false,
       		     success: function(result){
       		       //$("#"+loadType+"_loader").hide();
       		       $("#secondArea").html("");
       		       $("#secondArea").append(result);
                      //force selection
       	    	   var lastSecondArea='<?php echo $_REQUEST['secondArea']; ?>';
       	    	   if (lastSecondArea!='' && lastSecondArea !=null && lastSecondArea!=undefined){
       	             var secondAreas=document.frmBusqueda.secondArea;
       	             //alert('el combo tiene ' + secondArea.options.length + '  elementos, y el �ltimo seleccionado es ' + lastSecondArea);
       	    		 for (var i = 0; i < secondArea.options.length; i++) {
       	    		   if (secondArea.options[i].value == lastSecondArea) {
       	    			   secondArea.selectedIndex = i;
       	    			   break;
       	    		   }
       	    		 }
       	    	   }
       		     }
       		   });
            }

           
      });
      
    </script>   
    
<style>
.ui-menu .ui-menu-item {
    font-size: small;
    text-align: left;
}
</style>     
   
</head>
<body>
<?php include $GLOBALS['pathWeb'] . '/application/views/_templates/analyticstracking.php' ?>
<!-- header -->
<div id="container">
    <!-- Info -->
    <div style="height:155px">
        <img src="<?php echo URL; ?>public/img/nuevologo_.jpg" />
    </div>
    <?php include 'application/views/_templates/menu.php'?>

