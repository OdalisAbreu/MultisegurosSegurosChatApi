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
	
	
	 $html .='poliza; VehiculoNo; Codigo de plan; tipos de movimientos; fechaInicio; FechaTermino; HoraInicio; Tipo de Vehiculo; Uso Vehiculo; Uso Especifico; Codigo Marca;  Codigo Modelo;  Modelo Descripcion;  Año del vehiculo; Chassis;  Placa; Cilindros; Pasajeros; Prima Neta;  Valor del Vehiculo;  Nombre Solicitante;  Documento Identidad; Fecha ';
  $html .="\n";
	
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
	  
	  
	  $ServOpcional =  explode("-", $row['serv_adc']);
		for($i =0; $i < count($ServOpcional); $i++){
		  if($ServOpcional[$i]>0){	
			$qprec2=mysql_query("SELECT id,sumar FROM servicios WHERE id='".$ServOpcional[$i]."' LIMIT 1");
			$rprec2=mysql_fetch_array($qprec2);
				if($rprec2['sumar']=='s'){
					$CostoServ += RepCostoServiciodosRemesa($u['id'],$ServOpcional[$i]);
					$ServMonto += RepMontoServiciodosRemesa($u['id'],$ServOpcional[$i]);
					
				}
		    }
			
		}
		
	$precio	 	 	 = RepMontoSeguro($row['id']) + $ServMonto;
	
	  $monto 	  = substr(formatDinero($precio), 0, -3);

	  $marca = VehiculoExport($row['id_vehiculo']);
      $MarcaMod = explode("/", $marca);

$html .=' '.$prefi.';'.$row['id_vehiculo'].';1;'.FechaReporte($row['fecha_inicio']).';'.FechaReporte($row['fecha_fin']).';00:00:00;'.$TipoM['0'].';1;1;'.trim($MarcaMod['0']).';'.trim($MarcaMod['1']).';'.trim($MarcaMod['0']).trim($MarcaMod['1']).';'.$MarcaMod['2'].';'.$MarcaMod['3'].';'.$MarcaMod['4'].';4;4;'.$monto.';0;'.$Client.' '.$Cliente[1].';'.Cedula($row['id_cliente']).';'.$row['fecha'].';';

 } }

/*$carpeta = 'EXCEL/DOM/'.$fDesde.'';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}*/
	
	//$sfile	= "Archivos/CLIENTES/Transacciones_$fDesde.xls"; // Ruta del archivo a generar 
	$sfile	= "TXT/DOM/MS_RDV_$fDesde.txt"; // Ruta del archivo a generar 

	$fp		= fopen($sfile,"w");
	
	fwrite($fp,$html); 
	fclose($fp); 
	
	echo $html;




