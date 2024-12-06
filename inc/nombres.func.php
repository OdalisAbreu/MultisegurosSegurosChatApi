<?php

function ClientePers($id)
{
  $r2m = mysql_query(
    "SELECT id,nombres FROM personal WHERE id='" . $id . "' LIMIT 1"
  );
  while ($row2m = mysql_fetch_array($r2m)) {
    $nombresm = $row2m['nombres'];
  }
  return $nombresm;
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

function Cliente($id)
{
  $r2 = mysql_query(
    "SELECT id,asegurado_nombres,asegurado_apellidos,asegurado_telefono1,asegurado_cedula,ciudad,asegurado_direccion,asegurado_telefono1 FROM seguro_clientes WHERE id='" .
      $id .
      "' LIMIT 1"
  );
  while ($row2 = mysql_fetch_array($r2)) {
    $nombres =
      $row2['asegurado_nombres'] .
      "|" .
      $row2['asegurado_apellidos'] .
      "|" .
      $row2['asegurado_telefono1'] .
      "|" .
      $row2['asegurado_cedula'] .
      "|" .
      $row2['ciudad'] .
      "|" .
      $row2['id'] .
      "|" .
      $row2['asegurado_direccion'] .
      "|" .
      $row2['asegurado_telefono1'];
  }
  return $nombres;
}

function NomAseg($id)
{
  $r2mas = mysql_query(
    "SELECT id,nombre FROM seguros WHERE id='" . $id . "' LIMIT 1"
  );
  while ($row2mas = mysql_fetch_array($r2mas)) {
    $nombresmas = $row2mas['nombre'];
  }
  return $nombresmas;
}

function NombreSeguroS($id)
{
  $r5 = mysql_query(
    "SELECT id, nombre FROM seguros WHERE id='" . $id . "' LIMIT 1"
  );
  $row5 = mysql_fetch_array($r5);
  return $row5['nombre'];
}

function NombreProgS($id)
{
  $r51 = mysql_query(
    "SELECT id, nombre FROM suplidores WHERE id='" . $id . "' LIMIT 1"
  );
  $row51 = mysql_fetch_array($r51);
  return $row51['nombre'];
}

function NombreBancoRep($id)
{
  $querbancy = mysql_query(
    "SELECT * FROM cuentas_de_banco 
	WHERE id ='" .
      $id .
      "' LIMIT 1"
  );
  $ssd = mysql_fetch_array($querbancy);
  return $ssd['nombre_banc'] .
    " (<font style='font-size: 13px; color: #2196F3;'>No. Cta. " .
    $ssd['num_cuenta'] .
    "</font>)";
}

function NombreBancoSuplidoresRep($id)
{
  $querbancy = mysql_query(
    "SELECT * FROM bancos_suplidores 
	WHERE id ='" .
      $id .
      "' LIMIT 1"
  );
  $ssd = mysql_fetch_array($querbancy);

  //validar nombr del banco
  $Qbanc = mysql_query(
    "SELECT * FROM bancos 
	WHERE id ='" .
      $ssd['id_banc'] .
      "' LIMIT 1"
  );
  $Ds = mysql_fetch_array($Qbanc);

  return $Ds['nombre_banc'] .
    " (<font style='font-size: 13px; color: #2196F3;'>No. Cta. " .
    $ssd['num_cuenta'] .
    "</font>)";
}

function Sigla($id)
{
  $Sigla = mysql_query(
    "SELECT * from suplidores WHERE id_seguro ='" . $id . "' LIMIT 1"
  );
  $rSigla = mysql_fetch_array($Sigla);
  return $rSigla['sigla'];
}

function NombreSuplidoresRep($id)
{
  $querbancy1 = mysql_query(
    "SELECT * FROM bancos_suplidores 
	WHERE id ='" .
      $id .
      "' LIMIT 1"
  );
  $ssd1 = mysql_fetch_array($querbancy1);
  return $ssd1['nombres'];
}

function MontoSeguroRemesas($id_veh, $vigencia)
{
  $q1s = mysql_query(
    "SELECT * FROM seguro_vehiculo 
	WHERE id ='" .
      $id_veh .
      "' LIMIT 1"
  );
  $sxc = mysql_fetch_array($q1s);

  $sxw = mysql_query(
    "SELECT * FROM seguro_tarifas 
	WHERE veh_tipo ='" .
      $sxc['veh_tipo'] .
      "' LIMIT 1"
  );
  $vcx = mysql_fetch_array($sxw);

  if ($vigencia == '3') {
    return $vcx['3meses'];
  }
  if ($vigencia == '6') {
    return $vcx['6meses'];
  }
  if ($vigencia == '12') {
    return $vcx['12meses'];
  }
}

function TipoVehiculo($id)
{
  $sxwTV = mysql_query(
    "SELECT * FROM seguro_tarifas 
	WHERE veh_tipo ='" .
      $id .
      "' LIMIT 1"
  );
  $RvcxTVs = mysql_fetch_array($sxwTV);
  return $RvcxTVs['nombre'];
}

function VehiculoMarca($id)
{
  $sxwTVM = mysql_query(
    "SELECT * FROM seguro_marcas 
	WHERE ID ='" .
      $id .
      "' LIMIT 1"
  );
  $RvcxTVM = mysql_fetch_array($sxwTVM);
  return $RvcxTVM['DESCRIPCION'];
}

function VehiculoModelos($id)
{
  $sxwTV = mysql_query(
    "SELECT * FROM seguro_modelos 
	WHERE ID ='" .
      $id .
      "' LIMIT 1"
  );
  $RvcxTV = mysql_fetch_array($sxwTV);
  return $RvcxTV['descripcion'];
}

function TarifaVehiculo($id, $aseguradoraId)
{
  $asegId = 4;
  if ($aseguradoraId > 5) {
    $asegId = 7;
  }
  $sxwTV = mysql_query(
    "SELECT * FROM seguro_tarifas 
	WHERE veh_tipo ='$id' AND aseguradoraId = '$asegId'  LIMIT 1"
  );
  $RvcxTV = mysql_fetch_array($sxwTV);
  return $RvcxTV['dpa'] .
    "/" .
    $RvcxTV['rc'] .
    "/" .
    $RvcxTV['rc2'] .
    "/" .
    $RvcxTV['ap'] .
    "/" .
    $RvcxTV['fj'];
}

function CedulaPDF($id)
{
  $in = str_replace("-", "", $id);
  $cedula =
    substr($in, 0, 3) . "-" . substr($in, 3, -1) . "-" . substr($in, -1);
  return $cedula;
}

function TelefonoPDF($id)
{
  $in = str_replace("-", "", $id);
  $in2 = substr($in, 0, 3) . "-" . substr($in, 3, 3) . "-" . substr($in, -4);
  return $in2;
}

function FechaListPDF($id)
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

  return $f[2] .
    '-' .
    $f[1] .
    '-' .
    $f[0] .
    " (" .
    $hora .
    ":" .
    $fh[1] .
    ":" .
    $fh[2] .
    ")";
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

function GetPrefijo($id)
{
  $queryp = mysql_query(
    "SELECT * FROM  seguros WHERE id='" . $id . "' LIMIT 1"
  );
  $rowp = mysql_fetch_array($queryp);
  return $rowp['prefijo'];
}

function ValidarReverso($aseg, $poliza)
{
  $vr = mysql_query(
    "SELECT id,id_aseg,id_poliza FROM  seguro_transacciones_reversos 
	WHERE id_aseg='" .
      $aseg .
      "' AND id_poliza ='" .
      $poliza .
      "' LIMIT 1"
  );
  $rvr = mysql_fetch_array($vr);

  if ($rvr['id']) {
    return "15"; //HAY REGISTROS DE REVERSOS
  } else {
    return "00"; //NO HAY REGISTROS DE REVERSOS
  }
}

function CreditoActualAnular($id)
{
  $qC = mysql_query(
    "SELECT * FROM  recarga_retiro WHERE id_pers='" .
      $id .
      "' order by id desc LIMIT 1"
  );
  $rC = mysql_fetch_array($qC);
  return $rC['cred_actual'];
}

function BalActualAnular($id)
{
  $qCa = mysql_query("SELECT * FROM  personal WHERE id='" . $id . "' LIMIT 1");
  $rCa = mysql_fetch_array($qCa);
  return $rCa['balance'];
}

function ServAdicional($id, $vigencia)
{
  $sxwTVMa = mysql_query(
    "SELECT id,nombre,3meses,6meses,12meses FROM servicios WHERE id ='" .
      $id .
      "' LIMIT 1"
  );
  $RvcxTVMa = mysql_fetch_array($sxwTVMa);

  if ($vigencia == '3') {
    return $RvcxTVMa['nombre'] . "|" . $RvcxTVMa['3meses'];
  }
  if ($vigencia == '6') {
    return $RvcxTVMa['nombre'] . "|" . $RvcxTVMa['6meses'];
  }
  if ($vigencia == '12') {
    return $RvcxTVMa['nombre'] . "|" . $RvcxTVMa['12meses'];
  }
}

function montoSeguro($vigencia_poliza, $veh_tipo)
{
  $sxwTVMa = mysql_query(
    "SELECT veh_tipo,3meses,6meses,12meses FROM seguro_tarifas WHERE veh_tipo ='" .
      $veh_tipo .
      "' LIMIT 1"
  );
  $RvcxTVMa = mysql_fetch_array($sxwTVMa);

  if ($vigencia_poliza == '3') {
    return $RvcxTVMa['3meses'];
  }
  if ($vigencia_poliza == '6') {
    return $RvcxTVMa['6meses'];
  }
  if ($vigencia_poliza == '12') {
    return $RvcxTVMa['12meses'];
  }
}

function Tipo($id)
{
  $vehtipo = mysql_query(
    "SELECT id,veh_tipo FROM seguro_vehiculo WHERE id='" . $id . "' LIMIT 1"
  );
  while ($rowvehtipo = mysql_fetch_array($vehtipo)) {
    //ARRAY PARA SACAR EL TIPO DEL HEVICULO
    $vehtipos = mysql_query(
      "SELECT id,veh_tipo,nombre,dpa,rc,rc2,fj,id_general_seguro FROM seguro_tarifas WHERE veh_tipo='" .
        $rowvehtipo['veh_tipo'] .
        "' LIMIT 1"
    );
    while ($rowvehtipos = mysql_fetch_array($vehtipos)) {
      return $rowvehtipos['nombre'] .
        "/" .
        $rowvehtipos['dpa'] .
        "/" .
        $rowvehtipos['rc'] .
        "/" .
        $rowvehtipos['rc2'] .
        "/" .
        $rowvehtipos['fj'] .
        "/" .
        $rowvehtipo['veh_tipo'] .
        "/" .
        $rowvehtipos['id_general_seguro'] .
        "";
    }
  }
}

function VehiculoExport($id)
{
  $r2 = mysql_query(
    "SELECT id,veh_marca,veh_modelo,veh_chassis,veh_ano,veh_matricula FROM seguro_vehiculo WHERE id='" .
      $id .
      "' LIMIT 1"
  );
  $row2 = mysql_fetch_array($r2);

  $marca = $row2['veh_marca'];
  $modelo = $row2['veh_modelo'];
  $year = $row2['veh_ano'];
  $chassis = $row2['veh_chassis'];
  $placa = $row2['veh_matricula'];

  return $marca .
    "/" .
    $modelo .
    "" .
    "/" .
    $year .
    "" .
    "/" .
    $chassis .
    "" .
    "/" .
    $placa .
    "";
}
function FechaReporte($id)
{
  $clear1 = explode(' ', $id);

  $fecha_vigente1 = explode('-', $clear1[0]);

  if ($fecha_vigente1[1] == '01') {
    $mes = 'Ene';
  }
  if ($fecha_vigente1[1] == '02') {
    $mes = 'Feb';
  }
  if ($fecha_vigente1[1] == '03') {
    $mes = 'Mar';
  }
  if ($fecha_vigente1[1] == '04') {
    $mes = 'Abr';
  }
  if ($fecha_vigente1[1] == '05') {
    $mes = 'May';
  }
  if ($fecha_vigente1[1] == '06') {
    $mes = 'Jun';
  }
  if ($fecha_vigente1[1] == '07') {
    $mes = 'Jul';
  }
  if ($fecha_vigente1[1] == '08') {
    $mes = 'Ago';
  }
  if ($fecha_vigente1[1] == '09') {
    $mes = 'Sep';
  }
  if ($fecha_vigente1[1] == '10') {
    $mes = 'Oct';
  }
  if ($fecha_vigente1[1] == '11') {
    $mes = 'Nov';
  }
  if ($fecha_vigente1[1] == '12') {
    $mes = 'Dic';
  }

  return $fecha_vigente1[2] .
    '-' .
    $fecha_vigente1[1] .
    '-' .
    $fecha_vigente1[0];
}
function Cedula($id)
{
  $r2ced = mysql_query(
    "SELECT id,asegurado_cedula FROM seguro_clientes WHERE id='" .
      $id .
      "' LIMIT 1"
  );
  while ($row2ced = mysql_fetch_array($r2ced)) {
    $cedula = str_replace("-", "", $row2ced['asegurado_cedula']);

    $in = $cedula;
    $cedula =
      substr($in, 0, 3) . "-" . substr($in, 3, -1) . "-" . substr($in, -1);
  }
  return $cedula;
}
function CedulaSinGuion($id)
{
  $r2ced = mysql_query(
    "SELECT id,asegurado_cedula FROM seguro_clientes WHERE id='" .
      $id .
      "' LIMIT 1"
  );
  while ($row2ced = mysql_fetch_array($r2ced)) {
    $cedula = str_replace("-", "", $row2ced['asegurado_cedula']);

    $in = $cedula;
    $cedula =
      substr($in, 0, 3) . "" . substr($in, 3, -1) . "" . substr($in, -1);
  }
  return $cedula;
}
function PlanGeneral($id)
{
  if ($id == '1') {
    return "6";
  } //Autobuses (De 16 a 60 Pasajeros)', 1,  ------
  if ($id == '2') {
    return "2";
  } //Automovil', 2, 						------
  if ($id == '3') {
    return "7";
  } //Camion', 3, 							------
  if ($id == '4') {
    return "7";
  } //Camion Cabezote', 4, 					------
  if ($id == '5') {
    return "7";
  } //Camion Volteo', 5,						------
  if ($id == '6') {
    return "3";
  } //Camioneta', 6,   						------
  if ($id == '7') {
    return "1";
  } //Four Wheel', 7, 						------
  if ($id == '8') {
    return "3";
  } //Furgoneta', 8,							------
  if ($id == '9') {
    return "5";
  } //Grua', 9, 								------
  if ($id == '10') {
    return "2";
  } //Jeep', 10,  							------
  if ($id == '11') {
    return "2";
  } //Jeepeta', 11, 						------
  if ($id == '12') {
    return "5";
  } //Maquinaria Pesada', 12,				------
  if ($id == '13') {
    return "4";
  } //Minivan (Hasta 15 Pasajeros)', 13, 	------
  if ($id == '14') {
    return "1";
  } //Motocicleta', 14, 					------
  if ($id == '15') {
    return "1";
  } //Motoneta', 15, 						------
  if ($id == '16') {
    return "2";
  } //Station Wagon', 16, 					------
  if ($id == '17') {
    return "7";
  } //Trailer', 17,							------
  if ($id == '18') {
    return "4";
  } //Van (Hasta 15 Pasajeros)', 18,		------
  if ($id == '19') {
    return "6";
  } //Minibus (De 16 a 60 Pasajeros)', 19,  ------
  if ($id == '20') {
    return "7";
  } //Remolque', 20, 						------

  //Motocicletas, Motonetas y FourWheels (plan 1)
  // Automoviles y Jeeps  (plan 2)
  // CAMIONETAS (plan 3)
  // VANS, MINIVANS - Hasta 15 pasajeros (plan 4)
  //GRUAS y Maquinas Pesadas (plan 5)
  // Ambulancias, Veh. Especiales y Autobus (plan 6)
  // Camiones, Patanas y Trailers (plan 7)
}
function FechaReporteGeneral($id)
{
  $clear1 = explode(' ', $id);

  $fecha_vigente1 = explode('-', $clear1[0]);

  if ($fecha_vigente1[1] == '01') {
    $mes = 'Ene';
  }
  if ($fecha_vigente1[1] == '02') {
    $mes = 'Feb';
  }
  if ($fecha_vigente1[1] == '03') {
    $mes = 'Mar';
  }
  if ($fecha_vigente1[1] == '04') {
    $mes = 'Abr';
  }
  if ($fecha_vigente1[1] == '05') {
    $mes = 'May';
  }
  if ($fecha_vigente1[1] == '06') {
    $mes = 'Jun';
  }
  if ($fecha_vigente1[1] == '07') {
    $mes = 'Jul';
  }
  if ($fecha_vigente1[1] == '08') {
    $mes = 'Ago';
  }
  if ($fecha_vigente1[1] == '09') {
    $mes = 'Sep';
  }
  if ($fecha_vigente1[1] == '10') {
    $mes = 'Oct';
  }
  if ($fecha_vigente1[1] == '11') {
    $mes = 'Nov';
  }
  if ($fecha_vigente1[1] == '12') {
    $mes = 'Dic';
  }

  return $fecha_vigente1[2] . '' . $fecha_vigente1[1] . '' . $fecha_vigente1[0];
}
function FechaHora($id)
{
  $HoraFunc = explode(' ', $id);

  $hora2 = explode(':', $HoraFunc['1']);
  if ($hora2['0'] == '00') {
    $hora22 = '12';
  }
  if ($hora2['0'] == '01') {
    $hora22 = '01';
  }
  if ($hora2['0'] == '02') {
    $hora22 = '02';
  }
  if ($hora2['0'] == '03') {
    $hora22 = '03';
  }
  if ($hora2['0'] == '04') {
    $hora22 = '04';
  }
  if ($hora2['0'] == '05') {
    $hora22 = '05';
  }
  if ($hora2['0'] == '06') {
    $hora22 = '06';
  }
  if ($hora2['0'] == '07') {
    $hora22 = '07';
  }
  if ($hora2['0'] == '08') {
    $hora22 = '08';
  }
  if ($hora2['0'] == '09') {
    $hora22 = '09';
  }
  if ($hora2['0'] == '10') {
    $hora22 = '10';
  }
  if ($hora2['0'] == '11') {
    $hora22 = '11';
  }
  if ($hora2['0'] == '12') {
    $hora22 = '12';
  }
  if ($hora2['0'] == '13') {
    $hora22 = '01';
  }
  if ($hora2['0'] == '14') {
    $hora22 = '02';
  }
  if ($hora2['0'] == '15') {
    $hora22 = '03';
  }
  if ($hora2['0'] == '16') {
    $hora22 = '04';
  }
  if ($hora2['0'] == '17') {
    $hora22 = '05';
  }
  if ($hora2['0'] == '18') {
    $hora22 = '06';
  }
  if ($hora2['0'] == '19') {
    $hora22 = '07';
  }
  if ($hora2['0'] == '20') {
    $hora22 = '08';
  }
  if ($hora2['0'] == '21') {
    $hora22 = '09';
  }
  if ($hora2['0'] == '22') {
    $hora22 = '10';
  }
  if ($hora2['0'] == '23') {
    $hora22 = '11';
  }

  return $hora22 . ":" . $hora2['1'] . ":" . $hora2['2'];
}
function CedulaExport($id)
{
  $cedula = str_replace("-", "", $id);
  $in = $cedula;
  $ced = substr($in, 0, 3) . "-" . substr($in, 3, -1) . "-" . substr($in, -1);

  return $ced;
}

function Agencia($id)
{
  $rde = mysql_query(
    "SELECT id,id_agencia FROM personal WHERE id='" . $id . "' LIMIT 1"
  );
  $rrde = mysql_fetch_array($rde);

  $red = mysql_query(
    "SELECT * FROM agencia_via WHERE id='" . $rrde['id_agencia'] . "' LIMIT 1"
  );
  $rred = mysql_fetch_array($red);

  $ref = mysql_query(
    "SELECT * FROM supervisor WHERE id ='" .
      $rred['id_supervisor'] .
      "' LIMIT 1"
  );
  $rref = mysql_fetch_array($ref);

  return $rred['razon_social'] . "/" . $rref['nombre_ruta'];
}

function AgenciaVia($id)
{
  $red = mysql_query(
    "SELECT * FROM agencia_via WHERE num_agencia='" . $id . "' LIMIT 1"
  );

  $rred = mysql_fetch_array($red);

  if ($rred['num_agencia']) {
    return $rred['razon_social'] . "/" . $rred['ejecutivo'];
  } else {
    return "VIA/----";
  }
}

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

function Remplazar($text)
{
  $text = str_replace("Ñ", 'N', $text);
  $text = str_replace("ñ", 'n', $text);
  $text = str_replace("ā", 'a', $text);
  $text = str_replace("Ā", 'A', $text);
  $text = str_replace("É", 'E', $text);
  $text = str_replace("é", 'e', $text);
  $text = str_replace("í", 'i', $text);
  $text = str_replace("Í", 'I', $text);
  $text = str_replace("ú", 'u', $text);
  $text = str_replace("Ú", 'U', $text);
  $text = str_replace("Ó", 'O', $text);
  $text = str_replace("ó", 'o', $text);
  return $text;
}

function ServAdicHistory($id)
{
  $sxwTVMa = mysql_query(
    "SELECT id,nombre FROM servicios WHERE id ='" . $id . "' LIMIT 1"
  );
  $RvcxTVMa = mysql_fetch_array($sxwTVMa);
  return $RvcxTVMa['nombre'];
}

//PARA REPORTES DE LAS ASEGURADORAS
function RepMontoSeguro($idtrans)
{

  $qprec2c = mysql_query(
    "SELECT * FROM seguro_trans_history WHERE id_trans='" .
      $idtrans .
      "' 
	AND tipo='seg' LIMIT 1"
  );
  $rprec2c = mysql_fetch_array($qprec2c);
  return $rprec2c['monto'];
  //   $qprec2 = mysql_query(
  //     "SELECT 
  //     seguros.valores_alternos, trans.vigencia_poliza, veh.veh_tipo,
  //     IF(seguros.valores_alternos = 0,
  //         hist.monto,
  //         CASE
  //             WHEN trans.vigencia_poliza = 3 THEN va.prima_3meses
  //             WHEN trans.vigencia_poliza = 6 THEN va.prima_6meses
  //             WHEN trans.vigencia_poliza = 12 THEN va.prima_12meses
  //         END) monto
  // FROM
  //     seguro_trans_history hist
  //         INNER JOIN
  //     seguro_transacciones trans ON hist.id_trans = trans.id
  //         INNER JOIN
  //     seguros ON seguros.id = hist.id_aseg
  //         INNER JOIN
  //     seguro_vehiculo veh on trans.id_vehiculo = veh.id
  //         INNER JOIN 
  //     valores_acordados va ON va.tipo_vehiculo = veh.veh_tipo and va.id_aseguradora = hist.id_aseg
  // WHERE
  //     id_trans = '" .
  //       $idtrans .
  //       "'  AND hist.tipo = 'seg'
  //  LIMIT 1"
  //   );
  //   $rprec2 = mysql_fetch_array($qprec2);
  //   return $rprec2['monto'];
}

function RepMontoServicio($idtrans, $idserv)
{
  $qprec3 = mysql_query(
    "SELECT id_trans,id_serv_adc,monto FROM seguro_trans_history 
	WHERE id_trans='" .
      $idtrans .
      "' AND id_serv_adc='" .
      $idserv .
      "' LIMIT 1"
  );
  $rprec3 = mysql_fetch_array($qprec3);
  return $rprec3['monto'];
}

function RepMontoServ($id, $serv_adc)
{
  $ServOpcional = explode("-", $serv_adc);
  $MontoServiciosde = 0;
  for ($i = 0; $i < count($ServOpcional); $i++) {
    //BUSCAR SI SE SUMA O NO
    $qprec2 = mysql_query(
      "SELECT id,sumar FROM servicios WHERE id='" .
        $ServOpcional[$i] .
        "' LIMIT 1"
    );
    $rprec2 = mysql_fetch_array($qprec2);

    if ($rprec2['sumar'] == 's') {
      $MontoServiciosde += RepMontoServicio($id, $ServOpcional[$i]);
    }
  }
  return $MontoServiciosde;
}

function RepTipo($id)
{
  $queryt = mysql_query(
    "SELECT * FROM  seguro_tarifas WHERE veh_tipo='" . $id . "' LIMIT 1"
  );
  $rowt = mysql_fetch_array($queryt);
  return $rowt['nombre'] .
    "|" .
    $rowt['dpa'] .
    "|" .
    $rowt['ap'] .
    "|" .
    $rowt['rc'] .
    "|" .
    $rowt['rc2'] .
    "|" .
    $rowt['fj'] .
    "|" .
    $rowt['id_serv_rep'];
}

function Validar($id)
{
  $r512c2 = mysql_query(
    "SELECT * FROM servicios 
	WHERE id='" .
      $id .
      "' AND cambiar ='s' LIMIT 1"
  );
  $row512cx = mysql_fetch_array($r512c2);
  return $row512cx['dpa'] .
    "|" .
    $row512cx['ap'] .
    "|" .
    $row512cx['rc'] .
    "|" .
    $row512cx['rc2'] .
    "|" .
    $row512cx['fj'];
}

function CostoSeguroRemes($idtrans)
{
  $qprec2 = mysql_query(
    "SELECT 
    seguros.valores_alternos, trans.vigencia_poliza, veh.veh_tipo,
    IF(seguros.valores_alternos = 0,
        hist.costo,
        CASE
            WHEN trans.vigencia_poliza = 3 THEN va.costo_3meses
            WHEN trans.vigencia_poliza = 6 THEN va.costo_6meses
            WHEN trans.vigencia_poliza = 12 THEN va.costo_12meses
        END) costo
FROM
    seguro_trans_history hist
        INNER JOIN
    seguro_transacciones trans ON hist.id_trans = trans.id
        INNER JOIN
    seguros ON seguros.id = hist.id_aseg
        INNER JOIN
    seguro_vehiculo veh on trans.id_vehiculo = veh.id
        INNER JOIN 
    valores_acordados va ON va.tipo_vehiculo = veh.veh_tipo and va.id_aseguradora = hist.id_aseg
WHERE
    id_trans = '" .
      $idtrans .
      "'  AND hist.tipo = 'seg'
 LIMIT 1"
  );
  $rprec2 = mysql_fetch_array($qprec2);
  return $rprec2['costo'];
}

function PrecioSeguroRemes($idtrans)
{
  $qprec2 = mysql_query(
    "SELECT 
    seguros.valores_alternos, trans.vigencia_poliza, veh.veh_tipo,
    IF(seguros.valores_alternos = 0,
        hist.monto,
        CASE
            WHEN trans.vigencia_poliza = 3 THEN va.prima_3meses
            WHEN trans.vigencia_poliza = 6 THEN va.prima_6meses
            WHEN trans.vigencia_poliza = 12 THEN va.prima_12meses
        END) monto
FROM
    seguro_trans_history hist
        INNER JOIN
    seguro_transacciones trans ON hist.id_trans = trans.id
        INNER JOIN
    seguros ON seguros.id = hist.id_aseg
        INNER JOIN
    seguro_vehiculo veh on trans.id_vehiculo = veh.id
        INNER JOIN 
    valores_acordados va ON va.tipo_vehiculo = veh.veh_tipo and va.id_aseguradora = hist.id_aseg
WHERE
    id_trans = '" .
      $idtrans .
      "'  AND hist.tipo = 'seg'
 LIMIT 1"
  );
  $rprec2 = mysql_fetch_array($qprec2);
  return $rprec2['monto'];
}

function PrecioServicioRemes($id)
{
  $qprec2 = mysql_query(
    "SELECT * FROM seguro_trans_history WHERE id_serv_adc='" .
      $id .
      "' 
	AND tipo='serv' LIMIT 1"
  );
  $rprec2 = mysql_fetch_array($qprec2);
  return $rprec2['monto'];
}

function RepMontoServRemesa($id, $serv_adc)
{
  $ServOpcional = explode("-", $serv_adc);
  $MontoServiciosCde = 0;
  for ($i = 0; $i < count($ServOpcional); $i++) {
    if ($ServOpcional[$i] > 0) {
      //BUSCAR SI SE SUMA O NO
      $qprec2 = mysql_query(
        "SELECT id,sumar FROM servicios WHERE id='" .
          $ServOpcional[$i] .
          "' LIMIT 1"
      );

      $rprec2 = mysql_fetch_array($qprec2);
      if ($rprec2['sumar'] == 's') {
        $MontoServiciosCde += RepCostoServiciodosRemesa($id, $rprec2['id']);
      }
    }
  }
  return $MontoServiciosCde;
}

function RepCostoServiciodosRemesa($id, $serv_adc)
{
  $qprec22 = mysql_query(
    "SELECT id_trans, id_serv_adc, costo FROM seguro_trans_history 
	WHERE id_trans ='" .
      $id .
      "' AND id_serv_adc = '" .
      $serv_adc .
      "' AND tipo='serv' LIMIT 1"
  );
  $rprec22 = mysql_fetch_array($qprec22);
  return $rprec22['costo'];
}

function RepMontoServiciodosRemesa($id, $serv_adc)
{
  $qprec22 = mysql_query(
    "SELECT id_trans, id_serv_adc, monto FROM seguro_trans_history 
	WHERE id_trans ='" .
      $id .
      "' AND id_serv_adc = '" .
      $serv_adc .
      "' AND tipo='serv' LIMIT 1"
  );

  $rprec22 = mysql_fetch_array($qprec22);
  return $rprec22['monto'];
}

function sanear_string($string)
{
  $string = trim($string);

  $string = str_replace(
    array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
    array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
    $string
  );

  $string = str_replace(
    array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
    array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
    $string
  );

  $string = str_replace(
    array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
    array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
    $string
  );

  $string = str_replace(
    array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
    array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
    $string
  );

  $string = str_replace(
    array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
    array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
    $string
  );

  $string = str_replace(
    array('ñ', 'Ñ', 'ç', 'Ç'),
    array('n', 'N', 'c', 'C'),
    $string
  );

  //Esta parte se encarga de eliminar cualquier caracter extraño
  $string = str_replace(
    array(
      "¨",
      "º",
      "-",
      "~",
      "#",
      "@",
      "|",
      "!",
      '"',
      "'",
      "¡",
      "¿",
      "[",
      "^",
      "<code>",
      "]",
      "+",
      "}",
      "{",
      "¨",
      "´",
      ">",
      "< ",
      ";",
      ",",
      ":",
      "."
    ),
    '',
    $string
  );

  return $string;
}

function ModificarPref($id, $aseguradora)
{
  $qprec2 = mysql_query(
    "SELECT * FROM servicios WHERE id='" . $id . "' LIMIT 1"
  );
  $rprec2 = mysql_fetch_array($qprec2);

  if ($rprec2['mod_pref'] == 's') {
    return 'si|' . $rprec2['prefijo' . $aseguradora . ''];
  } else {
    return 'no|00';
  }
}

//PARA VALIDAR SI EL SERVICIO OPCIONAL SE CAMBIA O NO
function VerVariable($id)
{
  $SerOpcioal = explode("-", $id);
  for ($i = 0; $i < count($SerOpcioal); $i++) {
    //echo "ID SERV A REVISAR".$SerOpcioal[$i]."<br>";
    if ($SerOpcioal[$i] > 0) {
      $val = explode("|", Validar($SerOpcioal[$i]));
    }
    $dpa_1 = $val[0];
    $ap_1 = $val[1];
    $rc_1 = $val[2];
    $rc2_1 = $val[3];
    $fj_1 = $val[4];
  }

  return $dpa_1 . "|" . $ap_1 . "|" . $rc_1 . "|" . $rc2_1 . "|" . $fj_1;
}

function RepMontoServdos($id, $serv_adc)
{
  $ServOpcional = explode("-", $serv_adc);
  $MontoServiciosCde = 0;
  for ($i = 0; $i < count($ServOpcional); $i++) {
    if ($ServOpcional[$i] > 0) {
      //BUSCAR SI SE SUMA O NO
      $qprec2 = mysql_query(
        "SELECT id,sumar FROM servicios WHERE id='" .
          $ServOpcional[$i] .
          "' LIMIT 1"
      );

      $rprec2 = mysql_fetch_array($qprec2);
      if ($rprec2['sumar'] == 's') {
        $MontoServiciosCde += RepMontoServiciodos($id, $rprec2['id']);
      }
    }
  }
  return $MontoServiciosCde;
}

function RepMontoServiciodos($id, $serv_adc)
{
  $qprec22 = mysql_query(
    "SELECT id_trans, id_serv_adc, monto FROM seguro_trans_history 
	WHERE id_trans ='" .
      $id .
      "' AND id_serv_adc = '" .
      $serv_adc .
      "' AND tipo='serv' LIMIT 1"
  );

  $rprec22 = mysql_fetch_array($qprec22);
  return $rprec22['monto'];
}

function RepMontoServCosto($id, $serv_adc)
{
  $ServOpcional = explode("-", $serv_adc);
  $MontoServiciosCde = 0;
  for ($i = 0; $i < count($ServOpcional); $i++) {
    if ($ServOpcional[$i] > 0) {
      //BUSCAR SI SE SUMA O NO
      $qprec2 = mysql_query(
        "SELECT id,sumar FROM servicios WHERE id='" .
          $ServOpcional[$i] .
          "' LIMIT 1"
      );

      $rprec2 = mysql_fetch_array($qprec2);
      if ($rprec2['sumar'] == 's') {
        $MontoServiciosCde += RepMontoServiciodosCosto($id, $rprec2['id']);
      }
    }
  }
  return $MontoServiciosCde;
}

function RepMontoServiciodosCosto($id, $serv_adc)
{
  $qprec22 = mysql_query(
    "SELECT id_trans, id_serv_adc, monto, costo FROM seguro_trans_history 
	WHERE id_trans ='" .
      $id .
      "' AND id_serv_adc = '" .
      $serv_adc .
      "' AND tipo='serv' LIMIT 1"
  );

  $rprec22 = mysql_fetch_array($qprec22);
  return $rprec22['costo'];
}

function sendSMS($idTrans)
{
  $query = mysql_query(
    "select * from seguro_transacciones   
	WHERE id ='" .
      $idTrans .
      "' LIMIT 1"
  );

  $row = mysql_fetch_array($query);

  $laAgencia = getAgencia($row["id"]);
  $poliza =
    GetPrefijo($row['id_aseg']) .
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

  httpPost($urlSMS, $data, "jgrullon", "jgrullon2021*");
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
