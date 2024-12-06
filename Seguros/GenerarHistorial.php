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
	
	$wFecha2 = "fecha >= '2018-05-17 00:00:00' AND fecha <= '2018-05-17 23:59:59' ";


function VerVentas($id){
	$query=mysql_query("SELECT * FROM  seguro_trans_history WHERE id_trans ='".$id."' ");
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
	
	
	function MontoServicioHistory($id,$vigencia){
		$r6 = mysql_query("SELECT id, 3meses, 6meses, 12meses FROM servicios_backup WHERE id='".$id."'LIMIT 1");
		if($id>0){
			   while($row6=mysql_fetch_array($r6)){
				  if($vigencia ==3)  return $row6['3meses'];
				  if($vigencia ==6)  return $row6['6meses'];
				  if($vigencia ==12) return $row6['12meses']; 
			 }
	 	}
		 
	}
	
	function IfMontoTarifasHistory($veh_tipo,$vigencia){
		   $queryT=mysql_query("SELECT id,veh_tipo,3meses,6meses,12meses FROM seguro_tarifas_backup 
		   WHERE veh_tipo ='".$veh_tipo."'  LIMIT 1");
		   $rowT=mysql_fetch_array($queryT);
		  
		  if($vigencia ==3)  return $rowT['3meses'];
		  if($vigencia ==6)  return $rowT['6meses'];
		  if($vigencia ==12) return $rowT['12meses']; 
		  
	}
	
	
	//==================================================//
	$query=mysql_query("SELECT * FROM  seguro_transacciones WHERE id <= '2121'");
	//echo "<br><b>CONSULTA</B> SELECT * FROM  seguro_transacciones WHERE id <= '2121' <br>";
	while($row=mysql_fetch_array($query)){
	
	
	if(VerVentas($row['id'])=='2'){
	
	echo "no hay registro";
	
	
	//$Veh = explode("|", VehiculoHistory($row['id_vehiculo']));
	$monto = IfMontoTarifasHistory(VehiculoHistory($row['id_vehiculo']),$row['vigencia_poliza']); 
	
	/*echo "<br><br><b>TRANSACION</b><br>";
	echo  "ID trans: ".$row['id']."<br>";
	echo  "monto seguro: ".$monto."<br>";
	echo  "fecha: ".$row['fecha']."<br>";
	*/
	/*PARA REGISTRAR DATOS DEL USUARIO*/
	mysql_query(
		"INSERT INTO seguro_trans_history 
		(id_trans, tipo, id_serv_adc, monto, fecha) 
		VALUES 
		('".$row['id']."',
		'seg',
		'0',
		'".$monto."',
		'".$row['fecha']."'
		)");
		
	
	//echo "<br><b>SERVICIOS</b><br>";
	//echo  "ID trans: ".$row['id']."<br>";
	//echo  "serv_adc: ".$row['serv_adc']."<br>";
	
	$porciones = explode("-", $row['serv_adc']);
	 
	for($i =0; $i < count($porciones); $i++){ 
	
	if($porciones[$i]>0){
	$MontoServ = MontoServicioHistory($porciones[$i],$row['vigencia_poliza']); 
	
	//echo "#".$porciones[$i]." - ".$MontoServ."<br>";
	//echo  "fecha: ".$row['fecha']."<br>";
	
	/*PARA REGISTRAR DATOS DEL USUARIO*/
	mysql_query(
		"INSERT INTO seguro_trans_history 
		(id_trans, tipo, id_serv_adc, monto, fecha)   
		VALUES 
		('".$row['id']."',
		'serv',
		'".$porciones[$i]."',
		'".$MontoServ."',
		'".$row['fecha']."'
		)");
		
   
	  		}	
   		}
	
	}else{
		echo "<b>registros: </b>".$row['id']."<br>";
	}
}
?>



