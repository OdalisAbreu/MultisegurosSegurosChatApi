<?
	
	exit();
	session_start();
	ini_set('display_errors',1);
	set_time_limit(0);
	include("../inc/conexion_inc.php");
	include("../inc/fechas.func.php");
	Conectarse();
	
	// --------------------------------------------	
	if($_GET['fecha1']){
		$fecha1 = $_GET['fecha1'];
	}else{
		$fecha1 = fecha_despues(''.date('d/m/Y').'',-3);
	}
	// --------------------------------------------
	if($_GET['fecha2']){
		$fecha2 = $_GET['fecha2'];
	}else{
		$fecha2 = fecha_despues(''.date('d/m/Y').'',-3);
	}
	
	
	$fd1	= explode('/',$fecha1);
	$fh1	= explode('/',$fecha2);
	$fDesde 	= $fd1[2].'-'.$fd1[1].'-'.$fd1[0];
	$fHasta 	= $fh1[2].'-'.$fh1[1].'-'.$fh1[0];
	$wFecha 	= "AND fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 24:00:00'";
	
	
	//CONSULTANDO VENTAS
	function TotalVentas($conf){	
	  
		$wFecha = "fecha >= '".$conf['fech1']." 00:00:00' AND fecha < '".$conf['fech1']." 23:59:59'";
		
		$t['ventas']		= 0;
		$t['numTras']	= 0;
		
		
		$qR=mysql_query("SELECT * FROM seguro_transacciones_reversos WHERE id !=''");
	 	while($rev=mysql_fetch_array($qR)){ 
	    	$reversadas .= "[".$rev['id_trans']."]";
	 	}
	 
	 
		$query=mysql_query("
		SELECT 
		monto,fecha,id
		FROM seguro_transacciones
		WHERE
		$wFecha
		");
		
		$i = 0;
		while($res = mysql_fetch_array($query)){
			if((substr_count($reversadas,"[".$u['id']."]")>0)){
			}else{		
					$t['ventas']		+= $res['monto'];
					$i++;
			}
		}
		
		$t['numTras'] =  $i;
		return $t;
		
	}
		
		// CONSULTANDO SI YA EXISTE EL RESUMEN:
		function IfExisteCorte($conf){
			$sql = mysql_query("
			SELECT id FROM comisiones_detalle 
			WHERE 
			fecha >='".$conf['fecha']." 00:00:00' AND 
			fecha < '".$conf['fecha']." 23:59:59' AND 
			id_benef ='".$conf['user_id']."' ");
			$p	= mysql_fetch_array($sql);
			
			if($p['id'])
				return true;
			else
				return false;
			
		}
		// ...
		
	
	
	
	
	

	
	
	 $res1 = TotalVentas($c=array('fech1'=>$fDesde));
	 //print_r($res1);
	 
  	$sq =mysql_query("SELECT id,id_dist,seguro_porc2,cal_costo FROM personal 
	WHERE funcion_id ='7' AND activo ='si'");
	while($p=mysql_fetch_array($sq)){
	
	// GUARDANDO BALANCES AL CORTE 12:00 AM
	if(!IfExisteCorte($c=array('fecha'=>$fDesde,'user_id'=>$p['id']))){
		echo "<br><br>Generando ID. ".$p['id']."<br>";
		echo "Fecha Desde. ".$fDesde."<br>";
		echo "Venta. ".$res1['ventas']."<br>";
		
		if($p['cal_costo']=='no'){
			$toalC = ($p['seguro_porc2']/100) * $res1['ventas'];
		}else{
			
		}
		
	
	
	echo "INSERT INTO comisiones_detalle(
			id,id_benef,user_id,monto,porciento,numtras,fecha,fecha_gen,comision
			
			) VALUES (
				'','".$p['id']."','".$p['id_dist']."','".$res1['ventas']."',
				   '".$p['seguro_porc2']."','".$res1['numTras']."','".$fDesde." 00:00:00"."','".date('Y-m-d H:i:s')."','".$toalC."'
			)";
		
		if(mysql_query("
			INSERT INTO comisiones_detalle(
			id,id_benef,user_id,monto,porciento,numtras,fecha,fecha_gen,comision
			
			) VALUES (
				'','".$p['id']."','".$p['id_dist']."','".$res1['ventas']."',
				   '".$p['seguro_porc2']."','".$res1['numTras']."','".$fDesde." 00:00:00"."','".date('Y-m-d H:i:s')."','".$toalC."'
			)")){
			
				echo $fDesde." -> Ventas de <b>".$p['cliente_id']." </b> -Generada!<br>";
			}
	
			//echo mysql_error();
	
	}else{
		echo $fDesde." ->Venta Existe - <B>No Guardada!</B><br>";
	}
	
	
}
	
?>