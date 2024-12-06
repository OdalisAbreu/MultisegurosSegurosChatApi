<?
	session_start();
	ini_set('display_errors',1);
	set_time_limit(0);
	include("../inc/conexion_inc.php");
	include("../inc/fechas.func.php");
	Conectarse();
	
	
	
	
	// CONSULTANDO SI YA EXISTE EL RESUMEN:
	function IfExisteCorte($conf){
		$sql = mysql_query("
		SELECT id FROM corte_de_balance 
		WHERE 
		fecha >='".$conf['fecha']." 00:00:00' AND fecha < '".$conf['fecha']." 23:59:59' 
		AND user_id 	='".$conf['user_id']."' ");
		$p	= mysql_fetch_array($sql);
		
		if($p['id'])
			return true;
		else
			return false;
		
	}
	// ...
	
	
	
	
	if($_GET['fecha1']){
		$fDesde = $_GET['fecha1'];
	}else{
		$fecha1 = fecha_despues(''.date('d/m/Y').'',-0);
		$fd1	= explode('/',$fecha1);
		$fDesde = $fd1[2].'-'.$fd1[1].'-'.$fd1[0];
	}
	
	if($_GET['user_id']){
		$wUser = "AND id ='".$_GET['user_id']."'";	
	}
	
	$fechaActual = date('Y-m-d H:i:s');
	 
  	$sq =mysql_query(
	"SELECT id,balance FROM personal 
	WHERE funcion_id !=1 
	$wUser
	");
	while($p=mysql_fetch_array($sq)){
	
	// GUARDANDO BALANCES AL CORTE 12:00 AM
	if(!IfExisteCorte($c=array('fecha'=>$fDesde,'user_id'=>$p['id']))){
		
		echo "Guardando Balance: [".$p['id']." - Guardado. <br>";
		
		mysql_query(
		"INSERT INTO corte_de_balance (fecha,user_id,balance) 
		VALUES
		('".$fechaActual."','".$p['id']."','".$p['balance']."') ");
	
	}else{
		echo "ID:".$p['id']." -  Ya existe!";	
	}
	echo mysql_error();
}
	
?>