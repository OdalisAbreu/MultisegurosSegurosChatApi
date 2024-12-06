<?
	ini_set('display_errors',1);
	set_time_limit(0);
	include("inc/conexion_inc.php");
	Conectarse();
	
	// --------------------------------------------	
	if($_GET['fecha1']){
		$fecha1 = $_GET['fecha1'];
	}else{ 
		$fecha1 = fecha_despues(''.date('d/m/Y').'',-20);
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
	


function VerVentas($id){
	$query=mysql_query("SELECT * FROM  seguro_trans_history WHERE id_trans ='".$id."' AND tipo = 'seg' ");
	//echo "SELECT * FROM  seguro_trans_history WHERE id_trans ='".$id."' ";
	$row=mysql_fetch_array($query);
	if($row['id_trans']){
	   return 1;
	}else{
	   return 2;
	}
}


	function VehiculoHistory($id){
		$query=mysql_query("SELECT * FROM  seguro_vehiculo WHERE id='".$id."' LIMIT 1");
		$row=mysql_fetch_array($query);
		return $row['veh_tipo'];
		
	}
	
	
	function IfMontoTarifasHistory($veh_tipo,$vigencia){
		   $queryT=mysql_query("SELECT id,veh_tipo,3meses,6meses,12meses FROM seguro_tarifas_backup 
		   WHERE veh_tipo ='".$veh_tipo."'  LIMIT 1");
		   $rowT=mysql_fetch_array($queryT);
		  
		  if($vigencia ==3)  return $rowT['3meses'];
		  if($vigencia ==6)  return $rowT['6meses'];
		  if($vigencia ==12) return $rowT['12meses']; 
		  
	}
	
	function IfMontoTarifasHistoryCosto($veh_tipo,$vigencia){
		   $queryT=mysql_query("SELECT id, veh_tipo, 3meses, 6meses, 12meses FROM seguro_costos_backup 
		   WHERE veh_tipo ='".$veh_tipo."'  LIMIT 1");
		   $rowT=mysql_fetch_array($queryT);
		  
		  if($vigencia ==3)  return $rowT['3meses'];
		  if($vigencia ==6)  return $rowT['6meses'];
		  if($vigencia ==12) return $rowT['12meses']; 
		  
	}
	
	//==================================================//
	$query=mysql_query("SELECT * FROM  seguro_transacciones WHERE fecha >='2018-04-04 00:00:00' 
	AND fecha <='2018-06-13 23:59:59' ");
	//echo "<br><b>CONSULTA</B> SELECT * FROM  seguro_transacciones WHERE id <= '2121' <br>";
	while($row=mysql_fetch_array($query)){
	
	$tipo_veh = VehiculoHistory($row['id_vehiculo']);
	$monto = IfMontoTarifasHistory($tipo_veh,$row['vigencia_poliza']); 
	$costo = IfMontoTarifasHistoryCosto($tipo_veh,$row['vigencia_poliza']);
	
	
	if(VerVentas($row['id'])=='2'){
	
	echo "el ID: ".$row['id']." no esta registrado<br>";
	mysql_query(
		"INSERT INTO seguro_trans_history 
		(id_trans, id_aseg, tipo, id_serv_adc, monto, costo, fecha) 
		VALUES 
		('".$row['id']."',
		'".$row['id_aseg']."',
		'seg',
		'0',
		'".$monto."',
		'".$costo."',
		'".$row['fecha']."'
		)");
		
	}else{
		
	echo "El ID: ".$row['id']." esta registrado, <b>ACTUALIZANDO</b><br>";
	
	mysql_query("UPDATE seguro_trans_history SET id_aseg ='".$row['id_aseg']."',
		costo = '".$costo."', monto = '".$monto."' WHERE id_trans='".$row['id']."'");
		
		echo"
		UPDATE seguro_trans_history SET id_aseg ='".$row['id_aseg']."',
		costo = '".$costo."', monto = '".$monto."' WHERE id_trans='".$row['id']."' <br>";
		echo "===================================<br><br>";
		
	}
}
?>



