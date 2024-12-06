<?
// ARCHIVO DE CONFIGURACION DEL WEBSERVICE
// by LinksDominicana.com
// ----------------------------------------

define("WS_DIR", "ws_dev");
define("WS_VER", "uWS6.3.8-beta");

define("LOGS", 1);
// transacciones que exeden el tiempo:
define("EXEDTIME", 19);


// CODIGOS DE RESPUESTA
// --------------------------------------
$conf['Cod_resp'] = array(
	'00' => 'Seguro Procesado Correctamente',
	'11' => 'No tiene balance disponible',
	'14' => 'Usuario y/o Password incorrectos',
	'15' => 'Problemas de comunicacion con el webservice',
	'16' => 'Producto no disponible'
);
//---------------------------------------


function GetTransID_PAT($user)
{
	mysql_query(
		"INSERT INTO no_operacion_patria (fecha,user_id) 
		VALUES ('" . date('Y-m-d H:i:s') . "','" . $user . "')"
	);
	return mysql_insert_id();
}

function GetTransID_DOM($user)
{
	mysql_query(
		"INSERT INTO no_operacion_dominicana (fecha,user_id) 
		VALUES ('" . date('Y-m-d H:i:s') . "','" . $user . "')"
	);
	return mysql_insert_id();
}

function GetTransID_GENERAL($user)
{
	mysql_query(
		"INSERT INTO no_operacion_general (fecha,user_id) 
		VALUES ('" . date('Y-m-d H:i:s') . "','" . $user . "')"
	);
	return mysql_insert_id();
}

function GetTransID_ATRIO($user)
{
	mysql_query(
		"INSERT INTO no_operacion_atrio (fecha,user_id) 
		VALUES ('" . date('Y-m-d H:i:s') . "','" . $user . "')"
	);
	return mysql_insert_id();
}

function GetTransID_sura($user)
{
	mysql_query(
		"INSERT INTO no_operacion_sura (fecha,user_id) 
		VALUES ('" . date('Y-m-d H:i:s') . "','" . $user . "')"
	);
	return mysql_insert_id();
}

function GetTransID_sura_p($user)
{
	mysql_query(
		"INSERT INTO no_operacion_sura_premium (fecha,user_id) 
		VALUES ('" . date('Y-m-d H:i:s') . "','" . $user . "')"
	);
	return mysql_insert_id();
}

function GetTransID_one_alliance_p($user)
{
	mysql_query(
		"INSERT INTO no_operacion_one_alliance_premium (fecha,user_id) 
		VALUES ('" . date('Y-m-d H:i:s') . "','" . $user . "')"
	);
	return mysql_insert_id();
}

function GetPrefijo($id)
{
	$sqlp = mysql_query("SELECT id,prefijo FROM seguros 
		WHERE id='" . $id . "' LIMIT 1");
	$distp = mysql_fetch_array($sqlp);
	return $distp['prefijo'];
}
