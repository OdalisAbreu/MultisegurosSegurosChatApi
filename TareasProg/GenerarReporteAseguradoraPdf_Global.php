<?php

$AA		= explode('/', $_GET['fecha1']);
$Fech_reg 	= $AA[2] . '-' . $AA[1] . '-' . $AA[0];
$dir 		= "PDF/CLIENTES/" . $_GET['user_id'] . "/" . $Fech_reg . ".pdf";

/*  if (file_exists($dir)) { 
*/	?>
<!-- <a href="javascript:void(0)" class="btn btn-success"  onclick="location.replace('../ws_dev/TareasProg/Descargar.php?fecha=<?= $Fech_reg ?>&user_id=<?= $_GET['user_id'] ?>');"><b>Descargar Poliza</b></a>-->
<?
/*} else {*/

//header("Content-Type: text/html;charset=utf-8");
ini_set('display_errors', 0);

set_time_limit(0);
include("../inc/conexion_inc.php");
include("../inc/fechas.func.php");
include("../inc/nombres.func.php");
Conectarse();




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

$wFecha2 	= "fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 23:59:59' ";
$directorio = "https://multiseguros.com.do/SegurosChat/images/";
$logo 		= "https://multiseguros.com.do/SegurosChat/images/Aseguradora/";

$w_user = "(
	user_id='" . $_GET['user_id'] . "'";

