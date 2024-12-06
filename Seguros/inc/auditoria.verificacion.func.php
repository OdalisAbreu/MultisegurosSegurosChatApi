<?
	// FUNCION DE AUDITORIA
	
	function Auditoria($uid,$tipo,$descrip){
	
	$r2 = mysql_query("
	INSERT INTO auditoria_verificacion 
	(user_id,descrip,tipo,fecha_hora,ip) 
	VALUES
	('$uid','$descrip','$tipo','".date('Y/m/d H:i:s')."','".$_SERVER['REMOTE_ADDR']."')");

}

function AuditoriaNew($usuario,$password,$coneccion,$descrip,$peticion,$codigo,$empresa,$monto){
	$r2 = mysql_query("
	INSERT INTO auditoria 
	(user_id,pass,coneccion,descrip,peticion,codigo,empresa,fecha,ip,monto) 
	VALUES
	('".$usuario."','".$password."','".$coneccion."','".$descrip."','".$peticion."','".$codigo."','".$empresa."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."','".$monto."')");
}
function AuditoriaVentas($uid,$tipo,$descrip){
	
	$r2 = mysql_query("
	INSERT INTO auditoria_ventas 
	(user_id,transaccion_type,description,created_at) 
	VALUES
	('$uid','$tipo','$descrip','".date('Y/m/d H:i:s')."')");

}

?>