<?
// FUNCION DE AUDITORIA
//ini_set('display_errors',1);

function Auditoria($usuario, $password, $coneccion, $descrip, $peticion, $codigo, $empresa, $monto, $request = null)
{
	$r2 = mysql_query("
	INSERT INTO auditoria 
	(user_id,pass,coneccion,descrip,peticion,codigo,empresa,fecha,ip,monto, request) 
	VALUES
	('" . $usuario . "','" . $password . "','" . $coneccion . "','" . $descrip . "','" . $peticion . "','" . $codigo . "','" . $empresa . "','" . date('Y-m-d H:i:s') . "','" . $_SERVER['REMOTE_ADDR'] . "','" . $monto . "','" . $request . "')");
}
