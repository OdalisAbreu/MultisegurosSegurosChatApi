<?php

function SanidarParametros($param)
{
  // ANTI-SQL INYECTION
  $param1 = explode('/', $param);
  return $param1;
}

function ValidarServicio($veh_tipo, $serv_adc)
{
  $rServ = mysql_query(
    "SELECT * FROM seguro_tarifas WHERE veh_tipo='" .
      $veh_tipo .
      "'
		AND id_serv LIKE '%" .
      $serv_adc .
      "%'"
  );

  $rowServ = mysql_fetch_array($rServ);

  if ($rowServ['id']) {
    return $serv_adc;
  }
}

function MontoServicio($id, $vigencia)
{
  $r6 = mysql_query(
    "SELECT id, 3meses, 6meses, 12meses FROM servicios WHERE id='" .
      $id .
      "'LIMIT 1"
  );
  if ($id > 0) {
    while ($row6 = mysql_fetch_array($r6)) {
      if ($vigencia == 3) {
        return $row6['3meses'];
      }
      if ($vigencia == 6) {
        return $row6['6meses'];
      }
      if ($vigencia == 12) {
        return $row6['12meses'];
      }
    }
  }
}

function IfMontoTarifas($veh_tipo, $vigencia)
{
  $queryT = mysql_query(
    "
		   SELECT id,veh_tipo,3meses,6meses,12meses 
		   FROM seguro_tarifas 
		   WHERE veh_tipo ='" .
      $veh_tipo .
      "'  LIMIT 1"
  );
  $rowT = mysql_fetch_array($queryT);

  if ($vigencia == 3) {
    return $rowT['3meses'];
  }
  if ($vigencia == 6) {
    return $rowT['6meses'];
  }
  if ($vigencia == 12) {
    return $rowT['12meses'];
  }
}

/*function IfMontoValido($cod_p){
	
		 
		 $veh_tipo = VEH_TIPO;
		 
		 $query=mysql_query("
   SELECT id,veh_tipo,3meses,6meses,12meses 
   FROM seguro_tarifas 
   WHERE veh_tipo ='".$veh_tipo."'");

  while($row=mysql_fetch_array($query)){
	  
	  if(VIGENCIA ==3)  return $row['3meses'];
	  if(VIGENCIA ==6)  return $row['6meses'];
	  if(VIGENCIA ==12) return $row['12meses']; 
	  
	  }
		
	}*/

function getAgencia($idTrans)
{
  $query = sprintf(
    "SELECT 
			 IF(agencia_via.razon_social IS NULL,
        IF(vendedor_multiseguros.nombres IS NULL,
            vendedor_pagosmultiples.nombres,
            vendedor_multiseguros.nombres),
        CONCAT(agencia_via.num_agencia,
                ' - ',
                agencia_via.razon_social)) Vendedor,
			IF(distribuidor_via.nombres IS NULL,
				IF(distribuidor_multiseguros.nombres IS NULL,
					vendedor_pagosmultiples.nombres,
					distribuidor_multiseguros.nombres),
				distribuidor_via.nombres) Distribuidor
		FROM
			seguro_transacciones trans
				LEFT JOIN
			agencia_via ON agencia_via.num_agencia = SUBSTRING_INDEX(trans.x_id, '-', 1)
				AND SUBSTRING_INDEX(trans.x_id, '-', 1) != 'WEB'
				LEFT JOIN
			personal distribuidor_via ON agencia_via.user_id = distribuidor_via.id
				LEFT JOIN
			personal vendedor_multiseguros ON vendedor_multiseguros.id = trans.user_id
				AND SUBSTRING_INDEX(trans.x_id, '-', 1) = 'WEB'
				LEFT JOIN
			personal distribuidor_multiseguros ON distribuidor_multiseguros.funcion_id = 2
				AND vendedor_multiseguros.id_dist = distribuidor_multiseguros.id
				LEFT JOIN
			personal vendedor_pagosmultiples ON vendedor_pagosmultiples.id = trans.user_id
				AND SUBSTRING_INDEX(trans.x_id, '-', 1) = '86'
		WHERE
			trans.id = %d
		",
    $idTrans
  );

  $result = mysql_query($query);
  $data = mysql_fetch_array($result);

  $agencia = array(
    'vendedor' => $data["Vendedor"],
    'distribuidor' => $data["Distribuidor"]
  );
  return $agencia;
}

function FechaListPDFn($id)
{
  $clear1 = explode(' ', $id);
  $fecha_vigente1 = explode('-', $clear1[0]);
  return $fecha_vigente1[2] .
    '-' .
    $fecha_vigente1[1] .
    '-' .
    $fecha_vigente1[0];
}

