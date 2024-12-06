<?php



ini_set('display_errors', 1);
include('inc/config.php');
include('inc/conexion_inc.php');
define("TIM_INIC", getTiempo()); // <- tiempo inicio.
include('inc/auditoria.balance.func.php');
include('inc/nombres.func.php');

error_reporting(~E_ALL);
$claves = array_keys($Cod_resp);


function get_Reverso($seg, $num){
	global $seg, $num, $claves;

	Conectarse();


	// CONSULTAMOS TRANSACCION
	$q1 = mysql_query("
	## - Reverso.
	## CONSULTANDO PARA REVERSAR
	SELECT * FROM seguro_transacciones WHERE id_aseg = '".$seg."' 
	AND id_poliza = '".$num."' LIMIT 1");
	$trans = mysql_fetch_array($q1);
	
	$rs2 = mysql_query("SELECT id,funcion_id,id_dist,balance FROM personal WHERE
	id = '" . $trans['user_id'] . "' LIMIT 1");
	$u = mysql_fetch_array($rs2);

	
	$user_id = $u['id'];

	// BUSCAR: id_dist2
	$sql = mysql_query("
		SELECT id_dist,funcion_id,balance FROM personal WHERE id='" . $u['id_dist'] . "'  LIMIT 1");
	$dist_id2 = mysql_fetch_array($sql);
	// =====================



if ($trans['id'] > '1') {
			
			return Reverso($conf = array(
			'user_id' 		=> $user_id, 
			'trans_id' 		=> $trans['id'],
			'id_aseg' 		=> $trans['id_aseg'],
			'id_poliza' 		=> $trans['id_poliza'],
			'password' 		=> $trans['password'],
			'balance' 		=> $trans['balance'],
			'tipo_conex' 	=> $trans['tipo_conex'], 
			'monto' 			=> $trans['totalpagar'],
			'montoventa' 	=> $trans['monto'],  
			'totalpagar' 	=> $trans['totalpagar'], 
			'funcion_id' 	=> $u['funcion_id'], 
			'funcion_id2' 	=> $dist_id2['funcion_id'], 
			'dist_id' 		=> $u['id_dist'], 
			'balance1' 		=> $u['balance'],
			'balance2' 		=> $dist_id2['balance'],
			'id_dist2' 		=> $dist_id2['id_dist'], 
			'ganancia2' 		=> $trans['ganancia2'], 
			'ganancia5' 		=> $trans['ganancia5'], 
			'usar_bl_princ' => $u['usar_bl_princ'],));

		}  

		return $cod_resp . '/' . $conf["Cod_resp"][$cod_resp];
	
}


// ------------------------------------
function Reverso($conf){
	

		$resp = '00/Reverso Efectuado/1/' . $conf['trans_id'] . '/' . $numRev;


		// NUEVO MODELO DE REVERSOS POR INSERT 
		mysql_query("
			INSERT INTO seguro_transacciones_reversos 
			(id_trans,id_aseg,id_poliza,id_pers,fecha,monto) 
			VALUES ('".$conf['trans_id']."','".$conf['id_aseg']."','".$conf['id_poliza']."','".$conf['user_id']."','".date("Y-m-d H:i:s")."',
			'".$conf['montoventa']."')");

		$nombreseguro = NombreSeguroS($conf['id_aseg']);
		$prefijo = GetPrefijo($conf['id_aseg']).'-'.str_pad($conf['id_poliza'],6, "0", STR_PAD_LEFT);
		$bdesp = $conf['balance1'] + $conf['totalpagar'];
		$credito_Actual = CreditoActualAnular($conf['user_id']);
		// NUEVO MODELO DE REVERSOS POR INSERT
			mysql_query("
			INSERT INTO  recarga_retiro 
			(id_pers,autorizada_por,monto, 
			balance_anterior,balance_despues,tipo,
			comentario,fecha,
			rec_id,cred_actual) 
			VALUES ('".$conf['user_id']."','6','".$conf['totalpagar']."',
			'".$conf['balance1']."','".$bdesp."','NC',
			'anulacion de ".$nombreseguro." ".$prefijo."','".date("Y-m-d H:i:s")."',
			'0','".$credito_Actual."')");

		$idU = $conf['user_id'];

		mysql_query("
		UPDATE personal SET balance =(balance + " . $conf['totalpagar'] . ")
		WHERE id='" . $idU . "' LIMIT 1");


		// ========================
		// REVERSAR MULTI-NIVEL
		// ========================

		if ($conf["dist_id"] == '6' && $conf["funcion_id"] == '2') {
			// no hay movimiento multinivel
		} elseif ($conf["id_dist2"] == '6' && $conf["funcion_id"] == '2') {
			// Nivel2-> Nivel5-> Nivel1
			// solo movimiento en Nivel5
			mysql_query("
		UPDATE personal SET balance =(balance - " . $conf['ganancia5'] . ")
		WHERE id='" . $conf["dist_id"] . "' LIMIT 1");
		} elseif ($conf["id_dist2"] == '6' && $conf["funcion_id"] == '3') {
			// Nivel3-> Nivel2-> Nivel1
			if ($conf['funcion_id2'] == '2'){
				
				
			$BalActualAnular = BalActualAnular($conf['dist_id']);
			$bdesp = $BalActualAnular - $conf['ganancia2'];
			$credito_Actual = CreditoActualAnular($conf['dist_id']);
		// NUEVO MODELO DE REVERSOS POR INSERT
			mysql_query("
			INSERT INTO  recarga_retiro 
			(id_pers,autorizada_por,monto, 
			balance_anterior,balance_despues,tipo,
			comentario,fecha,
			rec_id,cred_actual) 
			VALUES ('".$conf['dist_id']."','6','".$conf['ganancia2']."',
			'".$BalActualAnular."','".$bdesp."','Retiro',
			'anulacion de ".$nombreseguro." ".$prefijo."','".date("Y-m-d H:i:s")."',
			'0','".$credito_Actual."')
			");
			
			
			
				mysql_query("
			UPDATE personal SET balance =(balance - " . $conf['ganancia2'] . ")
			WHERE id='" . $conf["dist_id"] . "' LIMIT 1"); }else
				// Nivel3-> Nivel5-> Nivel1
				mysql_query("
			UPDATE personal SET balance =(balance - " . $conf['ganancia5'] . ")
			WHERE id='" . $conf["dist_id"] . "' LIMIT 1");
		} elseif ($conf["id_dist2"] !== '6' && $conf["funcion_id"] == '3') {
			// Nivel3-> Nivel2-> Nivel5-> Nivel1
			// solo movimiento en Nivel2 y Nivel5
			mysql_query("
		UPDATE personal SET balance =(balance - " . $conf['ganancia2'] . ") 
		WHERE id='" . $conf["dist_id"] . "' LIMIT 1");
			// movimiento en Nivel5
			mysql_query("
		UPDATE personal SET balance =(balance - " . $conf['ganancia5'] . ")
		WHERE id='" . $conf["id_dist2"] . "' LIMIT 1");
			//echo "#".$conf["id_dist2"]."#".$conf["dist_id"];
		}

		
		Auditoria($conf['user_id'],$conf['password'],$conf['tipo_conex'],'Anulando Seguro #'.$conf['trans_id'].' ','anular_ok','00','',$conf['balance']);

		// GUARDAMOS EN LOG
		$conf1 = array('num_tele' => $conf['id'], 'monto' => $conf['monto']);
		SaveLogs2(array('tipo' => 'REVERSO:00' . $api[0], 'errors' => $errors, 'datos' => $conf1));


	



	return $resp;
}



if ($_REQUEST['seg'] || $_REQUEST['num']) {
	
	$seg = $_REQUEST['seg'];
	$num = $_REQUEST['num'];
	
	//if(ValidarReverso($seg,$num) =='15'){
	
			$respRev = get_Reverso($seg,$num);
			echo $respRev;
	/*}else{
			Auditoria($_POST['usuario'],$_POST['clave'],'','Acceso no permitido - Metodo: '.$_SERVER['REQUEST_METHOD'].' ','anular_error','19','','');
			echo '15/Error Anulando Seguro/00';
	}*/
	
}
?>