// PUNTOS DE VENTAS
$quer1 = mysql_query("
	SELECT id FROM personal WHERE id_dist ='" . $_GET['user_id'] . "'");
while ($u = mysql_fetch_array($quer1)) {

	$w_user .= " OR user_id='" . $u['id'] . "'";

	$quer2 = mysql_query("
		SELECT id FROM personal WHERE id_dist ='" . $u['id'] . "'");
	while ($u2 = mysql_fetch_array($quer2)) {
		$w_user .= " OR user_id='" . $u2['id'] . "'";
	}
}
$w_user .= " )";

$_GET['ruta_id'] = str_replace("-", " ", $_GET['ruta_id']);

if ($_GET['ruta_id'] != '1todos') {
	$ejecutivo = " ejecutivo LIKE '%" . $_GET['ruta_id'] . "%' ";
} else {
	//$ejecutivo = "id !='' ";
}

$ED = mysql_query("SELECT * from agencia_via WHERE $ejecutivo $idpers ");
//echo "<br>SELECT * from agencia_via WHERE $ejecutivo $idpers  <br>";
while ($RED = mysql_fetch_array($ED)) {
	$agenci .= "x_id LIKE '%" . $RED['num_agencia'] . "-%' OR ";
}

$resulAgen = rtrim($agenci, 'OR ');


if ($resulAgen) {
	$resulAgen = "AND (" . $resulAgen . ") ";
}


//BUSCAR PRIMERA TRANSACCION
$Q1 = mysql_query("select * from seguro_transacciones   
	WHERE $w_user AND $wFecha2 $resulAgen  order by id ASC");
while ($R1 = mysql_fetch_array($Q1)) {
	$Agent = explode("-", $R1['x_id']);
	$Agencia = explode("/", AgenciaVia($Agent[0]));
	//$t[] = $R1;
	$array[$Agencia[1]][] = $R1;
}


ksort($array);

foreach ($array as $key => $val) {

	foreach ($val as $key => $row) {

		$i++;


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

		$poliza = GetPrefijo($row['id_aseg']) . '-' . str_pad($row['id_poliza'], 6, "0", STR_PAD_LEFT);
		//echo "<br>ID consulta: ".$row['id']."<br>";
		//echo "seguro: ".$row['id_aseg']."<br>";
		//echo "cliente: ".$row['id_cliente']."<br>";
		//echo "vehiculo: ".$row['id_vehiculo']."<br>";
		//echo "poliza: ".$row['vigencia_poliza']."<br>";

		/*if($numero_filas==$i){
		echo "<br><br>REGISTRO REALIZADO COMPLETAMENTE";	
	}*/

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

		//$montoSeguro = montoSeguro($row['vigencia_poliza'],$RQVehi['veh_tipo']);

		$Agent = explode("-", $row['x_id']);
		$Agencia = explode("/", AgenciaVia($Agent[0]));

		$html .= '

<table border="0" cellpadding="0" cellspacing="0" style="margin-top:-10px; border:solid 2px #FFFFFF" width="700px;">
	<tr>
		<td style="height:1000px !important">
		


<table width="' . $ancho . 'px;" height="' . $altura . 'px;"  style="font-size:25px; margin-top:10px; " align="center" cellpadding="4" cellspacing="0"> 
        	<tr>
            	<td width="60%" ><h1>CERTIFICADO DE SEGURO<br>
				VEHICULOS DE MOTOR</h1></td>
               
                <td width="40%"><img src="' . $directorio . '/logo.png"  alt=""/></td>
            </tr>
        </table>
		
			
<table width="' . $ancho . 'px;" height="' . $altura . 'px;"  style="font-size:26px;" align="center" cellpadding="4" cellspacing="0" border="0">
  <tr>
    <td  valign="top" align="left"><b>ASEGURADO: ' . $RQClient['asegurado_nombres'] . ' ' . $RQClient['asegurado_apellidos'] . '</b></td>
    <td  valign="top" align="left"><b>POLIZA NO: ' . $poliza . '</b></td>
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
    <td  valign="top" align="left"><b>AGENCIA: ' . $Agent[0] . ' - ' . strtoupper(utf8_encode($Agencia[0])) . '</b></td>
    <td valign="top"  align="left"><b>FIN DE VIGENCIA: ' . FechaListPDFin($row['fecha_fin']) . '</b></td>
  </tr>
  <tr>
    <td  valign="top" align="left" colspan="2"><b>' . strtoupper(Remplazar($Agencia[1])) . '</b></td>
  </tr>
  <tr>
    <td valign="top" colspan="2" style="text-align:justify; border-top:solid 2px #000;">
<font style="margin-top:-10px"><b>Términos y Partes</b></font><br>
En virtud del pago de la prima estipulada y basándose en las declaraciones y garantías expresas más abajo, la Aseguradora se obliga a indemnizar al asegurado hasta una cantidad que no exceda los límites que se consignan, por las pérdidas o daños por él sostenidos de hecho y por los riesgos que, según se explican es esta póliza, puedan sufrir o causar el vehículo que se descrito en la misma, mientras esté dentro del territorio de la República Dominicana y siempre que tales pérdidas o daños hayan sido sufridos por el Asegurado debido a accidentes dentro del período de tiempo comprendido entre el día y la hora señalados como inicio de vigencia y las doce (12) meridiano del día señalado como fin de fin de vigencia. Esta póliza solamente asegura contra aquellos riesgos por los cuales aparezca específicamente cargada una prima. 
<p>Este Certificado de Seguro está sujeto a todos los demás términos, cláusulas, endosos y condiciones de la póliza de Vehículos de Motor aprobados por la Superintendencia de Seguros y contemplados en la Ley 146-02 sobre Seguros y Fianzas, salvo sus excepciones y los servicios opcionales que son contratados con sus respectivos suplidores.</p>

<b>Declaraciones y Garantías por el Asegurado</b><br>
Las informaciones contenidas en este documento son las declaraciones y garantías suministradas por el asegurado, quien garantiza la exactitud y veracidad de las mismas y, basándose en ellas, la Aseguradora emite esta póliza, limitándose a aplicar las primas que correspondan con arreglo a dichas declaraciones.
    </td>
  </tr>
  
  <tr>
    <td valign="top" colspan="2" align="center" style="border-top:solid 2px #000;">
    	<font><b>PLAN BASICO DE LEY - CONDICIONES PARTICULARES</b></font>
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
    <td valign="bottom" align="left"><b>REGISTRO (PLACA):</b> ' . $RQVehi['veh_matricula'] . '</td>
  </tr>
  <tr>
    <td valign="top" style="border-top:solid 2px #000; ">
	
	
	
	<table width="' . $anchoP . 'px;" cellpadding="3" cellspacing="0">
	    <tr>
			<td colspan="2" align="left"><b>COBERTURAS   Y LIMITES (En RD$)</b></td>
		</tr>
	  	<tr>
			<td align="left" width="260">Daños a la Propiedad Ajena</td>
			<td align="left">' . $dpa . '</td>
		</tr>
		<tr>
			<td align="left">Lesiones Corporales o Muerte 1 Persona</td>
			<td align="left">' . $rc . '</td>
		</tr>
		<tr>
			<td align="left">Lesiones Corporales o Muerte Más de 1 Persona</td>
			<td align="left">' . $rc2 . '</td>
		</tr>
		<tr>
			<td align="left">Lesiones Corporales o Muerte 1 Pasajero</td>
			<td align="left">' . $rc . '</td>
		</tr>
		<tr>
			<td align="left">Lesiones Corporales o Muerte Más de 1 Pasajero</td>
			<td align="left">' . $rc2 . '</td>
		</tr>
		<tr>
			<td align="left">Accidentes Personales Conductor</td>
			<td align="left">' . $ap . '</td>
		</tr>
		<tr>
			<td align="left">Fianza Judicial</td>
			<td align="left">' . $fj . '</td>
		</tr>
	  </table>
	
	
	
	</td>
    <td valign="top" style="border-top:solid 2px #000; border-left:solid 2px #000;">
	
	
	
	<table width="' . $anchoP . 'px;" cellpadding="3" cellspacing="0">
	    <tr>
			<td colspan="2" align="left"><b><b>SERVICIOS   ADICIONALES</b></b></td>
		</tr>
		
		';

		//BUSCAR CANTAIDAD DE LOS SERVICIOS ADICIONALES
		/*$porciones = explode("-", $row['serv_adc']);
	 
	for($i =0; $i < count($porciones); $i++){ 
	
	if($porciones>0){
	$r = explode("|", ServAdicional($porciones[$i],$row['vigencia_poliza']));
	$NombreServ = $r[0];
	$MontoServ = $r[1];
	
	$montoServAdc += $MontoServ;*/
		$QueryH = mysql_query("select * from seguro_trans_history   
	WHERE id_trans ='" . $row['id'] . "'");
		while ($RowHist = mysql_fetch_array($QueryH)) {

			if ($RowHist['tipo'] == 'serv') {
				$montoServAdc += $RowHist['monto'];


				$html .= '     
        <tr>
			<td align="left" width="265">' . ServAdicHistory($RowHist['id_serv_adc']) . " - Incluido" . '</td>
            <td align="left">RD$ ' . FormatDinero($RowHist['monto']) . '</td>
		</tr>';
			} else {
				$montoSeguro = $RowHist['monto'];
			}
		}



		$html .= '
	  	
    </table>
	
	
	
	
	</td>
  </tr>
  <tr>
    <td align="left" style="border-top:solid 2px #000; border-left:solid 2px #000; border-bottom:solid 2px #000; padding-left:25px; padding-bottom:30px; padding-top:30px; background-color:#E0E0E0; margin-bottom:15px; margin-top:15px; " valign="middle">
	
	
	
	<table width="' . $anchoP . 'px;" cellpadding="7" cellspacing="0" border="0" >
	  	<tr>
			<td align="left" width="259"  valign="middle"><b>Prima Seguro Básico</b></td>
			<td align="left" valign="middle"><b>RD$ ' . FormatDinero($montoSeguro) . '</b></td>
		</tr>
	</table>
	
	
	
	
	</td>
    <td align="left" style="border-top:solid 2px #000; border-left:solid 2px #000; border-bottom:solid 2px #000; border-right:solid 2px #000; padding-left:25px; padding-bottom:30px; padding-top:30px; background-color:#E0E0E0; margin-bottom:15px; margin-top:15px; " valign="middle">
	
	
	
	<table width="' . $anchoP . 'px;" cellpadding="7" cellspacing="0">
	  	<tr>
			<td align="left" width="262" valign="middle"><b>Prima Servicios Adicionales</b></td>
			<td align="left" valign="middle"><b>RD$ ' . FormatDinero($montoServAdc) . '</b></td>
		</tr>
	</table>
	
	
	
	</td>
  </tr>
  
  <tr>
  	<td colspan="2" align="right" height="25"  style="font-size:35px">
    	<strong>Total Póliza              RD$ ' . FormatDinero($montoSeguro + $montoServAdc) . '</strong>      
    </td>
  </tr>
   <tr style="color: #1760a5;
    font-weight: bold;
    font-size: 42px; " valign="top">
    	<td>
			<font style="font-size:30px"> Servicio al Cliente</font> 
			<img src="https://multiseguros.com.do/SegurosChat/images/VIA/4.jpg" height="37" />
			<br> +1 809 200 1842
		</td>
        <td valign="middle" align="left"> 
			<div style="padding-top:10px margin-left:7px; height:25px !important; font-size: 44px;">
				ES F&Aacute;CIL, ES VIA.
			</div>
		</td>
    </tr>
 
  <tr>
  	<td colspan="2">&nbsp;</td>
  </tr>
  <tr>
  	<td colspan="2">&nbsp;</td>
  </tr>
  
  
   <tr>
  	<td colspan="2" align="center" style="border-style: dashed;">


      
		<table align="center" cellpadding="2" style="margin-top:24px; " width="625px">
                
				<tr>
					<td style="border-right-style: dashed; border-left-style: dashed; border-top-style: dashed; border-bottom-style: dashed;"> 
					
					
					<table align="center" cellpadding="3" border="0" style="font-size:28.2px;">
    <tr style="margin-top:8px">
        <td align="left" ><img src="' . $logo . $NombreImg . '"  alt="" width="130px"/></td>
        <td align="left" ><img src="' . $directorio . '/logo.png" alt="" width="130px"/></td>
    </tr>
    <tr>
        <td align="left" width="97"><b>NO. POLIZA:</b></td>
        <td align="left" width="217"><b>' . $poliza . '</b></td>
    </tr>
    <tr>
        <td align="left"><b>NOMBRES:</b></td>
        <td align="left" ><b>' . $RQClient['asegurado_nombres'] . ' ' . $RQClient['asegurado_apellidos'] . '</b></td>
    </tr>
    <tr>
        <td align="left"><b>VEHICULO:</b></td>
        <td align="left"><b>' . TipoVehiculo($RQVehi['veh_tipo']) . ' ' . VehiculoMarca($RQVehi['veh_marca']) . '</b></td>
    </tr>
	<tr>
        <td align="left"><b>AÑO:</b></td>
        <td align="left"><b>' . $RQVehi['veh_ano'] . '</b></td>
    </tr>
    <tr>
        <td align="left"><b>CHASSIS:</b></td>
        <td align="left"><b>' . $RQVehi['veh_chassis'] . '</b></td>
    </tr>
    <tr>
        <td align="left"><b>VIGENCIA:</b></td>
        <td align="left">
			<b style="font-size:18px">DESDE</b> 
			<b style="font-size:22px">' . FechaListPDFn($row['fecha_inicio']) . '</b> 
			<b style="font-size:18px">HASTA</b> 
			<b style="font-size:22px">' . FechaListPDFin($row['fecha_fin']) . '</b>
		</td>
    </tr>
    <tr>
        <td align="left"><b>FIANZA JUDICIAL:</b></td>
        <td align="left"><b>RD$ ' . $fj . '</b></td>
    </tr>
</table>
					
					
					
					</td>
					<td  style="border-right-style: dashed; border-left-style: dashed; border-top-style: dashed; border-bottom-style: dashed;"> 
					
					
					
					
<table align="center" cellpadding="2" border="0"  width="625px">
  <tr>
    <td colspan="2" style="font-size:18px" align="left">
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
<b>Comuníquese con la aseguradora antes de iniciar cualquier trámite</b><br>
    </td>
  </tr>

<tr>
	<td colspan="2" align="left"><img src="' . $logo . $NombreImg . '"  alt="" width="100px"/></td>
</tr>

<tr>
  <td align="left" style="font-size:19px;">
  
  <table cellpadding="1" width="135px">
';

		$Descp = mysql_query("select * from ticket_poliza WHERE id_aseg ='" . $row['id_aseg'] . "' LIMIT 4");
		while ($rDescp = mysql_fetch_array($Descp)) {
			$html .= '     
        <tr>
			<td align="left">' . $rDescp['ciudad'] . '</td>
            <td align="left">' . $rDescp['telefono'] . '</td>
		</tr>';
		}
		$html .= ' 
 
 </table>
 
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
  	<td colspan="2">&nbsp;</td>
  </tr>
  <tr>
  	<td colspan="2">&nbsp;</td>
  </tr>
  <tr>
  	<td colspan="2">&nbsp;</td>
  </tr>
  
</table>






</td>
	</tr>

</table>


';
		// set font


	}
}
// * * * Direccion del Archivo

if ($html !== '0') {
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	//set auto page breaks
	//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->SetAutoPageBreak(TRUE, 0);
	$pdf->setCellPaddings(0, 0, 0, 0);
	//$pdf->SetFooterMargin(0);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setLanguageArray($l);
	$pdf->AddPage();

	$pdf->writeHTML($html, true, 0, true, false, '');
	$pdf->lastPage();
	//$carpeta = 'PDF/ASEGURADORA/'.$_GET['id_aseg'].'';
	$carpeta = 'PDF/CLIENTES/' . $_GET['user_id'] . '';

	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}


	$_GET['ruta_id'] = str_replace(" ", "-", $_GET['ruta_id']);
	if ($_GET['ruta_id'] != '1todos') {
		$rutids = $_GET['ruta_id'];
	}

	$nombreFile = $Fech_reg;
	//echo $nombreFile = $fecha1.$fecha2;

	$pdf->Output("PDF/CLIENTES/" . $_GET['user_id'] . "/" . $nombreFile . $rutids . ".pdf", 'F');
	$dir = "PDF/CLIENTES/" . $_GET['user_id'] . "/" . $nombreFile . $rutids . ".pdf";

	if (file_exists($dir)) { ?>

		<a href="javascript:void(0)" class="btn btn-success" onclick="location.replace('../ws_dev/TareasProg/Descargar.php?fecha=<?= $nombreFile . $rutids ?>&user_id=<?= $_GET['user_id'] ?>');"><b>Descargar Poliza</b></a>

<? }
}
//}

?>