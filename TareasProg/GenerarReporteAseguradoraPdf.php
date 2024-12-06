<?php

ini_set('display_errors', 1);

function microtime_float()
{
	list($useg, $seg) = explode(" ", microtime());
	return ((float) $useg + (float) $seg);
}


echo "Tiempo inicio: " . $tiempo_inicio = microtime_float() . "<br>";

set_time_limit(0);
include("../inc/conexion_inc.php");
include("../inc/fechas.func.php");
include("../inc/nombres.func.php");
Conectarse();

$directorio = "https://multiseguros.com.do/SegurosChat/images/";
$logo = "https://multiseguros.com.do/SegurosChat/images/Aseguradora/";


date_default_timezone_set('America/Santo_Domingo');
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

$ancho  = "690";
$anchoP = "345";
$altura = "3100";

// --------------------------------------------	
if ($_GET['fecha1']) {
	$fecha1 = $_GET['fecha1'];
} else {
	$fecha1 = fecha_despues('' . date('d/m/Y') . '', -2);
}
// --------------------------------------------
if ($_GET['fecha2']) {
	$fecha2 = $_GET['fecha2'];
} else {
	$fecha2 = fecha_despues('' . date('d/m/Y') . '', -2);
}
// -------------------------------------------

$fd1		= explode('/', $fecha1);
$fh1		= explode('/', $fecha2);
$fDesde 	= $fd1[2] . '-' . $fd1[1] . '-' . $fd1[0];
$fHasta 	= $fh1[2] . '-' . $fh1[1] . '-' . $fh1[0];

$wFecha2 = "fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 23:59:59' ";


