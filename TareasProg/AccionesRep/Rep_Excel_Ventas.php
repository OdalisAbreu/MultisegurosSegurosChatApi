<?
	ini_set('display_errors',0);
	set_time_limit(0);
	include("../../inc/conexion_inc.php");
	include("../../inc/fechas.func.php");
	include("../../inc/nombres.func.php");
	Conectarse();
	
	// --------------------------------------------	
	if($_GET['fecha1']){
		$fecha1 = $_GET['fecha1'];
	}else{ 
		$fecha1 = fecha_despues(''.date('d/m/Y').'',-1);
	}
	// --------------------------------------------
	if($_GET['fecha2']){
		$fecha2 = $_GET['fecha2'];
	}else{
		$fecha2 = fecha_despues(''.date('d/m/Y').'',-1);
	}
	// -------------------------------------------
	
    $ed1 = explode('/',$fecha1);
	$ed2 = explode('/',$fecha2);
	
	$fechasd = $ed1[2]."-".$ed1[1]."-".$ed1[0];
	
	$fdesde = $ed1[2]."-".$ed1[1]."-".$ed1[0];
 	$fhasta = $ed2[2]."-".$ed2[1]."-".$ed2[0];
	
	$fdesdeRep = $ed1[0]."-".$ed1[1]."-".$ed1[2];
 	$fhastaRep = $ed2[0]."-".$ed2[1]."-".$ed2[2];
	
	$wFecha2 = "fecha >= '$fdesde 00:00:00' AND fecha <= '$fhasta 23:59:59' ";
	

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
    // CONSULTANDO VENTAS

	// BUSCAMOS VENTAS DE TODOS:
	$UserData = TotalVentasV2($c=array('fech1'=>$fdesde,'fech2'=>$fhasta));


 $html .='   
  
  <table cellpadding="5" cellspacing="3"> 
	<tr>
    	<td colspan="6" align="center" style="font-size:25px; width:700px"><b>Ventas de Seguros</b></td>
    </tr>
	<tr>
    	<td colspan="6" align="center" style="font-size:14px">
		<b>Desde</b>
		'.$fdesdeRep.'
		<b>Hasta</b>
		'.$fhastaRep.'
		</td>
    </tr>
    <tr>
    	<td colspan="2" style="background-color:#D11B1E; font-size:17px; color:#FFF; width:300px"><b>Nombre</b></td>
        <td colspan="2" style="background-color:#D11B1E; font-size:17px; color:#FFF; width:300px"><b>Monto</b></td>
        <td colspan="2" style="background-color:#D11B1E; font-size:17px; color:#FFF; width:300px"><b>Costo</b></td>
    </tr>';
    
		$sq =mysql_query("SELECT id FROM  seguros WHERE id !='' ");
	while($p=mysql_fetch_array($sq)){
		
		if($UserData[$p['id']]['monto'] >0){ 
		
		$Totalmonto += $UserData[$p['id']]['monto'];
		$Totalcosto += $UserData[$p['id']]['costo'];
	
$html .='<tr>
    <td colspan="2" style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:14px; ">'.NomAseg($p['id']).'</td>
    <td colspan="2" style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:14px; ">$RD '.FormatDinero($UserData[$p['id']]['monto']).'</td>
    <td colspan="2" style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:14px; ">$RD '.FormatDinero($UserData[$p['id']]['costo']).'</td>
</tr>';	

  		}
		
    }
		
	
$html .= ' <tr>
    	<td colspan="2">&nbsp;</td>
        <td colspan="2" style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:16px;"><b>$RD '.FormatDinero($Totalmonto).'</b></td>
        <td colspan="2" style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:16px;"><b>$RD '.FormatDinero($Totalcosto).'</b></td>
    </tr>
</table>
<br><br>';	
	
	
	
	$html .='<table cellpadding="5" cellspacing="3" > 
	<tr>
    	<td colspan="6" align="center" style="font-size:25px; width:700px"><b>Ventas de Servicios Opcionales</b></td>
    </tr>
	<tr>
    	<td colspan="6" align="center" style="font-size:14px">
		<b>Desde</b>
		'.$fdesdeRep.'
		<b>Hasta</b>
		'.$fhastaRep.'
		</td>
    </tr>
    <tr>
    	<td colspan="2" style="background-color:#D11B1E; font-size:17px; color:#FFF; width:300px"><b>Nombre</b></td>
        <td colspan="2" style="background-color:#D11B1E; font-size:17px; color:#FFF; width:300px"><b>Monto</b></td>
        <td colspan="2" style="background-color:#D11B1E; font-size:17px; color:#FFF; width:300px"><b>Costo</b></td>
    </tr>'; 
  	$sq =mysql_query("SELECT id FROM  servicios WHERE id !='' ");
	while($p=mysql_fetch_array($sq)){
	
		if($UserData[$p['id']]['monto'] >0){
			
			$TotalmontoSer += $UserData[$p['id']]['monto'];
			$TotalcostoSer += $UserData[$p['id']]['costo'];
	$html .=' <tr>
    <td colspan="2" style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:14px;">'.ServAdicHistory($p['id']).'</td>
    <td colspan="2" style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:14px;">$RD '.FormatDinero($UserData[$p['id']]['monto']).'</td>
    <td colspan="2" style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:14px;">$RD '.FormatDinero($UserData[$p['id']]['costo']).'</td>
</tr>';	
			
		}
	}
	


$html .='<tr>
    	<td colspan="2">&nbsp;</td>
        <td colspan="2"  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:16px;"><b>$RD '.FormatDinero($TotalmontoSer).'</b></td>
        <td colspan="2"  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2; font-size:16px;"><b>$RD '.FormatDinero($TotalcostoSer).'</b></td>
    </tr>
</table>';
	
	/*$carpeta = 'Excel/CLIENTES/'.$dist_id.'';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}*/
	

	$hora = date("H:i:s");
	$sfile	= "EXCEL/VENTAS/Ventas_Admin_$fdesde.xls"; // Ruta del archivo a generar 

	$fp		= fopen($sfile,"w");
	
	fwrite($fp,$html); 
	fclose($fp); 
	
	echo $html;
	
    ?>






