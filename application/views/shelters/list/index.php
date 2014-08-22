<div id="columnaListaShelters">

	<?php include 'formBusqueda.php'?>

    <div>
   
              <table class="sheltersTable">
               <?php
                 foreach ($shelters as $shelter){
                   echo "<tr> \n"; 
                   echo "  <td class='shelterContainer'>" . $shelter->getName() . "</td> \n";
                   if (empty($zipCode)){
                     echo "  <td class='locacion'>" . $shelter->getAdminAreas() . "</td> \n";
                   }else{//muestra tambi�n la distancia
                   	echo "  <td><table> \n";
                   	echo "    <tr><td class='locacion'>" .  $shelter->getAdminAreas() . "</td></tr> \n";
                   	echo "    <tr><td class='distancia'>" .  round($shelter->getDistancia() * $conversionFactor)  . " " . $distanceUnit . "</td></tr> \n";
                   	echo "  </table></td> \n";
                   }
                   
                   echo "  <td>  <a class='btnMoreDetails w90' href='" . URL . "shelters/info/" . $countryUrl . "/" . $shelter->getUrlEncoded(). "'>Details</a></td> \n";
                   echo "</tr> \n"; 
                 }
               ?>
              </table>
    </div>
    <span class="navegacionPaginas">
      <?php 
        if ($_SESSION['hayAnterior']){
          echo "  <a href='" . URL . "shelters/listing/" . $countryUrl . "/previous'> << Previous </a> &nbsp;&nbsp;\n";
        }
        
        if ($_SESSION['haySiguiente']){
          echo "  <a href='" . URL . "shelters/listing/" . $countryUrl . "/next'>  Next >> </a> \n";
        }
        
      ?>
    </span>
</div>