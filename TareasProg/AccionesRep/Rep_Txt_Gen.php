<?
	ini_set('display_errors',1);
	set_time_limit(0);
	include("../../inc/conexion_inc.php");
	include("../../inc/fechas.func.php");
	include("../../inc/nombres.func.php");
	Conectarse();
	
	//ID DE LA DOMINICANA
	$aseguradora ="AND id_aseg='3' ";
	$_GET['aseguradora'] ="3";
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



	if($_GET['aseguradora'] !='1aseg'){
		$aseg = "AND id_aseg='".$_GET['aseguradora']."' ";
		$nombre = NombreSeguroS($_GET['aseguradora']);
		$clase = "1";
		$columna = "22";
		$colspan ="8";
		$colspan2 ="14";
		$calt = "17";
	}
	
	
function CiudadRep($id){
	$query=mysql_query("SELECT * FROM  seguro_clientes WHERE id='".$id."' LIMIT 1");
	$row=mysql_fetch_array($query);
	
	$queryp1=mysql_query("SELECT * FROM  ciudad WHERE id='".$row['ciudad']."' LIMIT 1");
	$rowp1=mysql_fetch_array($queryp1);
	
	$queryp2=mysql_query("SELECT * FROM  municipio WHERE id='".$rowp1['id_muni']."' LIMIT 1");
	$rowp2=mysql_fetch_array($queryp2);
	
	$queryp3=mysql_query("SELECT * FROM   provincia WHERE id='".$rowp2['id_prov']."' LIMIT 1");
	$rowp3=mysql_fetch_array($queryp3);
	
	return $rowp3['descrip'];
	
}


	$fd1	= explode('/',$fecha1);
	$fh1	= explode('/',$fecha2);
	$fDesde 	= $fd1[2].'-'.$fd1[1].'-'.$fd1[0];
	$fHasta 	= $fh1[2].'-'.$fh1[1].'-'.$fh1[0];
	$wFecha 	= "AND fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59'";
	
	
	/* $html .='poliza; VehiculoNo; Codigo de plan; tipos de movimientos; fechaInicio; FechaTermino; HoraInicio; Tipo de Vehiculo; Uso Vehiculo; Uso Especifico; Codigo Marca;  Codigo Modelo;  Modelo Descripcion;  Año del vehiculo; Chassis;  Placa; Cilindros; Pasajeros; Prima Neta;  Valor del Vehiculo;  Nombre Solicitante;  Documento Identidad; Fecha ';
  $html .="\n";*/
	
	$qR=mysql_query("SELECT * FROM seguro_transacciones_reversos WHERE id !=''");
	 while($rev=mysql_fetch_array($qR)){ 
	    $reversadas .= "[".$rev['id_trans']."]";
	 }
	 
$query=mysql_query("
   SELECT * FROM seguro_transacciones 
   WHERE user_id !='' $wFecha $aseg order by id ASC");
  while($row=mysql_fetch_array($query)){
	 
	 if((substr_count($reversadas,"[".$row['id']."]")>0)){
	}else{
		
	  $total    +=$row['monto'];
	  $ganancia += $row['ganancia'];
	  $fh1		= explode(' ',$row['fecha']);
	  $Cliente  = explode("|", Cliente($row['id_cliente']));
	  $Client   = str_replace("|", "", $Cliente[0]);
	  $i++;
	  
	  $pref = GetPrefijo($row['id_aseg']);
	  $idseg = str_pad($row['id_poliza'], 6, "0", STR_PAD_LEFT);
	  //$prefi = $pref."-".$idseg;
	  
	  $TipoM 			= explode("/", Tipo($row['id_vehiculo']));
	  $TipoM['1'] 		= substr(formatDinero($TipoM['1']), 0, -3);
	  $TipoM['2'] 		= substr(formatDinero($TipoM['2']), 0, -3);
	  $TipoM['3'] 		= substr(formatDinero($TipoM['3']), 0, -3);
	  $TipoM['4'] 		= substr(formatDinero($TipoM['4']), 0, -3);
	  $monto 	  		= substr(formatDinero($row['monto']), 0, -3);
	  $TipoVehiculo 		= $TipoM['5'];
	  $codigo_general 	= $TipoM['6'];
	  
	  $marca = VehiculoExport($row['id_vehiculo']);
      $MarcaMod = explode("/", $marca);
	  
	  $id_vehiculo 			= $row['id_vehiculo'];
	  $PlanGeneral 			= PlanGeneral($TipoVehiculo);
	  $fecha_inicio 			= FechaReporteGeneral($row['fecha_inicio']);
	  $fecha_fin 			= FechaReporteGeneral($row['fecha_fin']);
	  $FechaHora 			= FechaHora($row['fecha_inicio']);
	  $marca_nom 			= $MarcaMod['0'];
	  $modelo_nom 			= $MarcaMod['1'];
	  $year 					= $MarcaMod['2'];
	  $chassis 				= $MarcaMod['3'];
	  $placa					= $MarcaMod['4'];
	  $nombre_cliente 		= $Cliente[0];
	  $apellido_cliente		= $Cliente[1];
	  $cedula 				= CedulaExport($Cliente[3]);
	  $telefono_cliente 		= $Cliente[2];



$html .= "1002;8;".$pref.";".$idseg.";".$PlanGeneral.";1;".$fecha_inicio.";".$fecha_fin.";".$FechaHora.";".$codigo_general.";1;1;".trim($marca_nom).";".trim($modelo_nom).";;".trim($year).";".trim($chassis).";".trim($placa).";4;4;".$monto.";0;".$nombre_cliente." ".$apellido_cliente.";".$cedula.";0;0\n";

 } }

/*$carpeta = 'EXCEL/DOM/'.$fDesde.'';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}*/
	
	//$sfile	= "Archivos/CLIENTES/Transacciones_$fDesde.xls"; // Ruta del archivo a generar 
	$sfile	= "TXT/GEN/MS_RDV_$fDesde.txt"; // Ruta del archivo a generar 

	$fp		= fopen($sfile,"w");
	
	fwrite($fp,$html); 
	fclose($fp); 
	
	echo $html;




