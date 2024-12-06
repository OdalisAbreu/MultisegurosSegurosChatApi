<?
//exit("Seguro no procesado");
ini_set('display_errors', 0);
include("inc/conexion_inc.php");
include('inc/validador.php');
include('../inc/AntiInyection.func.php');
include('../inc/auditoria.balance.func.php');
include('../controller/Records.php');
Conectarse();



$_POST['plan'] = '1';

if ($_REQUEST['idApi'] == '2wessd@d3e') {

	//echo "<br>";

	//VALIDAR FECHA Y REORGANIZARLA
	$f1 = explode("/", $_REQUEST['fecha_inicio']);
	$_POST['xID'] = $_REQUEST['xID'];
	$_POST['clave'] = $_REQUEST['clave'];
	$_POST['usuario'] = $_REQUEST['usuario'];
	$_POST['idApi'] = $_REQUEST['idApi'];

	$_POST['tipo'] = $_REQUEST['tipo'];
	$_POST['marca'] = $_REQUEST['marca'];
	$_POST['modelo'] = $_REQUEST['modelo'];
	$_POST['year'] = $_REQUEST['year'];
	$_POST['chassis'] = $_REQUEST['chassis'];
	$_POST['placa'] = $_REQUEST['placa'];

	$_POST['nombres'] = $_REQUEST['nombres'];
	$_POST['apellidos'] = $_REQUEST['apellidos'];
	$_POST['cedula'] = $_REQUEST['cedula'];
	$_POST['pasaporte'] = $_REQUEST['pasaporte'];
	$_POST['telefono1'] = $_REQUEST['telefono1'];
	$_POST['email'] = $_REQUEST['email'];
	$_POST['direccion'] = $_REQUEST['direccion'];
	$_POST['ciudad'] = $_REQUEST['ciudad'];

	$_POST['aseguradora'] = $_REQUEST['aseguradora'];
	$_POST['vigencia_poliza'] = $_REQUEST['vigencia_poliza'];
	//$_POST['producto'] 			= $_REQUEST['producto'];
	$_POST['serv_adc'] = $_REQUEST['serv_adc'];
	if (!empty($f1)) {
		$_POST['fecha_inicio'] = $f1[2]."-".$f1[1]."-".$f1[0];
	} else {
		$_POST['fecha_inicio'] = $_REQUEST['fecha_inicio'];
	}
	//exit("ENTRO AQUI");
}


//echo $_REQUEST['vigencia_poliza'];


