<?php

ini_set('display_errors', 0);
set_time_limit(0);
require 'PHPMailerAutoload.php';
include "../../inc/conexion_inc.php";
include "../../inc/nombres.func.php";
include "../../inc/fechas.func.php";
Conectarse();

// CONSULTANDO VENTAS
function TotalVentasV2($conf)
{
	$wFecha2 =
		"fecha >= '" .
		$conf['fech1'] .
		" 00:00:00'  AND  fecha <= '" .
		$conf['fech2'] .
		" 23:59:59'";

	$query = mysql_query("SELECT monto, costo, tipo, id_aseg, id_serv_adc
		FROM seguro_trans_history WHERE $wFecha2 ");
	$t = array();
	while ($r = mysql_fetch_array($query)) {
		if ($r['tipo'] == 'seg') {
			$t[$r['id_aseg']]['monto'] += $r['monto'];
			$t[$r['id_aseg']]['costo'] += $r['costo'];
		}

		if ($r['tipo'] == 'serv') {
			$t[$r['id_serv_adc']]['monto'] += $r['monto'];
			$t[$r['id_serv_adc']]['costo'] += $r['costo'];
		}
	}

	return $t;
}

// exit();
function enviarEmailHtml()
{
	//explode
	$fech1 = fecha_despues('' . date('d/m/Y') . '', -1);
	$fech2 = fecha_despues('' . date('d/m/Y') . '', -1);

	$ed1 = explode('/', $fech1);
	$ed2 = explode('/', $fech2);

	$fechasd = $ed1[2] . "-" . $ed1[1] . "-" . $ed1[0];

	$fdesde = $ed1[2] . "-" . $ed1[1] . "-" . $ed1[0];
	$fhasta = $ed2[2] . "-" . $ed2[1] . "-" . $ed2[0];

	$fdesdeRep = $ed1[0] . "-" . $ed1[1] . "-" . $ed1[2];
	$fhastaRep = $ed2[0] . "-" . $ed2[1] . "-" . $ed2[2];

	$mail = new PHPMailer();
	$mail->isSMTP();
	$mail->Host = 'multiseguros.com.do';
	$mail->SMTPAuth = true;
	$mail->Username = 'operaciones@multiseguros.com.do';
	$mail->Password = '@x43RMcKh9@L';
	$mail->SMTPSecure = 'ssl';
	$mail->From = 'operaciones@multiseguros.com.do';
	$mail->FromName = 'MultiSeguros';
	$mail->Port = '465';
	$mail->SMTPDebug = true;

	$mail->addAddress('grullon.jose@gmail.com');

	$mail->WordWrap = 50;
	$mail->Subject = 'Reporte de ventas detallado del ' . $fechasd . ' ';
	$mail->Body = 'para ver el mensaje necesita HTML.';
	$mail->IsHTML(true);

	// BUSCAMOS VENTAS DE TODOS:
	$UserData = TotalVentasV2($c = array('fech1' => $fdesde, 'fech2' => $fhasta));

	$html =
		'<table cellpadding="5" cellspacing="0" width="60%"> 
	<tr>
    	<td colspan="3" align="center" style="font-size:22px"><b>Ventas de Seguros</b></td>
    </tr>
	<tr>
    	<td colspan="3" align="center" style="font-size:15px">
		<b>Desde</b>
		' .
		$fdesdeRep .
		'
		<b>Hasta</b>
		' .
		$fhastaRep .
		'
		</td>
    </tr>
    <tr>
    	<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Nombre</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Monto</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Costo</b></td>
    </tr>';
	$sq = mysql_query("SELECT id FROM  seguros WHERE id !='' ");

	$Totalmonto = 0;
	$Totalcosto = 0;

	while ($p = mysql_fetch_array($sq)) {
		if ($UserData[$p['id']]['monto'] > 0) {
			$Totalmonto += $UserData[$p['id']]['monto'];
			$Totalcosto += $UserData[$p['id']]['costo'];

			$html .=
				'<tr>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">' .
				NomAseg($p['id']) .
				'</td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD ' .
				FormatDinero($UserData[$p['id']]['monto']) .
				'</td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD ' .
				FormatDinero($UserData[$p['id']]['costo']) .
				'</td>
</tr>';
		}
	}

	$html .=
		' <tr>
    	<td>&nbsp;</td>
        <td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD ' .
		FormatDinero($Totalmonto) .
		'</b></td>
        <td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"s><b>$RD ' .
		FormatDinero($Totalcosto) .
		'</b></td>
    </tr>
</table>
<br><br>';

	$html .=
		'<table cellpadding="5" cellspacing="0" width="60%" id="servopc" style="display:none;"> 
	<tr>
    	<td colspan="3" align="center" style="font-size:22px"><b>Ventas de Servicios Opcionales</b></td>
    </tr>
	<tr>
    	<td colspan="3" align="center" style="font-size:15px">
		<b>Desde</b>
		' .
		$fdesdeRep .
		'
		<b>Hasta</b>
		' .
		$fhastaRep .
		'
		</td>
    </tr>
    <tr>
    	<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Nombre</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Monto</b></td>
        <td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Costo</b></td>
    </tr>';
	$sq = mysql_query("SELECT id FROM  servicios WHERE id !='' ");

	$TotalmontoSer = 0;
	$TotalcostoSer = 0;

	while ($p = mysql_fetch_array($sq)) {
		$o = '0';
		if ($UserData[$p['id']]['monto'] > 0) {
			$o++;

			$TotalmontoSer += $UserData[$p['id']]['monto'];
			$TotalcostoSer += $UserData[$p['id']]['costo'];
			$html .=
				' <tr>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">' .
				ServAdicHistory($p['id']) .
				'</td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD ' .
				FormatDinero($UserData[$p['id']]['monto']) .
				'</td>
    <td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD ' .
				FormatDinero($UserData[$p['id']]['costo']) .
				'</td>
</tr>';
		}
	}

	if ($o > 0) {
		echo '<script> $("#servopc").show(0); </script>';
	} else {
		echo '<script> $("#servopc").hide(0); </script>';
	}

	$html .=
		'<tr>
    	<td>&nbsp;</td>
        <td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD ' .
		FormatDinero($TotalmontoSer) .
		'</b></td>
        <td  style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD ' .
		FormatDinero($TotalcostoSer) .
		'</b></td>
    </tr>
</table>';

	echo $html;
	$mail->MsgHTML($html);
	if (!$mail->send()) {
		//echo 'Message could not be sent.';
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "15/error enviando/15";
	} else {
		//echo 'Message has been sent';
		echo realpath(__FILE__) . "00/mensaje enviado/00";
	}
}

enviarEmailHtml();
exit();
