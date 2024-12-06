<?
	ini_set('display_errors',1);
	set_time_limit(0);
	include("../inc/conexion_inc.php");
	include("../inc/fechas.func.php");
	include("../inc/nombres.func.php");
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
	
   	$fd1		= explode('/',$fecha1);
	$fh1		= explode('/',$fecha2);
	$fDesde 	= $fd1[2].'-'.$fd1[1].'-'.$fd1[0];
	$fHasta 	= $fh1[2].'-'.$fh1[1].'-'.$fh1[0];
	
	$wFecha2 = "fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59' ";



function Vehiculo($id){
	$query=mysql_query("
	SELECT * FROM  seguro_vehiculo
	WHERE id='".$id."' LIMIT 1");
	$rowsc=mysql_fetch_array($query);
	return $rowsc['veh_tipo']."|".$rowsc['veh_marca']."|".$rowsc['veh_modelo']."|".$rowsc['veh_ano']."|".$rowsc['veh_matricula']."|".$rowsc['veh_chassis'];
	
}


function Marcas($id){
	$querym=mysql_query("SELECT * FROM  seguro_marcas WHERE ID='".$id."' LIMIT 1");
	$rowm=mysql_fetch_array($querym);
	return $rowm['DESCRIPCION'];
}

function Modelos($id){
	$querymo=mysql_query("SELECT * FROM  seguro_modelos WHERE id='".$id."' LIMIT 1");
	$rowmo=mysql_fetch_array($querymo);
	return $rowmo['descripcion'];
}

function Clientes($id){
	$query=mysql_query("
	SELECT * FROM  seguro_clientes
	WHERE id='".$id."' LIMIT 1");
	$row=mysql_fetch_array($query);
	return $row['asegurado_nombres']."|".$row['asegurado_apellidos']."|".$row['asegurado_cedula']."|".$row['asegurado_direccion']."|".$row['ciudad']."|".$row['asegurado_telefono1'];
	
}


function Ciudad($id){
	$queryp1=mysql_query("SELECT * FROM  ciudad WHERE id='".$id."' LIMIT 1");
	$rowp1=mysql_fetch_array($queryp1);
	
	$queryp2=mysql_query("SELECT * FROM  municipio WHERE id='".$rowp1['id_muni']."' LIMIT 1");
	$rowp2=mysql_fetch_array($queryp2);
	
	$queryp3=mysql_query("SELECT * FROM   provincia WHERE id='".$rowp2['id_prov']."' LIMIT 1");
	$rowp3=mysql_fetch_array($queryp3);
	
	return $rowp3['descrip'];
	
}


function Fecha($id){
	$clear1 = explode(' ',$id);  
	$fecha_vigente1 = explode('-',$clear1[0]); 
	   
	   if($fecha_vigente1[1] =='01'){ $mes =' Enero'; }
	   if($fecha_vigente1[1] =='02'){ $mes =' Febrero'; }
	   if($fecha_vigente1[1] =='03'){ $mes =' Marzo'; }
	   if($fecha_vigente1[1] =='04'){ $mes =' Abril'; }
	   if($fecha_vigente1[1] =='05'){ $mes =' Mayo'; }
	   if($fecha_vigente1[1] =='06'){ $mes =' Junio'; }
	   if($fecha_vigente1[1] =='07'){ $mes =' Julio'; }
	   if($fecha_vigente1[1] =='08'){ $mes =' Agosto'; }
	   if($fecha_vigente1[1] =='09'){ $mes =' Septiembre'; }
	   if($fecha_vigente1[1] =='10'){ $mes =' Octubre'; }
	   if($fecha_vigente1[1] =='11'){ $mes =' Noviembre'; }
	   if($fecha_vigente1[1] =='12'){ $mes =' Diciembre'; }
	   return $Vard = $fecha_vigente1[2].' de '.$mes.' del '.$fecha_vigente1[0];
}

function Telefono($id){
  $telefono = str_replace("-","",$id);
  $in  = $telefono;
  return substr($in,0,3)."-".substr($in,3,3)."-".substr($in,-4);
}



function Tipos($id){
	$queryt=mysql_query("SELECT * FROM  seguro_tarifas WHERE veh_tipo='".$id."' LIMIT 1");
	$rowt=mysql_fetch_array($queryt);
	return $rowt['nombre']."|".$rowt['dpa']."|".$rowt['rc']."|".$rowt['rc2']."|".$rowt['fj']."|".$rowt['id_serv_rep'];
}


function Ventas($id){
	
	
	global $fDesde, $fHasta, $fecha1, $fecha2;	
	$wFecha = "fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59' ";
	 
	// --------------------- Index ID ------------------------ //
	$qIndex = mysql_query("SELECT id_inicio FROM indexa WHERE fecha ='".$fDesde."' ");
 	$Index	= mysql_fetch_array($qIndex);
	  if($Index['id_inicio']){
	     $wIndexId = "(id > ".$Index['id_inicio'].") AND ";
	   }
	// -------------------------------------------------------
	
	//PARA LOS REVERSOS
	$qR=mysql_query("SELECT id_trans FROM seguro_transacciones_reversos WHERE $wFecha");
		  while($rev=mysql_fetch_array($qR)){ 
		  $reversadas .= "[".$rev['id_trans']."]";
    }
	
$quer1 = mysql_query("SELECT * FROM seguro_trans_history WHERE $wFecha AND id_serv_adc = '".$id."'");
while($u=mysql_fetch_array($quer1)){
	$idtrans .= $u['id_trans'].",";
}






if($idtrans){

 $html .='   
  <table cellpadding="4" cellspacing="0">
  	
	<tr>
		<td colspan="21"> 
		
		
		
		<table width="100%" cellpadding="9" cellspacing="0">
	<tr>
    	<td colspan="6">
		
		<b style="font-size: 60px; color: #d9261c;">Multi</b><b style="font-size: 60px; color: #828282;">Seguros 
			</b>	
			</td>
    	
   
	  <td align="center" colspan="7">
		  <font style="font-size: 22px; color: #828282; font-weight: bold;">
		  	<b>REPORTE DIARIO DE VENTAS</b>
		  </font>
		  
		  <br>
		  <font style="font-size: 18px; color: #828282; font-weight: bold;">
		  	'.ServAdicHistory($id).'
		  <font><br>
		  <font style="font-size: 14px; color: #828282; font-weight: bold;">
		  	<b>Desde:</b> '.$fecha1.' <b>Hasta:</b> '.$fecha2.'
			</font>
	  </td>
	  <td align="center" colspan="4"> 
	  	<b>Fecha del Reporte</b><br>
		'.FechaHora(date("Y-m-d H:i:s")).'
	  </td>
  </tr>
	
</table>


		</td>
	</tr>
	
	
   <tr style="background-color:#B1070A; color:#FFFFFF; font-size:14px;">
   		<td></td>
        <td>No. Poliza</td>
        <td>Nombres</td>
        <td>Apellidos</td>
        <td>C&eacute;dula</td>
        <td>Ciudad</td>
        <td>Tel&eacute;fono</td>
        <td>Tipo</td>
        <td>Marca</td>
        <td>Modelo</td>
        <td>A&ntilde;o</td>
        <td>Chassis</td>
        <td>Placa</td>
        <td>Fecha Emisi&oacute;n con hora</td>
		<td>Agencia</td>
        <td>Inicio Vigencia</td>
        <td>Fin Vigencia</td>
        <td>Prima</td>
   </tr> ';
    
	
	$idtrans = substr($idtrans, 0, -1);
	$cons = "id IN (".$idtrans.")";
	
$quer1 = mysql_query("SELECT * FROM seguro_transacciones WHERE $wFecha AND $cons order by id DESC");
while($u=mysql_fetch_array($quer1)){
	
	$t++;
	
	
	//echo "ID: ".$u['id_cliente'];	
	$veh =  explode("|", Vehiculo($u['id_vehiculo']));
	$tipo =  explode("|", Tipos($veh[0]));
	$marca = Marcas($veh[1]);
	$modelo = Modelos($veh[2]);
	$cliente =  explode("|", Clientes($u['id_cliente']));
	
	
	$ModificarPref =  ModificarPref($id,$u['id_aseg']);
	$MPref = explode("|", $ModificarPref);
	
	$result = $MPref[0];
	$valor  = $MPref[1];
	
	
	if($result=='si'){
		$pref = $MPref[1];
	}else{
		$pref = GetPrefijo($u['id_aseg']);
	}
	
	$idseg = str_pad($u['id_poliza'], 6, "0", STR_PAD_LEFT);
	$prefi = $pref."-".$idseg;
	
	$CostoServ 		+= RepCostoServiciodosRemesa($u['id'],$id);	
	$precio	 	 	 =  $CostoServ;
	
	
	$SUMtotal += $precio;
$html .='
	<tr>
   		<td>'.$t.'</td>
        <td>'.$prefi.'</td>
        <td>'.$cliente[0].'</td>
        <td>'.$cliente[1].'</td>
        <td>'.CedulaExport($cliente[2]).'</td>
        <td>'.Ciudad($cliente[4]).'</td>
        <td>'.Telefono($cliente[5]).'</td>
        <td>'.$tipo[0].'</td>
        <td>'.$marca.'</td>
        <td>'.$modelo.'</td>
        <td>'.$veh[3].'</td>
        <td>'.$veh[5].'</td>
        <td>'.$veh[4].'</td>
        <td>'.FechaHora($u['fecha']).'</td>
		<td>'.$u['id'].'</td>
        <td>'.Fecha($u['fecha_inicio']).'</td>
        <td>'.Fecha($u['fecha_fin']).'</td>
        <td>$'.formatDinero($precio).'</td>
   </tr>';
		 
		  

    }
	

$html .='
	<tr>
		<td colspan="17" align="right" style="font-size: 15px; color: #828282; font-weight: bold;">Total:</td>
        <td style="font-size: 15px; color: #828282; font-weight: bold;">$'.formatDinero($SUMtotal).'</td>
   </tr>
</table>';
	
	$carpeta = 'Excel/SERVICIO_OPCIONAL/'.$id.'';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}
	
	$sfile	= "Excel/SERVICIO_OPCIONAL/".$id."/$fDesde.xls"; // Ruta del archivo a generar 
	$fp		= fopen($sfile,"w");
	
	fwrite($fp,$html); 
	fclose($fp); 
	
	echo $html;
    ?>


<? }  } ?>



<?


	
$quer1P = mysql_query("SELECT * FROM servicios WHERE sumar ='n' AND activo ='si' ");
	while($u=mysql_fetch_array($quer1P)){
		
	if($u['id']){
		//Ventas($u['id']);
		echo "<br>Ventas: ".$u['nombre']."<b> [ID: ".$u['id']."] </b> ".Ventas($u['id'])."<br>";
	  }
	}



?>

