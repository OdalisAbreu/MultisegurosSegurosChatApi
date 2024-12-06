
<table cellpadding="5" cellspacing="0" width="60%"> 
	<tr>
    	<td colspan="6" align="center" style="font-size:22px"><b>Ventas por Distribuidores</b></td>
    </tr>
	<tr>
    	<td colspan="6" align="center" style="font-size:15px">
			<b>Desde</b> <?=$fdesdeRep?> <b>Hasta</b> <?=$fhastaRep?>
		</td>
    </tr>
    <tr>
		<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>#</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Nombre</b></td>
    	<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Transacciones</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Monto</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Ganancia</b></td>
		<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Anulado</b></td>
    </tr>
  	
<?
	function Ventas($id){
		global $fdesde, $fhasta, $fdesdeRep;
		
		$wFecha 	= "AND fecha >= '$fdesde 00:00:00' AND fecha <= '$fhasta 23:59:59'";
			
			$qR=mysql_query("SELECT * FROM seguro_transacciones_reversos WHERE id !=''");
			$reversadas .= "0";
			 while($rev=mysql_fetch_array($qR)){ 
				$reversadas .= "[".$rev['id_trans']."]";
				//$reversadas 	.= ",".$rev['id_trans'];
			 }
			 
			$w_user = "(
	user_id='".$id."'";
	
	// PUNTOS DE VENTAS
	$quer1 = mysql_query("
	SELECT id FROM personal WHERE id_dist ='".$id."'");
	while($u=mysql_fetch_array($quer1)){
	
		$w_user .= " OR user_id='".$u['id']."'";
	
		$quer2 = mysql_query("
		SELECT id FROM personal WHERE id_dist ='".$u['id']."'"); 
		while($u2=mysql_fetch_array($quer2)){
		$w_user .= " OR user_id='".$u2['id']."'";	
		}
	
	}
	$w_user .= " )";
	 
		   $query=mysql_query("
		   SELECT * FROM seguro_transacciones 
		   WHERE $w_user $wFecha order by id ASC");
		  $trans = 0;
		  while($row=mysql_fetch_array($query)){
			  $trans ++;
			  if((substr_count($reversadas,"[".$row['id']."]")>0)){
				 $Rtotal +=$row['totalpagar']; 
			  }else{
				$total   +=$row['monto'];
				$Ganancia1   +=$row['ganancia1'];
			  }
			  
		  }
		  
		  return $total."/".$Rtotal."/".$trans."/".$Ganancia1;
	}


$Q1=mysql_query("SELECT * FROM personal WHERE funcion_id='2' order by id ASC");
while($R1=mysql_fetch_array($Q1)){
		$t[] = $R1;
	}
	
	foreach($t as $key=>$row) { 
	
	$vent = explode('/',Ventas($row['id']));
	$MontoTotal = $vent[0];
	$AnulaTotal = $vent[1];
	$TransTotal = $vent[2];
	$Ganancia1  = $vent[3];
	
	if($AnulaTotal>0){
		$AnulTotal = $AnulaTotal;
	}else{
		$AnulTotal = 0;
	}
	
	if($MontoTotal>0){
		$TotalMonto += $MontoTotal;
		$TotalAnul  += $AnulTotal;
		$TotalGan	+= $Ganancia1;
		
	?>
	
	<tr>
        <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?=$row['id']?></td>
        <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"> [ <b><?=$row['id']?></b> ] <?=$row['nombres']?> </td>
        <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?=$TransTotal?></td>
        <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD <?=formatDinero($MontoTotal)?></td>
        <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD <?=formatDinero($Ganancia1)?></td>
        <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD <?=formatDinero($AnulTotal)?></td>
    </tr>
	
	
	<?
  		  }
       }
	?>	
	
 <tr>
    <td colspan="3">&nbsp;</td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD <?=FormatDinero($TotalMonto)?></b></td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD <?=FormatDinero($TotalGan)?></b></td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD <?=FormatDinero($TotalAnul)?></b></td>	
    </tr>
</table>
<br><br>	
	
	
	
	   
<?  if($MontVendido>0){  ?>  
<table>
<tbody>
  <tr>
  	<td colspan="5">&nbsp;</td>
  	<td><strong>$RD <?=formatDinero($MontVendido)?></strong></td> 
  <?  
if($montoAnul>0){   ?> 
  	
	<td><strong>$RD <?=formatDinero($montoAnul)?> </strong></td>
	<? }else{  ?>
    <td><strong>$RD 0.00 </strong></td>
   <?  } ?>
  </tr>
  
<? }  ?>

</tbody>
</table>