<?php

///exit();
session_start();
ini_set('display_errors', 1);
//include('inc/config.php');
include('inc/conexion_inc.php');
include('inc/auditoria.balance.func.php');
Conectarse();


if ($_REQUEST) {



	$rs2 = mysql_query("
	SELECT id,activo,user,password,balance,tipo_conex,usar_bl_princ,id_dist FROM personal WHERE 
	user = '" . $_REQUEST['usuario'] . "' AND password='" . $_REQUEST['clave'] . "' 
	LIMIT 1");
	$numU = mysql_num_rows($rs2);

	if ($numU == '1') {

		$p = mysql_fetch_array($rs2);
		//VALIDAR SI ESTA ACTIVO O NO
		if ($p['activo'] == 'si') {

			if ($p['usar_bl_princ'] == 'no') {
				Auditoria($p['id'], $p['password'], $p['tipo_conex'], 'OK=solicitud de balance no compartido ', 'balance', '00', '', $p['balance']);
				exit("" . $p['balance'] . "/Balance Actual/00");
			} else 
	
	if ($p['usar_bl_princ'] == 'si') {
				// USAR BAL PRINCIPAL.
				$rs3 = mysql_query(
					"SELECT id, balance FROM personal WHERE 
		   id = '" . $p['id_dist'] . "' LIMIT 1"
				);
				$p1 = mysql_fetch_array($rs3);

				Auditoria($p['id'], $p['password'], $p['tipo_conex'], 'OK=solicitud de balance si compartido', 'balance', '00', '', $p1['balance']);
				exit("" . $p1['balance'] . "/Balance Actual/00");
			}
		} else if ($p['activo'] == 'no') {
			Auditoria($p['id'], $p['password'], $p['tipo_conex'], 'ERROR=solicitud de balance, usuario inactivo ', 'balance', '18', '', $p['balance']);
			exit("18/Usuario inactivo/00");
		}
	} else {
		Auditoria($_POST['usuario'], $_POST['clave'], '', 'ERROR=solicitud de balance', 'balance', '14', '', '');
		exit("14/Usuario o Password incorrectos/00");
	}
} else {

	Auditoria('', 'Desconocido', 'http-request', 'Acceso no permitido - Metodo: ' . $_SERVER['REQUEST_METHOD'], 'balance', '19', '', '', json_encode($_REQUEST));

	exit('ACCESO NO PERMITIDO');
}
