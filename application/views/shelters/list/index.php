 <?php require_once 'utils/Resources.php';?>
<div id="columnaListaShelters">
    <!-- peque�o form y javascript para invocar la pantalla de detalle con un par�metro "start" como post -->
    <form name="frmNavegacion" action="" method="post">
      <input type="hidden" name="start" value="<?php echo $_REQUEST['start']; ?>" />
      <input type="hidden" name="firstArea" value="<?php echo $_REQUEST['firstArea']; ?>" />      
      <input type="hidden" name="secondArea" value="<?php echo $_REQUEST['secondArea']; ?>" />
      <input type="hidden" name="zipCode" value="<?php echo $_REQUEST['zipCode']; ?>" />
      <input type="hidden" name="shelterName" value="<?php echo $_REQUEST['shelterName']; ?>" />
      <input type="hidden" name="country" value="<?php echo $_REQUEST['country']; ?>" />
      
    </form>
    <script type="text/javascript">
      function navega(url){
        document.frmNavegacion.action=url;
        document.frmNavegacion.submit();
      }
    </script>

    <div class="descriptiveParagraph2">
      <b><?php echo Resources::getText($headerTitleKey); ?></b>
      <br/>
      <?php echo Resources::getText($headerTextKey); ?>
    </div>
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
                   
                   echo "  <td>  <a class='btnMoreDetails w90' href='#' onclick=navega('" . URL . "shelters/info/" . $_REQUEST['country'] . "/" . $shelter->getUrlEncoded(). "')>Details</a></td> \n";
                   echo "</tr> \n"; 
                 }
               ?>
              </table>
    </div>
    <span class="navegacionPaginas">
      <?php 
        if ($_REQUEST['hayAnterior']){
          echo "  <a href='#' onclick=navega('" . URL . "shelters/listing/" . $_REQUEST['country'] . "/previous')> << Previous </a> &nbsp;&nbsp;\n";
        }
        
        if ($_REQUEST['haySiguiente']){
          echo "  <a href='#' onclick=navega('" . URL . "shelters/listing/" . $_REQUEST['country'] . "/next')>  Next >> </a> \n";
        }
        
      ?>
    </span>
</div>