//BUSCAR PRIMERA TRANSACCION
$Q1 = mysql_query("select * from seguro_transacciones   
	WHERE $wFecha2 AND id_aseg='1' order by id ASC");
while ($R1 = mysql_fetch_array($Q1)) {
	$t[] = $R1;
}




//BUSCAR ULTIMA TRANSACCION
/*$Q2=mysql_query("select * from seguro_transacciones   
	WHERE $wFecha2 AND id_aseg='1' order by id DESC LIMIT 1");
  	$R2=mysql_fetch_array($Q2);*/



foreach ($t as $key => $row) {


	$id_aseguradora = $row['id_aseg'];

	switch ($id_aseguradora) {
		case '1':
			$NombreImg = "dominicana.jpg";
			break;
		case '2':
			$NombreImg = "patria.png";
			break;
		case '3':
			$NombreImg = "general.png";
			break;
		case '4':
			$NombreImg = "atrio.png";
			break;
		default:
			$NombreImg = "";
			break;
	}


	echo "ID consulta: " . $row['id'] . "<br>";
	echo "seguro: " . $row['id_aseg'] . "<br>";
	echo "cliente: " . $row['id_cliente'] . "<br>";
	echo "vehiculo: " . $row['id_vehiculo'] . "<br>";
	echo "poliza: " . $row['vigencia_poliza'] . "<br>";


	//BUSCAR DATOS DEL CLIENTE
	$QClient = mysql_query("select * from seguro_clientes WHERE id ='" . $row['id_cliente'] . "' LIMIT 1");
	$RQClient = mysql_fetch_array($QClient);

	//BUSCAR DATOS DEL VEHICULO
	$QVeh = mysql_query("select * from seguro_vehiculo WHERE id ='" . $row['id_vehiculo'] . "' LIMIT 1");
	$RQVehi = mysql_fetch_array($QVeh);

	$tarifa = explode("/", TarifaVehiculo($RQVehi['veh_tipo']));

	$dpa 	= substr(FormatDinero($tarifa[0]), 0, -3);
	$rc 		= substr(FormatDinero($tarifa[1]), 0, -3);
	$rc2 	= substr(FormatDinero($tarifa[2]), 0, -3);
	$ap 		= substr(FormatDinero($tarifa[3]), 0, -3);
	$fj 		= substr(FormatDinero($tarifa[4]), 0, -3);

	$montoSeguro = montoSeguro($row['vigencia_poliza'], $RQVehi['veh_tipo']);


	$html .= '
<table width="' . $ancho . 'px;" height="' . $altura . 'px;"  style="font-size:25px;" align="center" cellpadding="4" cellspacing="0"> 
        	<tr>
            	<td width="60%" ><h1>CERTIFICADO DE SEGURO<br>
				VEHICULOSDE MOTOR</h1></td>
               
                <td width="40%"><img src="' . $directorio . '/logo.png"  alt=""/></td>
            </tr>
        </table>
		
			
<table width="' . $ancho . 'px;" height="' . $altura . 'px;"  style="font-size:25px;" align="center" cellpadding="4" cellspacing="0">
  <tr>
    <td  valign="top" align="left"><b>ASEGURADO: ' . $RQClient['asegurado_nombres'] . ' ' . $RQClient['asegurado_apellidos'] . '</b></td>
    <td  valign="top" align="left"><b>POLIZA NO: ' . GetPrefijo($row['id_aseg']) . '-' . str_pad($row['id_poliza'], 6, "0", STR_PAD_LEFT) . '</b></td>
  </tr>
  <tr>
    <td  valign="top" align="left"><b>CEDULA: ' . CedulaPDF($RQClient['asegurado_cedula']) . '</b></td>
    <td valign="top"  align="left"><b>ASEGURADORA: ' . NombreSeguroS($row['id_aseg']) . '</b></td>
  </tr>
  <tr>
    <td  valign="top" align="left"><b>DIRECCION: ' . $RQClient['asegurado_direccion'] . '</b></td>
    <td valign="top"  align="left"><b>FECHA DE EMISION: ' . FechaListPDF($row['fecha']) . '</b></td>
  </tr>
  <tr>
    <td  valign="top" align="left"><b>TELEFONO: ' . TelefonoPDF($RQClient['asegurado_telefono1']) . '</b></td>
    <td valign="top"  align="left"><b>INICIO DE VIGENCIA: ' . FechaListPDFn($row['fecha_inicio']) . '</b></td>
  </tr>
  <tr>
    <td  valign="top" align="left"><b>AGENCIA: ' . ClientePers($row['user_id']) . '</b></td>
    <td valign="top"  align="left"><b>FIN DE VIGENCIA: ' . FechaListPDFin($row['fecha_fin']) . '</b></td>
  </tr>
  <tr>
    <td valign="top" colspan="2" style="text-align:justify; border-top:solid 1px #000;">
    	<p style="margin-top:-10px"><b>Términos y Partes</b></p>
En virtud del pago de la prima estipulada y basándose en las declaraciones y garantías expresas más abajo, la Aseguradora se obliga a indemnizar al asegurado hasta una cantidad que no exceda los límites que se consignan, por las pérdidas o daños por él sostenidos de hecho y por los riesgos que, según se explican es esta póliza, puedan sufrir o causar el vehículo que se descrito en la misma, mientras esté dentro del territorio de la República Dominicana y siempre que tales pérdidas o daños hayan sido sufridos por el Asegurado debido a accidentes dentro del período de tiempo comprendido entre el día y la hora señalados como inicio de vigencia y las doce (12) meridiano del día señalado como fin de fin de vigencia. Esta póliza solamente asegura contra aquellos riesgos por los cuales aparezca específicamente cargada una prima. 
<p>Este Certificado de Seguro está sujeto a todos los demás términos, cláusulas, endosos y condiciones de la póliza de Vehículos de Motor aprobados por la Superintendencia de Seguros y contemplados en la Ley 146-02 sobre Seguros y Fianzas, salvo sus excepciones y los servicios opcionales que son contratados con sus respectivos suplidores.</p>

<b>Declaraciones y Garantías por el Asegurado</b> 
Las informaciones contenidas en este documento son las declaraciones y garantías suministradas por el asegurado, quien garantiza la exactitud y veracidad de las mismas y, basándose en ellas, la Aseguradora emite esta póliza, limitándose a aplicar las primas que correspondan con arreglo a dichas declaraciones.
    </td>
  </tr>
  
  <tr>
    <td valign="top" colspan="2" align="center" style="border-top:solid 1px #000;">
    	<h2 ><b>PLAN BASICO DE LEY - CONDICIONES PARTICULARES</b></h2>
    </td>
  </tr>
  <tr>
    <td  valign="bottom" align="left"><b>TIPO:</b> ' . TipoVehiculo($RQVehi['veh_tipo']) . '</td>
    <td valign="bottom" align="left"><b>AÑO:</b> ' . $RQVehi['veh_ano'] . '</td>
  </tr>
  <tr>
    <td valign="bottom" align="left"><b>MARCA:</b> ' . VehiculoMarca($RQVehi['veh_marca']) . '</td>
    <td valign="bottom" align="left"><b>CHASSIS:</b> ' . $RQVehi['veh_chassis'] . '</td>
  </tr>
  <tr>
    <td valign="bottom" align="left"><b>MODELO:</b> ' . VehiculoModelos($RQVehi['veh_modelo']) . '</td>
    <td valign="bottom" align="left"><b>REGISTRO:</b> ' . $RQVehi['veh_matricula'] . '</td>
  </tr>
  <tr>
    <td valign="top" style="border-top:solid 1px #000; border-left:solid 1px #000;">
	
	
	
	<table width="' . $anchoP . 'px;" cellpadding="3" cellspacing="0">
	    <tr>
			<td colspan="2" align="left"><b>COBERTURAS   Y LIMITES (En RD$)</b></td>
		</tr>
	  	<tr>
			<td align="left" width="260">Daños a la Propiedad Ajena</td>
			<td align="left">RD$ ' . $dpa . '</td>
		</tr>
		<tr>
			<td align="left">Lesiones Corporales o Muerte 1 Persona</td>
			<td align="left">RD$ ' . $rc . '</td>
		</tr>
		<tr>
			<td align="left">Lesiones Corporales o Muerte Más de 1 Persona</td>
			<td align="left">RD$ ' . $rc2 . '</td>
		</tr>
		<tr>
			<td align="left">Lesiones Corporales o Muerte 1 Pasajero</td>
			<td align="left">RD$ ' . $rc . '</td>
		</tr>
		<tr>
			<td align="left">Lesiones Corporales o Muerte Más de 1 Pasajero</td>
			<td align="left">RD$ ' . $rc2 . '</td>
		</tr>
		<tr>
			<td align="left">Accidentes Personales Conductor</td>
			<td align="left">RD$ ' . $ap . '</td>
		</tr>
		<tr>
			<td align="left">Fianza Judicial</td>
			<td align="left">RD$ ' . $fj . '</td>
		</tr>
	  </table>
	
	
	
	</td>
    <td valign="top" style="border-top:solid 1px #000; border-left:solid 1px #000;">
	
	
	
	<table width="' . $anchoP . 'px;" cellpadding="3" cellspacing="0">
	    <tr>
			<td colspan="2" align="left"><b><b>SERVICIOS   ADICIONALES</b></b></td>
		</tr>
		
		';

	if ($row['serv_adc'] != '') {
		//BUSCAR CANTAIDAD DE LOS SERVICIOS ADICIONALES
		$porciones = explode("-", $row['serv_adc']);

		for ($i = 0; $i < count($porciones); $i++) {

			if ($porciones > 0) {
				$r = explode("|", ServAdicional($porciones[$i], $row['vigencia_poliza']));
				$NombreServ = $r[0];
				$MontoServ = $r[1];

				$montoServAdc += $MontoServ;



				$html .= '     
        <tr>
			<td align="left" width="265">' . $NombreServ . " - Incluido" . '</td>
            <td align="left">RD$ ' . FormatDinero($MontoServ) . '</td>
		</tr>';
			}
		}
	}

	$html .= '
	  	
    </table>
	
	
	
	
	</td>
  </tr>
  <tr>
    <td align="left" style="border-top:solid 1px #000; border-left:solid 1px #000; border-bottom:solid 1px #000; padding-left:25px; padding-bottom:25px; padding-top:25px; background-color:#E0E0E0; margin-bottom:10px; margin-top:10px; " valign="middle">
	
	
	
	<table width="' . $anchoP . 'px;" cellpadding="1" cellspacing="0">
	  	<tr>
			<td align="left" width="262"><b>Prima Seguro Básico</b></td>
			<td align="left"><b>RD$ ' . FormatDinero($montoSeguro) . '</b></td>
		</tr>
	</table>
	
	
	
	
	</td>
    <td align="left" style="border-top:solid 1px #000; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; padding-left:25px; padding-bottom:25px; padding-top:25px; background-color:#E0E0E0; margin-bottom:10px; margin-top:10px; " valign="middle">
	
	
	
	<table width="' . $anchoP . 'px;" cellpadding="1" cellspacing="0">
	  	<tr>
			<td align="left" width="265"><b>Prima Servicios Adicionales</b></td>
			<td align="left"><b>RD$ ' . FormatDinero($montoServAdc) . '</b></td>
		</tr>
	</table>
	
	
	
	</td>
  </tr>
  
  <tr>
  	<td colspan="2" align="right" height="25" style="font-size:35px">
    	<strong>Total Póliza              RD$ ' . FormatDinero($montoSeguro + $montoServAdc) . '</strong>      
    </td>
  </tr>
   <tr>
  	<td colspan="2" align="right">&nbsp;</td>
  </tr>
   <tr>
  	<td colspan="2" align="center" style="border-style: dashed;">



		<table align="center" cellpadding="2" style="margin-top:25px; " width="650px">
                
				<tr>
					<td style="border-right-style: dashed; border-left-style: dashed; border-top-style: dashed; border-bottom-style: dashed;"> 
					
					
					<table align="center" cellpadding="2" border="0">
    <tr>
        <td align="left" ><img src="' . $logo . $NombreImg . '"  alt="" width="130px"/></td>
        <td align="left" ><img src="' . $directorio . '/logo.png" alt="" width="130px"/></td>
    </tr>
    <tr>
        <td align="left" width="83"><b>NO. POLIZA:</b></td>
        <td align="left" ><b>' . GetPrefijo($row['id_aseg']) . '-' . str_pad($row['id_poliza'], 6, "0", STR_PAD_LEFT) . '</b></td>
    </tr>
    <tr>
        <td align="left" width="83"><b>NOMBRES:</b></td>
        <td align="left"><b>' . $RQClient['asegurado_nombres'] . ' ' . $RQClient['asegurado_apellidos'] . '</b></td>
    </tr>
    <tr>
        <td align="left" width="83"><b>VEHICULO:</b></td>
        <td align="left"><b>' . TipoVehiculo($RQVehi['veh_tipo']) . ' ' . VehiculoMarca($RQVehi['veh_marca']) . '</b></td>
    </tr>
    <tr>
        <td align="left" width="83"><b>CHASSIS:</b></td>
        <td align="left"><b>' . $RQVehi['veh_chassis'] . '</b></td>
    </tr>
    <tr>
        <td align="left" width="83"><b>VIGENCIA:</b></td>
        <td align="left">
			<b style="font-size:18px">DESDE</b> 
			<b>' . FechaListPDFn($row['fecha_inicio']) . '</b> 
			<b style="font-size:18px">HASTA</b> 
			<b>' . FechaListPDFin($row['fecha_fin']) . '</b>
		</td>
    </tr>
    <tr>
        <td align="left" width="83"><b>FIANZA JUDICIAL:</b></td>
        <td align="left"><b>RD$ ' . $fj . '</b></td>
    </tr>
</table>
					
					
					
					</td>
					<td  style="border-right-style: dashed; border-left-style: dashed; border-top-style: dashed; border-bottom-style: dashed;"> 
					
					
					
					
<table align="center" cellpadding="2" border="0">
  <tr>
    <td colspan="2" style="font-size:21px" align="left">
El vehículo descrito en el anverso está asegurado bajo la póliza emitida por La Aseguradora, <br>
sujeto a los términos, límites y condiciones que en ella se expresan y al pago de la prima. <br>
<br>
<b>En caso de accidente:</b> <br>
&nbsp;&nbsp;1- Asista a los lesionados, si los hubiere. Con cuidado, retire los vehículos de la vía. <br>
&nbsp;&nbsp;2- No acepte responsabilidad al momento del accidente; reserve su derecho. <br>
&nbsp;&nbsp;3- Obtenga el nombre y la dirección del conductor y el propietario del otro vehículo. <br>
&nbsp;&nbsp;4- Obtenga el número de placa, aseguradora, y número de póliza. <br>
&nbsp;&nbsp;5- Obtenga el nombre y dirección de los lesionados y testigos. <br>
<br>
<b style="text-align:center">Comuníquese con la aseguradora antes de iniciar cualquier trámite</b><br>
    </td>
  </tr>

<tr>
	<td colspan="2" align="left"><img src="' . $logo . $NombreImg . '"  alt="" width="100px"/></td>
</tr>

<tr>
  <td align="left">
Santo Domingo: 809-547-1234 <br>
Santo Domingo Este: 809-483-3555 <br>
Santiago: 809-582-1161 <br>
La Romana: 809-556-2789<br>

  
  </td>
  <td align="center" valign="middle" style="color:#6886FD">
  <br>Asistencia Vial <br>
Casa del Conductor <br>
809 381 2424
  </td>
</tr>




</table>
					
					
					
					
					
					 </td>
				</tr>
		</table>
		
		

    </td>
  </tr>
  <tr>
	<td colspan="2"><br><br><br>
	</td>
</tr>
</table>

';
	// set font


}
// * * * Direccion del Archivo

if ($html !== '0') {
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setLanguageArray($l);
	$pdf->AddPage();

	$pdf->writeHTML($html, true, 0, true, false, '');
	$pdf->lastPage();
	//$carpeta = 'PDF/ASEGURADORA/'.$_GET['id_aseg'].'';
	$carpeta = 'PDF/ASEGURADORA/1';

	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}

	$nombreFile = date('d-m-Y H:i:s');
	$pdf->Output("PDF/ASEGURADORA/1/$nombreFile.pdf", 'F');
	echo $nombreFile . ".pdf";
}

$tiempo_fin = microtime_float();

echo "Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);