function FechaListPDFin($id)
{
  $clear1 = explode(' ', $id);
  $f = explode('-', $clear1[0]);
  $fh = explode(':', $clear1[1]);

  if ($fh[0] == '00') {
    $hora = '12';
  }
  if ($fh[0] == '01') {
    $hora = '1';
  }
  if ($fh[0] == '02') {
    $hora = '2';
  }
  if ($fh[0] == '03') {
    $hora = '3';
  }
  if ($fh[0] == '04') {
    $hora = '4';
  }
  if ($fh[0] == '05') {
    $hora = '5';
  }
  if ($fh[0] == '06') {
    $hora = '6';
  }
  if ($fh[0] == '07') {
    $hora = '7';
  }
  if ($fh[0] == '08') {
    $hora = '8';
  }
  if ($fh[0] == '09') {
    $hora = '9';
  }
  if ($fh[0] == '10') {
    $hora = '10';
  }
  if ($fh[0] == '11') {
    $hora = '11';
  }
  if ($fh[0] == '12') {
    $hora = '12';
  }
  if ($fh[0] == '13') {
    $hora = '1';
  }
  if ($fh[0] == '14') {
    $hora = '2';
  }
  if ($fh[0] == '15') {
    $hora = '3';
  }
  if ($fh[0] == '16') {
    $hora = '4';
  }
  if ($fh[0] == '17') {
    $hora = '5';
  }
  if ($fh[0] == '18') {
    $hora = '6';
  }
  if ($fh[0] == '19') {
    $hora = '7';
  }
  if ($fh[0] == '20') {
    $hora = '8';
  }
  if ($fh[0] == '21') {
    $hora = '9';
  }
  if ($fh[0] == '22') {
    $hora = '10';
  }
  if ($fh[0] == '23') {
    $hora = '11';
  }

  return $f[2] . '-' . $f[1] . '-' . $f[0] . " 12:00 PM";
}

function getTelefonoCliente($idTrans)
{
  $query = sprintf(
    "SELECT 
		asegurado_telefono1
	FROM
		seguro_clientes
			INNER JOIN
		seguro_transacciones ON seguro_transacciones.id_cliente = seguro_clientes.id
	WHERE
		seguro_transacciones.id =  %d
		",
    $idTrans
  );

  $result = mysql_query($query);
  $data = mysql_fetch_array($result);

  $telefono = $data["asegurado_telefono1"];
  return $telefono;
}

function GetPrefijo2($id)
{
  $queryp = mysql_query(
    "SELECT * FROM  seguros WHERE id='" . $id . "' LIMIT 1"
  );
  $rowp = mysql_fetch_array($queryp);
  return $rowp['prefijo'];
}

function sendSMS($idTrans)
{
  $query = mysql_query(
    "SELECT * FROM seguro_transacciones   
	WHERE id ='" .
      $idTrans .
      "' LIMIT 1"
  );

  $row = mysql_fetch_array($query);

  $laAgencia = getAgencia($row["id"]);
  $poliza =
    GetPrefijo2($row['id_aseg']) .
    '-' .
    str_pad($row['id_poliza'], 6, "0", STR_PAD_LEFT);

  $mensajeSMS =
    "MultiSeguros-Gracias por comprar su Seguro de Ley en " .
    $laAgencia["vendedor"] .
    ". No. Poliza " .
    $poliza .
    " Vigencia del " .
    FechaListPDFn($row['fecha_inicio']) .
    " al " .
    FechaListPDFin($row['fecha_fin']) .
    ". Su MARBETE-> ";

  $linkPdfPoliza =
    "https://multiseguros.com.do/ws_dev/TareasProg/GenerarReporteAseguradoraPdfUnico.php?sms=0&id_trans=" .
    $row["id"];

  $urlSMS = "https://apismsi.aldeamo.com/SmsiWS/smsSendPost/";

  // $data = array(
  // 	"country" => "1",
  // 	"message" => $mensajeSMS,
  // 	"addresseeList" => array(
  // 		"mobile" => "8293805036",
  // 		"url" => $linkPdfPoliza
  // 	)
  // );

  $celCliente = getTelefonoCliente($row["id"]);

  $data =
    '{"country": "1","message":"' .
    $mensajeSMS .
    '","addresseeList": [{"mobile":"' .
    $celCliente .
    '","url":"' .
    $linkPdfPoliza .
    '"}]}';
  error_log("SMS");
  error_log($data);
  $smsRespuesta = httpPost($urlSMS, $data, "jgrullon", "jgrullon2021*");
  error_log($smsRespuesta);
  Auditoria_Interna('', '', '', $data, 'SMS LOG', '00', '', '', $smsRespuesta);
}

function httpPost($url, $data, $username, $password)
{
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => array(
      "Content-Type: application/json",
      "cache-control: no-cache"
    ),
    CURLOPT_USERPWD => $username . ":" . $password
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    return "cURL Error #:" . $err;
  } else {
    return $response;
  }
}

function Auditoria_Interna($usuario, $password, $coneccion, $descrip, $peticion, $codigo, $empresa, $monto, $request = null)
{
  $r2 = mysql_query("
	INSERT INTO auditoria 
	(user_id,pass,coneccion,descrip,peticion,codigo,empresa,fecha,ip,monto, request) 
	VALUES
	('" . $usuario . "','" . $password . "','" . $coneccion . "','" . $descrip . "','" . $peticion . "','" . $codigo . "','" . $empresa . "','" . date('Y-m-d H:i:s') . "','" . $_SERVER['REMOTE_ADDR'] . "','" . $monto . "','" . $request . "')");
}
