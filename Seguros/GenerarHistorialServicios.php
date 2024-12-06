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
	$query=mysql_query("SELECT * FROM  seguro_trans_history WHERE id_trans ='".$id."' AND tipo='serv'");
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
		$r6 = mysql_query("SELECT id, 3meses, 6meses, 12meses FROM servicios_backup	 WHERE id='".$id."'LIMIT 1");
		if($id>0){
			   while($row6=mysql_fetch_array($r6)){
				  if($vigencia ==3)  return $row6['3meses'];
				  if($vigencia ==6)  return $row6['6meses'];
				  if($vigencia ==12) return $row6['12meses']; 
			 }
	 	}
		 
	}
	
	function MontoServicioHistoryCosto($id,$vigencia){
		$r6 = mysql_query("SELECT id, 3meses_costos, 6meses_costos, 12meses_costos FROM servicios_backup WHERE id='".$id."'LIMIT 1");
		if($id>0){
			   while($row6=mysql_fetch_array($r6)){
				  if($vigencia ==3)  return $row6['3meses_costos'];
				  if($vigencia ==6)  return $row6['6meses_costos'];
				  if($vigencia ==12) return $row6['12meses_costos']; 
			 }
	 	}
		 
	}
	
	
	
	
	//==================================================//
	$query=mysql_query("SELECT * FROM  seguro_transacciones WHERE fecha >='2018-04-04 00:00:00' 
	AND fecha <='2018-06-13 23:59:59' AND serv_adc !='' limit 1");
	//echo "<br><b>CONSULTA</B> SELECT * FROM  seguro_transacciones WHERE id <= '2121' <br>";
	while($row=mysql_fetch_array($query)){
	
	
	if(VerVentas($row['id'])=='2'){
	
	echo "el ID: ".$row['id']." no esta registrado<br>";
	
	$porciones = explode("-", $row['serv_adc']);
	 
	for($i =0; $i < count($porciones); $i++){ 
	
	if($porciones[$i]>0){
	$MontoServ = MontoServicioHistory($porciones[$i],$row['vigencia_poliza']); 
	$CostoServ = MontoServicioHistoryCosto($porciones[$i],$row['vigencia_poliza']); 
	//echo "#".$porciones[$i]." - ".$MontoServ."<br>";
	//echo  "fecha: ".$row['fecha']."<br>";
	
	/*PARA REGISTRAR DATOS DEL USUARIO*/
	/*mysql_query(
		"INSERT INTO seguro_trans_history 
		(id_trans, id_aseg, tipo, id_serv_adc, monto, costo, fecha)   
		VALUES 
		('".$row['id']."',
		'".$row['id_aseg']."',
		'serv',
		'".$porciones[$i]."',
		'".$MontoServ."',
		'".$CostoServ."',
		'".$row['fecha']."'
		)");*/
		
   echo "INSERT INTO seguro_trans_history 
		(id_trans, id_aseg, tipo, id_serv_adc, monto, costo, fecha)   
		VALUES 
		('".$row['id']."',
		'".$row['id_aseg']."',
		'serv',
		'".$porciones[$i]."',
		'".$MontoServ."',
		'".$CostoServ."',
		'".$row['fecha']."'
		)";
	  		}	
   		}
	
	}else{
		
		echo "El ID: ".$row['id']."  esta registrado, <b>ACTUALIZANDO</b><br>";
	
	$porciones = explode("-", $row['serv_adc']);
	 
	for($i =0; $i < count($porciones); $i++){ 
	
	if($porciones[$i]>0){
	$MontoServ = MontoServicioHistory($porciones[$i],$row['vigencia_poliza']); 
	$CostoServ = MontoServicioHistoryCosto($porciones[$i],$row['vigencia_poliza']); 
	//echo "#".$porciones[$i]." - ".$MontoServ."<br>";
	//echo  "fecha: ".$row['fecha']."<br>";
	
	/*mysql_query("UPDATE seguro_trans_history SET id_serv_adc ='".$row['id_aseg']."', id_serv_adc ='".$porciones[$i]."',
		costo = '".$CostoServ."', monto = '".$MontoServ."' WHERE id_trans='".$row['id']."'");*/
		
		echo"
		UPDATE seguro_trans_history SET id_aseg ='".$row['id_aseg']."', id_serv_adc ='".$porciones[$i]."',
		costo = '".$CostoServ."', monto = '".$MontoServ."' WHERE id_trans='".$row['id']."' <br>";
		echo "===================================<br><br>";
		
		
	/*PARA REGISTRAR DATOS DEL USUARIO*/
	mysql_query(
		"INSERT INTO seguro_trans_history 
		(id_trans, id_aseg, tipo, id_serv_adc, monto, costo, fecha)   
		VALUES 
		('".$row['id']."',
		'".$row['id_aseg']."',
		'serv',
		'".$porciones[$i]."',
		'".$MontoServ."',
		'".$CostoServ."',
		'".$row['fecha']."'
		)");
		
   
	  		}	
   		}
		
		
		
	}
}





$query=mysql_query("SELECT * FROM  seguro_transacciones WHERE fecha >='2017-01-01 00:00:00' 
	AND fecha <='2019-06-30 23:59:59' AND serv_adc !='' ");
	while($row=mysql_fetch_array($query)){
		
		$porciones = explode("-", $row['serv_adc']);
	 
	for($i =0; $i < count($porciones); $i++){
		
	if($porciones[$i]>0){
		
		$Costo2 = MontoServicioHistoryCosto($porciones[$i],$row['vigencia_poliza']);	
		$Monto2 = MontoServicioHistory($porciones[$i],$row['vigencia_poliza']);
		
		$r++;
		echo "<b>#".$r."</b> ==>> <b>ID: </b>".$row['id']." ==>> <b>Aseguradora:</b> ".$row['id_aseg']." ==>> <b>Servicio opcional:</b> ".$porciones[$i]." ==>>  
		<b>Vigencia Poliza:</b> ".$row['vigencia_poliza']." ==>> <b>Costo:</b> ".$Costo2." ==>> <b>Monto:</b> ".$Monto2."<br>";
		
		/*mysql_query("UPDATE seguro_trans_history SET id_aseg ='".$row['id_aseg']."', costo = '".$Costo2."', 
		monto = '".$Monto2."', fecha = '".$row['fecha']."' WHERE id_trans='".$row['id']."' AND 
		id_serv_adc ='".$porciones[$i]."' AND tipo='serv'");*/
		
		echo "UPDATE seguro_trans_history SET id_aseg ='".$row['id_aseg']."', costo = '".$Costo2."', 
		monto = '".$Monto2."', fecha = '".$row['fecha']."' WHERE id_trans='".$row['id']."' AND 
		id_serv_adc ='".$porciones[$i]."' AND tipo='serv' <br><br>";
		
	 }
	}
		
	}
?>



