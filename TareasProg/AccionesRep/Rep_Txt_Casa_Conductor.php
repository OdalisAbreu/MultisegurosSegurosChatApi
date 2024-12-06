<?
	ini_set('display_errors',1);
	set_time_limit(0);
	include("../../inc/conexion_inc.php");
	include("../../inc/fechas.func.php");
	include("../../inc/nombres.func.php");
	Conectarse();
	
	//ID DE LA DOMINICANA
	$aseguradora ="AND id_aseg='1' ";
	$_GET['aseguradora'] ="1";
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
	
	$wFecha2 = "fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 23:59:59' ";

	
	
function CiudadRep($id){
	
	$queryp1=mysql_query("SELECT * FROM  ciudad WHERE id='".$id."' LIMIT 1");
	$rowp1=mysql_fetch_array($queryp1);
	
	$queryp2=mysql_query("SELECT * FROM  municipio WHERE id='".$rowp1['id_muni']."' LIMIT 1");
	$rowp2=mysql_fetch_array($queryp2);
	
	$queryp3=mysql_query("SELECT * FROM   provincia WHERE id='".$rowp2['id_prov']."' LIMIT 1");
	$rowp3=mysql_fetch_array($queryp3);
	
	return $rowp3['descrip']."|".$rowp1['descrip'];
	
}


	$fd1	= explode('/',$fecha1);
	$fh1	= explode('/',$fecha2);
	$fDesde 	= $fd1[2].'-'.$fd1[1].'-'.$fd1[0];
	$fDesdeArch 	= $fd1[0].''.$fd1[1].''.$fd1[1];
	$fHasta 	= $fh1[2].'-'.$fh1[1].'-'.$fh1[0];
	$wFecha 	= "AND fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59'";
	
	
	$qR=mysql_query("SELECT * FROM seguro_transacciones_reversos WHERE id !=''");
	 while($rev=mysql_fetch_array($qR)){ 
	    $reversadas .= "[".$rev['id_trans']."]";
	 }
	 
$query=mysql_query("
   SELECT * FROM seguro_transacciones 
   WHERE user_id !='' $wFecha AND serv_adc LIKE '%100%' order by id ASC");

  while($row=mysql_fetch_array($query)){
	 
	 if((substr_count($reversadas,"[".$row['id']."]")>0)){
	}else{
		
	  $total +=$row['monto'];
	  $ganancia += $row['ganancia'];
	  $fh1		= explode(' ',$row['fecha']);
	  $Cliente = explode("|", Cliente($row['id_cliente']));
	  $Client = str_replace("|", "", $Cliente[0]);
	  $i++;
	  
	  $pref = GetPrefijo($row['id_aseg']);
	  $idseg = str_pad($row['id_poliza'], 6, "0", STR_PAD_LEFT);
	  $prefi = $pref."-".$idseg;
	  
	  $TipoM = explode("/", Tipo($row['id_vehiculo']));
	  $TipoM['1'] = substr(formatDinero($TipoM['1']), 0, -3);
	  $TipoM['2'] = substr(formatDinero($TipoM['2']), 0, -3);
	  $TipoM['3'] = substr(formatDinero($TipoM['3']), 0, -3);
	  $TipoM['4'] = substr(formatDinero($TipoM['4']), 0, -3);
	  $monto 	  = substr(formatDinero($row['monto']), 0, -3);

	  $marca = VehiculoExport($row['id_vehiculo']);
      $MarcaMod = explode("/", $marca);
	  
	  $ciud	= explode('|',CiudadRep($Cliente[4]));
	  $ciudad = $ciud[0];
	  $provincia = $ciud[1];
	  $telefono = str_replace("-", "", $Cliente[7]);
	  $uso = "PRIVADO";
	  $estado = "1"; // 1 es emision, 2 es cancelacion, 3 es actualizacion
	  
	  //FECHA
	  $FE1 = explode(" ", $row['fecha']);
	  $FE2 = explode("-", $FE1[0]);
	  $fcreacion = $FE2[0]."-".$FE2[1]."-".$FE2[2];
	  
	  //FECHA INICIO
	  $FEI1 = explode(" ", $row['fecha_inicio']);
	  $FEI2 = explode("-", $FE1[0]);
	  $fecha_inicio = $FEI2[0]."-".$FEI2[1]."-".$FEI2[2];
	  
	  //FECHA FIN
	  $FEN1 = explode(" ", $row['fecha_fin']);
	  $FEN2 = explode("-", $FE1[0]);
	  $fecha_fin = $FEN2[0]."-".$FEN2[1]."-".$FEN2[2];
	  $nombre_aseguradora = NombreSeguroS($_GET['aseguradora']);
	  
$html .="80|".CedulaSinGuion($row['id_cliente'])."|1|".$Client."|".$Cliente[1]."|1|".$prefi."|".$ciudad."|".$provincia."|".$Cliente[6]."|".$telefono."|".$telefono."|".$uso."|".$TipoM['0']."|".rtrim(VehiculoMarca(trim($MarcaMod['0'])))."|".rtrim(VehiculoModelos(trim($MarcaMod['1'])))."|".trim($MarcaMod['2'])."||".$MarcaMod['4']."|".$MarcaMod['3']."|".$estado."|".date("Y-m-d")."|".$fcreacion."|".$fecha_inicio."|".$fecha_fin.'
';

 } }

/*$carpeta = 'EXCEL/DOM/'.$fDesde.'';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}*/
	
	//$sfile	= "TXT/CASA_CONDUCTOR/MSG_CDC_$fDesdeArch.txt"; // Ruta del archivo a generar 
	$sfile	= "TXT/CASA_CONDUCTOR/MSG_CDC_".date("Y-m-d").".txt"; // Ruta del archivo a generar 
	

	$fp		= fopen($sfile,"w");
	
	fwrite($fp,$html); 
	fclose($fp); 
	
	echo $html;