if ($_POST) {
	$hoy = date("Ymd");
	$Choy = str_replace("-", "", $_POST['fecha_inicio']);

	if ($Choy < $hoy) {

		//$_POST['fecha_inicio'] = date("Y-m-d");
		Auditoria(
			$_POST['usuario'],
			$_POST['clave'],
			'',
			"Error en fecha, fecha no identificada, usted envio REQUEST:" . $_POST['fecha_inicio'] . ", HOY:$hoy, CHOY:$Choy, PV: " . $_POST['xID'],
			'venta_error',
			'17',
			'',
			'',
			json_encode($_POST)
		);
	} elseif ($Choy > $hoy) {

		$_POST['fecha_inicio'] = $_POST['fecha_inicio'];
	}

	// Validar el Vehiculo 
	$model = validateModel($_POST['marca'], $_POST['modelo'], $_POST['tipo']);
	if($model != 'Ok'){
		exit("41 /".$model."/00 ");
	}
	//Validar Placa
	$validarPlaca = validatePlaca($_REQUEST['placa'], $_REQUEST['tipo'] );
	if($validarPlaca != 'Ok'){
		exit("42 /".$validarPlaca."/00 ");
	}
	//echo "v: ".$_POST['vigencia_poliza'];
	if (IfVigencia($_REQUEST['vigencia_poliza']) == '15') {

		Auditoria($_POST['usuario'], $_POST['clave'], '', 'Error en parametros, vigencia no identificada, usted envio  ' . $_POST['vigencia_poliza'] . '', 'venta_error', '22', '', '');
		exit("22/Vigencia no permitida/00");
	}


	if (IfSeguroActivo($_REQUEST['aseguradora']) != '00') {

		Auditoria($_POST['usuario'], $_POST['clave'], '', 'Error en parametros, aseguradora no identificada, usted envio  el id de la aseguradora: ' . $_POST['aseguradora'] . ' que esta  inabilitada', 'venta_error', '21', '', '');
		exit("21/Aseguradora inactiva/00");
	}


	$var = array("insert", "INSERT", "delete", "DELETE", "update", "UPDATE", "select", "SELECT", "-", "/", "'", "&", "#");
	$_POST['cedula'] 	= str_replace($var, "", $_POST['cedula']);
	$_POST['placa'] 		= str_replace($var, "", $_POST['placa']);
	$_POST['chassis'] 	= str_replace($var, "", $_POST['chassis']);
	$_POST['telefono1'] 	= str_replace($var, "", $_POST['telefono1']);
	$_POST['nombres'] 	= str_replace($var, "", $_POST['nombres']);
	$_POST['apellidos'] 	= str_replace($var, "", $_POST['apellidos']);
	$_POST['direccion'] 	= str_replace($var, "", $_POST['direccion']);
	$_POST['prod'] 		= '2';

	//QUITAR ESTO DESPUES DE QUE INICIE EL PROCESO
	//if($_POST['tes']=='Y84p84jm1'){

	//$_POST['aseguradora'] = "1";


	$rs2 = mysql_query("
	SELECT id,activo,user,password,balance,tipo_conex FROM personal WHERE 
	(
   	id = '" . mysql_real_escape_string($_REQUEST['usuario']) . "' OR 
	user = '" . mysql_real_escape_string($_REQUEST['usuario']) . "') AND password='" . $_REQUEST['clave'] . "' 
	LIMIT 1");

	$numU = mysql_num_rows($rs2);

	if ($numU == '1') {

		$user = mysql_fetch_array($rs2);

		if ($user['activo'] == 'si') {

			$_POST['user_id'] = $user['id'];
		} else {

			Auditoria($user['user'], $user['password'], $user['tipo_conex'], 'Usuario inactivo', 'venta_error', '18', '', $user['balance']);

			exit('18/Usuario inactivo/00');
		}
	} else {

		Auditoria($_POST['usuario'], $_POST['clave'], '', 'Usuario y/o Clave incorrectos', 'venta_error', '14', '', '');
		exit('14/Usuario o Clave incorrectos/00');
	}


	/*PARA REGISTRAR DATOS DEL USUARIO*/
	mysql_query(
		"INSERT INTO seguro_clientes 
		(user_id, asegurado_nombres, asegurado_apellidos, asegurado_cedula, asegurado_pasaporte,
		asegurado_direccion, asegurado_telefono1, asegurado_email, ciudad, cliente_registro) 
		VALUES 
		('" . $_POST['user_id'] . "',
		'" . $_POST['nombres'] . "',
		'" . $_POST['apellidos'] . "',
		'" . $_POST['cedula'] . "',
		'" . $_POST['pasaporte'] . "',
		'" . $_POST['direccion'] . "',
		'" . $_POST['telefono1'] . "',
		'" . $_POST['email'] . "',
		'" . $_POST['ciudad'] . "',
		'" . date("Y-m-d H:i:s") . "'
		
		)"
	);

	//echo mysql_error();
	$id_cliente = mysql_insert_id();

	/*PARA REGISTRAR DATOS DEL VEHICULO*/
	mysql_query(
		"INSERT INTO seguro_vehiculo (veh_marca,veh_modelo,id_cliente,user_id,veh_ano,veh_chassis,veh_tipo,veh_matricula,veh_registro) 
		VALUES 
		('" . $_POST['marca'] . "',
		'" . $_POST['modelo'] . "',
		'" . $id_cliente . "',
		'" . $_POST['user_id'] . "',
		'" . $_POST['year'] . "',
		'" . $_POST['chassis'] . "',
		'" . $_POST['tipo'] . "',
		'" . $_POST['placa'] . "',
		'" . date("Y-m-d H:i:s") . "'
		)"
	);

	//echo mysql_error();
	$id_vehiculo = mysql_insert_id();



	/*$sql 	= mysql_query(
	"SELECT user,password,balance,tipo_conex FROM personal WHERE id='".$_POST['user_id']."' LIMIT 1");
	$user 	= mysql_fetch_array($sql);*/

	//PARA UTILIZAR LA ASEGURADORA
	$POSTaseguradora = $_POST['aseguradora'];

	//$xID 	= "WEB-".$_POST['user_id'].date('Ymdhis');
	$url = "https://multiseguros.com.do/ws_dev/Seguros/GET_Seguro.php" .
		"?usuario=" . trim($_POST['usuario']) .
		"&xID=" . $_POST['xID'] .
		"&password=" . trim($_POST['clave']) .
		"&asegurado_nombres=" . str_replace(' ', '+', $_POST['nombres']) .
		"&asegurado_apellidos=" . str_replace(' ', '+', $_POST['apellidos']) .
		"&asegurado_cedula=" . $_POST['cedula'] .
		"&asegurado_pasaporte=" . $_POST['pasaporte'] .
		"&asegurado_direccion=" . $_POST['direccion'] .
		"&asegurado_telefono1=" . trim($_POST['telefono1']) .
		"&veh_tipo=" . trim($_POST['tipo']) .
		"&veh_ano=" . trim($_POST['year']) .
		"&veh_marca=" . trim($_POST['marca']) .
		"&veh_modelo=" . trim($_POST['modelo']) .
		"&veh_chassis=" . trim($_POST['chassis']) .
		"&vigencia_poliza=" . trim($_POST['vigencia_poliza']) .
		"&veh_matricula=" . trim($_POST['placa']) .
		"&serv_adc=" . trim($_POST['serv_adc']) .
		"&fecha_inicio=" . str_replace(' ', '+', $_POST['fecha_inicio']) .
		"&id_vehiculo=" . $id_vehiculo .
		"&id_cliente=" . $id_cliente .
		"&prod=" . $_POST['prod'] .
		"&id_aseg=" . $_POST['aseguradora'] .
		"&id_plan=" . $_POST['plan'] . "";
	$url = str_replace(" ", "+", $url);

	$getWS 	= file_get_contents($url);

	/*if($_SESSION['user_id']=='13'){
			print_r( $url);
		}*/
	//echo $url;

	$respuesta = explode("/", $getWS);

	// RESPUESTA RECARGA ENVIADA.gg
	if ($respuesta[0] == '00') {
		//RETORNARLE AL PROGRAMADOR
		Auditoria($user['user'], $user['password'], $user['tipo_conex'], 'Seguro Procesado Correctamente ID:' . $respuesta[2] . '', 'venta_ok', '00', '', $user['balance']);


		//PARA GUARDAR EL HISTORIAL DE MONTO AL MOMENTO DE VENDER
		function VehiculoHistory($id)
		{
			$query = mysql_query("
			SELECT * FROM  seguro_vehiculo
			WHERE id='" . $id . "' LIMIT 1");
			$row = mysql_fetch_array($query);
			return $row['veh_tipo'];
		}


		function MontoServicioHistory($id, $vigencia)
		{
			$r6 = mysql_query("SELECT id, 3meses, 6meses, 12meses FROM servicios WHERE id='" . $id . "'LIMIT 1");
			if ($id > 0) {
				while ($row6 = mysql_fetch_array($r6)) {
					if ($vigencia == 3)  return $row6['3meses'];
					if ($vigencia == 6)  return $row6['6meses'];
					if ($vigencia == 12) return $row6['12meses'];
				}
			}
		}

		function CostoServicioHistory($id, $vigencia)
		{
			$r6 = mysql_query("SELECT * FROM servicios WHERE id='" . $id . "'LIMIT 1");
			if ($id > 0) {
				while ($row6 = mysql_fetch_array($r6)) {
					if ($vigencia == 3)  return $row6['3meses_costos'];
					if ($vigencia == 6)  return $row6['6meses_costos'];
					if ($vigencia == 12) return $row6['12meses_costos'];
				}
			}
		}


		function IfMontoTarifasHistory($veh_tipo, $vigencia)
		{
			$queryT = mysql_query("
		   SELECT id,veh_tipo,3meses,6meses,12meses 
		   FROM seguro_tarifas 
		   WHERE veh_tipo ='" . $veh_tipo . "'  LIMIT 1");
			$rowT = mysql_fetch_array($queryT);

			if ($vigencia == 3)  return $rowT['3meses'];
			if ($vigencia == 6)  return $rowT['6meses'];
			if ($vigencia == 12) return $rowT['12meses'];
		}


		function IfMontoCostoTarifasHistory($veh_tipo, $vigencia, $idAseg)
		{
			$queryT = mysql_query("
		   SELECT id,veh_tipo,3meses,6meses,12meses 
		   FROM seguro_costos 
		   WHERE veh_tipo ='" . $veh_tipo . "' AND id_seg = '$idAseg'  LIMIT 1");
			$rowT = mysql_fetch_array($queryT);

			if ($vigencia == 3)  return $rowT['3meses'];
			if ($vigencia == 6)  return $rowT['6meses'];
			if ($vigencia == 12) return $rowT['12meses'];
		}


		if ($respuesta[2]) {
			//==================================================//
			$query = mysql_query("SELECT * FROM  seguro_transacciones WHERE id='" . $respuesta[2] . "' LIMIT 1");
			//echo "<br><b>CONSULTA</B> SELECT * FROM  seguro_transacciones WHERE $wFecha2 <br>";
			while ($row = mysql_fetch_array($query)) {

				$Vtipo = VehiculoHistory($row['id_vehiculo']);
				//$Veh = explode("|", VehiculoHistory($row['id_vehiculo']));
				$monto = IfMontoTarifasHistory($Vtipo, $row['vigencia_poliza']);
				$costo = IfMontoCostoTarifasHistory($Vtipo, $row['vigencia_poliza'], $row["id_aseg"]);
				/*echo "<br><br><b>TRANSACION</b><br>";
	echo  "ID trans: ".$row['id']."<br>";
	echo  "monto seguro: ".$monto."<br>";
	echo  "fecha: ".$row['fecha']."<br>";
	*/
				/*PARA REGISTRAR DATOS DEL USUARIO*/
				mysql_query(
					"INSERT INTO seguro_trans_history 
		(id_trans, tipo, id_aseg,  id_serv_adc, monto, costo, fecha) 
		VALUES 
		('" . $row['id'] . "',
		'seg',
		'" . $POSTaseguradora . "',
		'0',
		'" . $monto . "',
		'" . $costo . "',
		'" . $row['fecha'] . "'
		)"
				);


				//echo "<br><b>SERVICIOS</b><br>";
				//echo  "ID trans: ".$row['id']."<br>";
				//echo  "serv_adc: ".$row['serv_adc']."<br>";

				$porciones = explode("-", $row['serv_adc']);

				for ($i = 0; $i < count($porciones); $i++) {

					if ($porciones[$i] > 0) {

						$MontoServ = MontoServicioHistory($porciones[$i], $row['vigencia_poliza']);
						$CostoServ = CostoServicioHistory($porciones[$i], $row['vigencia_poliza']);
						//echo "#".$porciones[$i]." - ".$MontoServ."<br>";
						//echo  "fecha: ".$row['fecha']."<br>";

						/*PARA REGISTRAR DATOS DEL USUARIO*/
						mysql_query(
							"INSERT INTO seguro_trans_history 
		(id_trans, tipo, id_aseg,  id_serv_adc, monto, costo, fecha)   
		VALUES 
		('" . $row['id'] . "',
		'serv',
		'" . $POSTaseguradora . "',
		'" . $porciones[$i] . "',
		'" . $MontoServ . "',
		'" . $CostoServ . "',
		'" . $row['fecha'] . "'
		)"
						);
					}
				}

				//echo "<br>=============== TERMINO =================<br>";


			}
			//==================================================//

		}

		//PARA GUARDAR EL HISTORIAL DE MONTO AL MOMENTO DE VENDER



		exit('00/' . $respuesta[1] . '/' . $respuesta[2] . '/' . $respuesta[3] . '/' . $respuesta[4] . '');
		// RETORNARLE AL PROGRAMADOR
	} elseif ($respuesta[0] == '11') {

		Auditoria($user['user'], $user['password'], $user['tipo_conex'], 'No tiene balance disponible', 'venta', '12', '', $user['balance']);
		echo '12/No tiene balance disponible/00';
	} elseif ($respuesta[0] !== '00') {

		Auditoria($user['user'], $user['password'], $user['tipo_conex'], 'Error Procesando Seguro', 'venta_error', '15', '', $user['balance']);
		echo '15/Error Procesando Seguro/00';
	}



	//aqui





	//QUITAR ESTO DESPUES DE QUE INICIE EL PROCESO
} else {
	Auditoria($_POST['usuario'], $_POST['clave'], '', 'Acceso no permitido - Metodo: ' . $_SERVER['REQUEST_METHOD'] . " Cedula: " . $_POST['cedula'], 'venta_error', '19', '', '');
	exit('ACCESO NO PERMITIDO');
}
