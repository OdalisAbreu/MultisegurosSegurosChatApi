<?php
ini_set('display_errors', 0);
include 'inc/conexion_inc.php';
define("TIM_INIC", getTiempo());
include 'inc/config.php';
include 'inc/nombres.func.php';
Conectarse();

error_log(json_encode($_REQUEST));

function fecha_despues2($fecha, $dias)
{
	list($ano, $mes, $dia) = explode("-", $fecha);
	if (!checkdate($mes, $dia, $ano)) {
		return false;
	}
	$dia = $dia + $dias;
	$fecha = date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano));
	return $fecha;
}

// -----------------------------------
function SolicitarSeguro($conf)
{
	error_log(json_encode($conf));
	$cliente['seguro_porc' . $conf['cod_p'] . ''] =
		$conf['seguro_porc' . $conf['cod_p'] . ''];
	//$cliente['terminal_asig'] 	= $conf['terminal_asig'];
	//$cliente['vender_con'] 		= $conf['vender_con'];
	///$cliente['tipo_conex'] 		= $conf['tipo_conex'];
	$BL_ACTUAL = $conf['balance'];
	$dist_id_func = $conf['funcion_id'];
	$dist_id_func2 = $conf['funcion_id2'];
	// ==========================
	// CALCULO PORC. EL QUE REALIZA REC.
	// ==========================
	$porciento = $cliente["seguro_porc" . $conf['cod_p'] . ""];
	$conf['monto'] = (int) $conf['monto'];
	$calcPorcNivelActual = $conf['monto'] * "0.$porciento"; //1975 * 080 = 158
	$COSTO = $conf['monto'] - $calcPorcNivelActual; //1975 - 158 = 1817
	// ==========================
	// CALCULO PORC. NIVEL 1
	// ==========================
	$n1 = mysql_query(
		"SELECT seguro_porc" .
			$conf['cod_p'] .
			" FROM personal WHERE id='6' LIMIT 1"
	);
	$n1D = mysql_fetch_array($n1);
	$porciento4 = $n1D['seguro_porc' . $conf['cod_p'] . ''];
	$calcPorcNivel1 = $conf['monto'] * "0.$porciento4";
	// ==========================
	// CALCULO PORC. NIVEL 5
	// ==========================
	if ($dist_id_func2 == '5') {
		//<--- La func del que pertenece.
		$uID5 = $conf["dist_id"];
	} elseif ($dist_id_func2 !== '6') {
		$uID5 = $conf["id_dist2"];
	}
	if ($uID5) {
		$descS = "seguro_porc" . $conf['cod_p'];
		$sql2 = mysql_query(
			"SELECT $descS FROM personal WHERE id='" . $uID5 . "' LIMIT 1"
		);
		$porcNew = mysql_fetch_array($sql2);
		$porc = $porcNew["$descS"];
		$calcPorcNivel5 = $conf['monto'] * "0.$porc";
		//echo "PorcNivel5: ".$calcPorcNivel5." | ";
	}
	// ==========================
	// CALCULO PORC. NIVEL 2
	// ==========================
	if ($dist_id_func2 == '2') {
		$uID2 = $conf["dist_id"];
		$n2 = mysql_query(
			"SELECT seguro_porc" .
				$conf['cod_p'] .
				",balance FROM personal 
		WHERE id='" .
				$uID2 .
				"' LIMIT 1"
		);
		$distD = mysql_fetch_array($n2);
		$porciento3 = $distD['seguro_porc' . $conf['cod_p'] . ''];
		$calcPorcNivel2 = $conf['monto'] * "0.$porciento3";
		//echo "PorcNivel2: ".$calcPorcNivel2." | ";
	}
	// ============================
	// MANEJO DE BALANCE COMPARTIDO [usar_bl_princ]
	// ============================
	if ($conf['usar_bl_princ'] == 'si') {
		$calcPorcNivelActual = 0;
		// Si es Nivel 5
		if ($dist_id_func2 == '5' && !$distD) {
			$n2 = mysql_query(
				"SELECT seguro_porc" .
					$conf['cod_p'] .
					",balance FROM personal 
			WHERE id='" .
					$conf["dist_id"] .
					"' LIMIT 1"
			);
			$distD = mysql_fetch_array($n2);
			$porciento3 = $distD['seguro_porc' . $conf['cod_p'] . ''];
			$calcPorcNivel2 = $conf['monto'] * "0.$porciento3";
			//echo "Bal.Princ Ninel(".$dist_id_func2."): ".$calcPorcNivel2." | ";
		}
		// Cambiamos balance y costo por el Dist.
		$BL_ACTUAL = $distD['balance'];
		$COSTO = $conf['monto'] - $calcPorcNivel2;
		// Verificando si se le paso la ganancia:
		// se le desconto el costo:
		if ($dist_id_func2 == '5') {
			$costo5descontado = true;
		} elseif ($dist_id_func2 == '2') {
			$costo2descontado = true;
		}
	}
	// ============================
	// VER. SI TIENE BALANCE
	if ($COSTO <= $BL_ACTUAL) {
		/*if($conf['cod_p'] ==1){
		// ========================
		// ===> SEGUROS 1
		$apiSeg = GetSeguroDeVida($conf3);	
	}else*/
		$conf['cod_p'] = '2';

		if ($conf['cod_p'] == 2) {
			$apiSeg[0] = "00";
		}
		if ($apiSeg[0] == '00') {
			// DESCONTAR COSTO AL CLIENTE.
			// ============================
			// MANEJO DE BALANCE COMPARTIDO [usar_bl_princ]
			if ($conf['usar_bl_princ'] !== 'si') {
				// propio balance
				mysql_query(
					"UPDATE personal SET balance =(balance - $COSTO) 
		WHERE id='" .
						$conf['user_id'] .
						"' LIMIT 1"
				);
			} else {
				// propio balance
				mysql_query(
					"UPDATE personal SET balance =(balance - $COSTO) 
		WHERE id='" .
						$conf["dist_id"] .
						"' LIMIT 1"
				);
			}
			// PROC. NIVEL 1
			// ===========================
			if ($conf["dist_id"] == '6' && $conf["funcion_id"] == '2') {
				// Nivel2-> Nivel1
				$GancNivel1 = $calcPorcNivel1 - $calcPorcNivelActual;
				//echo "1#";
			} elseif ($conf["id_dist2"] == '6' && $conf["funcion_id"] == '2') {
				// Nivel2-> Nivel5-> Nivel1
				$GancNivel1 = $calcPorcNivel1 - $calcPorcNivel5;
				//echo "1#";
			} elseif ($conf["id_dist2"] == '6' && $conf["funcion_id"] == '3') {
				//echo " Nivel3-> Nivel2-> Nivel1 | ";
				if ($dist_id_func2 == '2') {
					$GancNivel1 = $calcPorcNivel1 - $calcPorcNivel2;
				}
				// Nivel3-> Nivel5-> Nivel1
				else {
					$GancNivel1 = $calcPorcNivel1 - $calcPorcNivel5;
				}
			} elseif ($conf["id_dist2"] == '6' && $conf["funcion_id"] == '3') {
				// Nivel3-> Nivel2-> Nivel1
				$GancNivel1 = $calcPorcNivel1 - $calcPorcNivel2;
			} elseif ($conf["id_dist2"] !== '6' && $conf["funcion_id"] == '3') {
				// Nivel3-> Nivel2-> Nivel5-> Nivel1
				$GancNivel1 = $calcPorcNivel1 - $calcPorcNivel5;
			}
			// ===========================
			// PROC. NIVEL 5
			// ===========================
			if ($conf["id_dist2"] == '6' && $conf["funcion_id"] == '2') {
				// Nivel2-> Nivel5-> Nivel1
				$GancNivel5 = $calcPorcNivel5 - $calcPorcNivelActual;
			} elseif (
				$conf["id_dist2"] == '6' &&
				$conf["funcion_id"] == '3' &&
				$dist_id_func2 == '2'
			) {
				// Nivel3-> Nivel2-> Nivel1
				$GancNivel5 = $calcPorcNivel5 - $calcPorcNivel2;
			} elseif (
				$conf["id_dist2"] == '6' &&
				$conf["funcion_id"] == '3' &&
				$dist_id_func2 == '5'
			) {
				//echo " Nivel3-> Nivel5-> Nivel1<br>";
				$GancNivel5 = $calcPorcNivel5 - $calcPorcNivelActual;
			} elseif (
				$conf["id_dist2"] !== '6' &&
				$conf["funcion_id"] == '3' &&
				$dist_id_func2 == '2'
			) {
				// Nivel3-> Nivel2-> Nivel5-> Nivel1
				$GancNivel5 = $calcPorcNivel5 - $calcPorcNivel2;
				//echo "Ganc Niv2".
			} else {
				$GancNivel5 = 0;
			}
			// ===========================
			// PROC. NIVEL 2
			if ($conf["funcion_id"] == '2') {
				// Nivel2-> Nivel1
				$GancNivel2 = $calcPorcNivelActual;
			} elseif ($conf["funcion_id"] == '3') {
				// Nivel3-> Nivel2-> Nivel5-> Nivel1
				if ($dist_id_func2 == '2') {
					$GancNivel2 = $calcPorcNivel2 - $calcPorcNivelActual;
					//echo "Gan Nivel2: ".$GancNivel2."<br>";
				} else {
					$GancNivel2 = 0;
				}
			}
			// ================================
			// PROCESO de MOVIMIENTO de BALANCE
			// Nivel 2
			if ($GancNivel2 && !$costo2descontado) {
				error_log("here");
				mysql_query(
					"UPDATE personal SET balance =(balance + $GancNivel2) 
			WHERE id='" .
						$uID2 .
						"' LIMIT 1"
				);
			}
			// Nivel 5
			if ($GancNivel5 && !$costo5descontado) {
				mysql_query(
					"UPDATE personal SET balance =(balance + $GancNivel5) 
			WHERE id='" .
						$uID5 .
						"' LIMIT 1"
				);
			}
			// =================================
			// RECOJEMOS DATOS DEL SeguroO
			// se guarda en tabla: seguros_transacciones
			if ($_REQUEST['id_aseg'] == '1') {
				$id_poliz = GetTransID_DOM($conf['user_id']);
			} elseif ($_REQUEST['id_aseg'] == '4') {
				$id_poliz = GetTransID_ATRIO($conf['user_id']);
			} elseif ($_REQUEST['id_aseg'] == '5') {
				$id_poliz = GetTransID_sura($conf['user_id']);
			} elseif ($_REQUEST['id_aseg'] == '7') {
				$id_poliz = GetTransID_sura_p($conf['user_id']);
			} elseif ($_REQUEST['id_aseg'] == '8') {
				$id_poliz = GetTransID_one_alliance_p($conf['user_id']);
			} elseif ($_REQUEST['id_aseg'] == '6') {
				$id_poliz = GetTransID_ATRIO($conf['user_id']);
			}

			$conf['id_aseg'] = $_REQUEST['id_aseg'];
			$conf['id_plan'] = $_REQUEST['id_plan'];
			$conf['id_poliza'] = $id_poliz;
			$conf['totalpagar'] = $COSTO;
			$conf['descuento2'] = $porciento;
			$conf['ganancia'] = $calcPorcNivelActual;
			$conf['ganancia2'] = $GancNivel2;
			$conf['ganancia1'] = $GancNivel1;
			$conf['ganancia5'] = $GancNivel5;
			$_REQUEST['id_poliza_cliente'] = $conf['id_poliza'];

			// GUARDAMOS DATOS DEl Seguro.
			$NoTransaccion = RegTransaccion($conf);
			$NumTransac = mysql_insert_id();
			// GUARDAMOS EN LOG
			if ($conf && LOGS) {
				$conf['provresp'] = $apiSeg[1];
				SaveLogs2(array(
					'tipo' => 'SEGURO:' . $apiSeg[0],
					'errors' => $errors,
					'datos' => $conf
				));
			}

			if ($NoTransaccion) {
				sendSMS($NoTransaccion);
			}
			error_log("here2");
			return array('00', $NoTransaccion);
		} else {
			// GUARDAMOS EN LOG
			if ($conf && LOGS) {
				$conf['provresp'] = $apiSeg[1];
				SaveLogs2(array(
					'tipo' => 'SEGURO:' . $apiSeg[0],
					'errors' => $errors,
					'datos' => $conf
				));
			}
			return array($apiSeg[0], 0);
		} // END --> TERMINAMOS INTERPRETE DE ERRORES.
	} else {
		// GUARDAMOS EN LOG
		if ($conf && LOGS) {
			$conf['descrip'] = 'No bal. disponible';
			SaveLogs2(array(
				'tipo' => 'SEGURO:11' . $apiSeg[0],
				'errors' => $errors[0],
				'datos' => $conf
			));
		}
		return array("11", 0);
	}
}

