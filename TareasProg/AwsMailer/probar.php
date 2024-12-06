<?
ini_set('display_errors',1);
	set_time_limit(0);
	require 'PHPMailerAutoload.php';
	include("../../inc/conexion_inc.php");
	include("../../inc/nombres.func.php");
	include("../../inc/fechas.func.php");
	Conectarse(); 
	
	
	
	echo $fech1 = fecha_despues(''.date('d/m/Y').'',-1);
	$fech2 = fecha_despues(''.date('d/m/Y').'',-1);
	
	$ed1 = explode('/',$fech1);
	$ed2 = explode('/',$fech2);
	
	$fechasd = $ed1[2]."-".$ed1[1]."-".$ed1[0];
	
	$fdesde = $ed1[2]."-".$ed1[1]."-".$ed1[0];
 	$fhasta = $ed2[2]."-".$ed2[1]."-".$ed2[0];
	
	$fdesdeRep = $ed1[0]."-".$ed1[1]."-".$ed1[2];
 	$fhastaRep = $ed2[0]."-".$ed2[1]."-".$ed2[2];
  
  // CONSULTANDO VENTAS
	function TotalVentasV2($conf){
		
		$wFecha2 = "fecha >= '".$conf['fech1']." 00:00:00'  AND  fecha <= '".$conf['fech2']." 23:59:59'";
		
		$query=mysql_query("SELECT monto, costo, tipo, id_aseg, id_serv_adc
		FROM seguro_trans_history WHERE $wFecha2 ");
		while($r=mysql_fetch_array($query)){
			
			if($r['tipo'] =='seg'){
				$t[$r['id_aseg']]['monto']	+= $r['monto'];
				$t[$r['id_aseg']]['costo']	+= $r['costo'];
			}
			
			if($r['tipo'] =='serv'){
				$t[$r['id_serv_adc']]['monto']	+= $r['monto'];
				$t[$r['id_serv_adc']]['costo']	+= $r['costo'];
			}
		   
	}
		
		return $t;
}
	
	// BUSCAMOS VENTAS DE TODOS:
	$UserData = TotalVentasV2($c=array('fech1'=>$fdesde,'fech2'=>$fhasta));
	
	/*echo "<pre>";
	print_r($UserData);
	echo "</pre>";*/ 
	//exit();
	// ----------------------------
	
	
	
	
	echo '<table cellpadding="5" cellspacing="0" width="60%"> 
	<tr>
    	<td colspan="3" align="center" style="font-size:22px"><b>Ventas de Seguros</b></td>
    </tr>
	<tr>
    	<td colspan="3" align="center" style="font-size:15px">
		<b>Desde</b>
		'.$fdesdeRep.'
		<b>Hasta</b>
		'.$fhastaRep.'
		</td>
    </tr>
    <tr>
    	<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Nombre</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Monto</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Costo</b></td>
    </tr>'; 
  	$sq =mysql_query("SELECT id FROM  seguros WHERE id !='' ");
	while($p=mysql_fetch_array($sq)){
		
		if($UserData[$p['id']]['monto'] >0){ 
		
		$Totalmonto += $UserData[$p['id']]['monto'];
		$Totalcosto += $UserData[$p['id']]['costo'];
		?>
			
 <tr>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?=NomAseg($p['id'])?></td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?=FormatDinero($UserData[$p['id']]['monto'])?></td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?=FormatDinero($UserData[$p['id']]['costo'])?></td>
</tr>
	<?
        }
		
    }
	
	?>
    
    <tr>
    	<td>&nbsp;</td>
        <td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b><?=FormatDinero($Totalmonto)?></b></td>
        <td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"s><b><?=FormatDinero($Totalcosto)?></b></td>
    </tr>
</table>
<br><br>

<?
  
  
  echo '<table cellpadding="5" cellspacing="0" width="60%"> 
	<tr>
    	<td colspan="3" align="center" style="font-size:22px"><b>Ventas de Servicios Opcionales</b></td>
    </tr>
	<tr>
    	<td colspan="3" align="center" style="font-size:15px">
		<b>Desde</b>
		'.$fdesdeRep.'
		<b>Hasta</b>
		'.$fhastaRep.'
		</td>
    </tr>
    <tr>
    	<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Nombre</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Monto</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Costo</b></td>
    </tr>'; 
  	$sq =mysql_query("SELECT id FROM  servicios WHERE id !='' ");
	while($p=mysql_fetch_array($sq)){
	
		if($UserData[$p['id']]['monto'] >0){
			
			$TotalmontoSer += $UserData[$p['id']]['monto'];
			$TotalcostoSer += $UserData[$p['id']]['costo'];
		  ?>
 <tr>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?=ServAdicHistory($p['id'])?></td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?=FormatDinero($UserData[$p['id']]['monto'])?></td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?=FormatDinero($UserData[$p['id']]['costo'])?></td>
</tr>
          <?
		  
		  
		}
		
    }
?>

<tr>
    	<td>&nbsp;</td>
        <td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b><?=FormatDinero($TotalmontoSer)?></b></td>
        <td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"s><b><?=FormatDinero($TotalcostoSer)?></b></td>
    </tr>
</table>


