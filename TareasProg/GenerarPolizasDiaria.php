<?
	echo "ihnabilitado";
	exit();
	session_start();
	ini_set('display_errors',1);
	date_default_timezone_set('America/Santo_Domingo');
	set_time_limit(0);
	include("../inc/conexion_inc.php");
	include("../inc/fechas.func.php");
	Conectarse();
	
	
	require_once('tcpdf/config/lang/eng.php');
	require_once('tcpdf/tcpdf.php');
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
		$fecha2 = fecha_despues(''.date('d/m/Y').'',0);
	}
	
	
	$fd1	= explode('/',$fecha1);
	$fh1	= explode('/',$fecha2);
	$fDesde 	= $fd1[2].'-'.$fd1[1].'-'.$fd1[0];
	$fHasta 	= $fh1[2].'-'.$fh1[1].'-'.$fh1[0];
	$wFecha 	= "AND fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59'";
	
	
	//CONSULTANDO VENTAS
	function TotalVentas($conf){	
	  
		$wFecha = "fecha >= '".$conf['fech1']." 00:00:00' AND fecha < '".$conf['fech1']." 23:59:59'";
		
		$t['ventas']		= 0;
		$t['numTras']	= 0;
		
		$query=mysql_query("
		SELECT 
		monto,fecha,id
		FROM seguro_transacciones
		WHERE
		$wFecha
		");
		
		$i = 0;
		while($res = mysql_fetch_array($query)){
		
			$t['ventas']		+= $res['monto'];
			$i++;
		
		}
		
		$t['numTras'] =  $i;
		return $t;
		
	}
	
		
	
	
	
	
	

	
	
	 $res1 = TotalVentas($c=array('fech1'=>$fDesde));
	 //print_r($res1);
	 
  	$sq =mysql_query("SELECT id,id_dist,seguro_porc2,cal_costo FROM personal 
	WHERE funcion_id ='7' AND activo ='si'");
	while($p=mysql_fetch_array($sq)){
	
	// GUARDANDO BALANCES AL CORTE 12:00 AM
		echo "<br><br>Generando ID. ".$p['id']."<br>";
		echo "Fecha Desde. ".$fDesde."<br>";
		echo "Venta. ".$res1['ventas']."<br>";
		
		if($p['cal_costo']=='no'){
			$toalC = ($p['seguro_porc2']/100) * $res1['ventas'];
		}else{
			
		}
		
	
	
	
		
		
	
			
	
	
	
}
	
?>