function ProcesaSeguro($xID, $usuario, $password, $cod_p, $monto)
{
	global $conf, $dir2;

	// =======================
	// INFO DEL QUE EFECTUA EL Seguro
	// =======================
	$rs2 = mysql_query(
		"SELECT 
		   id,balance, seguro_porc" .
			$cod_p .
			",tipo_conex,id_dist,funcion_id,
		    usar_bl_princ
		   FROM personal WHERE 
		  
		   (
   	id = '" .
			mysql_real_escape_string($usuario) .
			"' OR 
	user = '" .
			mysql_real_escape_string($usuario) .
			"') 
		   AND password='" .
			$password .
			"' 
		   AND activo !='no' LIMIT 1"
	);
	$numU = mysql_num_rows($rs2);

	//echo mysql_error();

	if ($numU == '1') {
		$p = mysql_fetch_array($rs2);

		// BUSCAMOS EL SUPERDIST.
		$sql = mysql_query(
			"SELECT id_dist,funcion_id FROM personal WHERE id='" .
				$p['id_dist'] .
				"'  LIMIT 1"
		);
		$dist_id2 = mysql_fetch_array($sql);

		$conf2 = array(
			'user_id' => $p['id'],
			'balance' => $p['balance'],
			'seguro_porc' . $cod_p . '' => $p['seguro_porc' . $cod_p . ''],
			//'vender_con'				=>$p['vender_con'],
			'usar_bl_princ' => $p['usar_bl_princ'], //<-- NUEVO EN ws4.1-beta
			'tipo_conex' => $p['tipo_conex'],
			'dist_id' => $p['id_dist'],
			'funcion_id' => $p['funcion_id'],
			'funcion_id2' => $dist_id2['funcion_id'],
			'id_dist2' => $dist_id2['id_dist'],
			'monto' => $monto,
			//'num_tele'				=>str_replace('-',"",$num_tel),
			'cod_p' => $cod_p,
			'x_id' => $xID,
			'NoTransac' => $NoTransac,
			//'via'					=>$_GET['via'],
			'fecha' => date('YmdHis')
		);

		$cod_resp = SolicitarSeguro($conf2);
	} else {
		// ERROR AUTENTIFICANDO USUARIO
		$cod_resp = array('14', 0);
	}

	// VERIFICAMOS SI LA TRANS. SE COMPLETO Y ENV. NO. TRANS
	//if($cod_resp[0] =='00'){
	//		//$conf2['NoTransac1'] = UltTransaccion();
	//	}else {
	//		$conf2['NoTransac1'] ='0';
	//	}

	// ENVIAMOS LA RESPUESTA AL CLIENTE
	$respuesta = array(
		'Codigo_Resp' => $cod_resp[0],
		'Desc_Respuesta' => $conf["Cod_resp"][$cod_resp[0]],
		'No_Transaccion' => $cod_resp[1]
	);

	$pref = GetPrefijo($_REQUEST['id_aseg']);
	$id_poliza_cliente = str_pad(
		$_REQUEST['id_poliza_cliente'],
		6,
		"0",
		STR_PAD_LEFT
	);
	return $cod_resp[0] .
		'/' .
		$conf["Cod_resp"][$cod_resp[0]] .
		'/' .
		$cod_resp[1] .
		'/' .
		$conf2['fecha'] .
		'/' .
		$pref .
		'-' .
		$id_poliza_cliente;
}

// ANTI-SQL INYECTION
// $param1 = SanidarParametros($param);
$xID = $_REQUEST['xID'];
$usuario = $_REQUEST['usuario'];
$password = $_REQUEST['password'];
$cod_p = $_REQUEST['prod'];

define('VIGENCIA', $_REQUEST['vigencia_poliza']);
define('VEH_TIPO', $_REQUEST['veh_tipo']);

/*if($cod_p ==1)
		$monto 		= IfMontoValido($cod_p) * VIGENCIA;
	else*/
/*$monto 		= IfMontoValido($cod_p);
	
	//BUSCAR CANTAIDAD DE LOS SERVICIOS ADICIONALES
	$porciones = explode("-", $_REQUEST['serv_adc']);
	
	for($i =0; $i < count($porciones); $i++){
		$_REQUEST['serv'] .= "".$porciones[$i]."-";
		$MontoServicio += MontoServicio($porciones[$i],VIGENCIA);
	}
	
		//$monto 	= $MontoServicio + $monto;
		$monto 		= $monto;*/

//CANTIDAD DEL MONTO DEL SEGURO POR EL TIPO DE VEHICULO Y LA VIGENCIA
$MontoTarifa = IfMontoTarifas(
	$_REQUEST['veh_tipo'],
	$_REQUEST['vigencia_poliza']
);

//BUSCAR CANTIDAD DE LOS SERVICIOS ADICIONALES
$porciones = explode("-", $_REQUEST['serv_adc']);

for ($i = 0; $i < count($porciones); $i++) {
	$serv_adc = ValidarServicio($_REQUEST['veh_tipo'], $porciones[$i]);
	$MServicio += MontoServicio($serv_adc, $_REQUEST['vigencia_poliza']);

	if ($serv_adc) {
		$_REQUEST['serv'] .= "" . $serv_adc . "-";
	}
}

$monto = $MServicio + $MontoTarifa;

// solo Seguro de Vida:
define('C_NOMBRE', $_REQUEST['asegurado_nombres']);
define('C_APELLIDO', $_REQUEST['asegurado_apellidos']);
define('C_CEDULA', $_REQUEST['asegurado_cedula']);
define('ID_CLIENTE', $_GET['id_cliente']);
define('VIG_POLIZA', $_REQUEST['vigencia_poliza']);
define('PRODUCTO', $_REQUEST['prod']);

if (!$_REQUEST['fecha_inicio']) {
	$_REQUEST['fecha_inicio'] = date("Y-m-d H:i:s");
} else {
	$_REQUEST['fecha_inicio'] = str_replace("+", "", $_REQUEST['fecha_inicio']);
}

// registrar con fecha despues:
//$_REQUEST['fecha_inicio'] = fecha_despues2(''.$_REQUEST['fecha_inicio'].'',1);

//define('ASEGURADORA',$_REQUEST['id_aseg']);
define('FECHA_INICIO', $_REQUEST['fecha_inicio']);
define('ID_VEHICULO', $_GET['id_vehiculo']);
define('SERV_ADC', $_REQUEST['serv']);
define('FECHA_FIN', Fecha_Fin_Mes(FECHA_INICIO, VIG_POLIZA));
//define('C_TELEFONO',$param1[12]);

if ($monto == 0) {
	exit('16/Producto no disponible/0/0');
}

$print = ProcesaSeguro($xID, $usuario, $password, $cod_p, $monto);
exit($print);
