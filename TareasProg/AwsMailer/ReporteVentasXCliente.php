<?php

date_default_timezone_set('America/Anguilla');
ini_set('display_errors', 1);
set_time_limit(0);

include("../../inc/conexion_inc.php");
include("../../inc/nombres.func.php");
include("../../inc/fechas.func.php");
Conectarse();


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


function Ventas($id)
{
	global $fdesde, $fhasta, $fdesdeRep;

	$wFecha 	= "AND fecha >= '$fdesde 00:00:00' AND fecha <= '$fhasta 23:59:59'";

	$qR = mysql_query("SELECT * FROM seguro_transacciones_reversos WHERE id !=''");
	$reversadas .= "0";
	while ($rev = mysql_fetch_array($qR)) {
		$reversadas .= "[" . $rev['id_trans'] . "]";
		//$reversadas 	.= ",".$rev['id_trans'];
	}

	$w_user = "(
	user_id='" . $id . "'";

	// PUNTOS DE VENTAS
	$quer1 = mysql_query("
	SELECT id FROM personal WHERE id_dist ='" . $id . "'");
	while ($u = mysql_fetch_array($quer1)) {

		$w_user .= " OR user_id='" . $u['id'] . "'";

		$quer2 = mysql_query("
		SELECT id FROM personal WHERE id_dist ='" . $u['id'] . "'");
		while ($u2 = mysql_fetch_array($quer2)) {
			$w_user .= " OR user_id='" . $u2['id'] . "'";
		}
	}
	$w_user .= " )";

	$query = mysql_query("
		   SELECT * FROM seguro_transacciones 
		   WHERE $w_user $wFecha order by id ASC");
	$trans = 0;
	while ($row = mysql_fetch_array($query)) {
		$trans++;
		if ((substr_count($reversadas, "[" . $row['id'] . "]") > 0)) {
			$Rtotal += $row['totalpagar'];
		} else {
			$total   += $row['monto'];
			$Ganancia1   += $row['ganancia1'];
		}
	}

	return $total . "/" . $Rtotal . "/" . $trans . "/" . $Ganancia1;
}


function enviarEmailHtml($html)
{

	require_once('ArcEmail/class.phpmailer.php');
	$mail = new PHPMailer(true);
	$mail->IsSMTP();
	try {
		http: //127.0.0.1/
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = "ssl";
		$mail->Host       = "mail.multiseguros.com.do";
		$mail->Port       = 465;
		$mail->Username   = "operaciones@multiseguros.com.do";  // Usuario Gmail
		$mail->Password   = "@x43RMcKh9@L";     // Contraseña Gmail
		$fecha 			= fecha_despues('' . date('d/m/Y') . '', -1);
		$mail->SetFrom("operaciones@multiseguros.com.do", "MultiSeguros");
		$mail->addAddress('grullon.jose@gmail.com');
		$mail->isHTML(true);
		$mail->Subject 	= 'Ventas Distribuidas el ' . $fecha;
		$mail->AltBody 	= 'para ver el mensaje necesita HTML.';

		$mail->MsgHTML("$html
	 probando el email");

		$mail->Send();

		echo 'Mensaje enviado a ' . $email . '';
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

if ($_GET['enviar'] == 1) {
	$html = file_get_contents("https://127.0.0.1/ws2/TareasProg/AwsMailer/ReporteVentasXCliente.php");
	enviarEmailHtml($html);
	exit();
}



?>
<table cellpadding="5" cellspacing="0" width="60%">
	<tr>
		<td colspan="6" align="center" style="font-size:22px"><b>Ventas por Distribuidores</b></td>
	</tr>
	<tr>
		<td colspan="6" align="center" style="font-size:15px">
			<b>Desde</b> <?= $fdesdeRep ?> <b>Hasta</b> <?= $fhastaRep ?>
		</td>
	</tr>
	<tr>
		<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>#</b></td>
		<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Nombre</b></td>
		<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Transacciones</b></td>
		<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Monto</b></td>
		<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Ganancia</b></td>
		<td style="background-color:#D11B1E; font-size:15px; color:#FFF"><b>Anulado</b></td>
	</tr>

	<?
	$Q1 = mysql_query("SELECT * FROM personal WHERE funcion_id='2' order by id ASC");
	while ($R1 = mysql_fetch_array($Q1)) {
		$t[] = $R1;
	}

	foreach ($t as $key => $row) {

		$vent = explode('/', Ventas($row['id']));
		$MontoTotal = $vent[0];
		$AnulaTotal = $vent[1];
		$TransTotal = $vent[2];
		$Ganancia1  = $vent[3];

		if ($AnulaTotal > 0) {
			$AnulTotal = $AnulaTotal;
		} else {
			$AnulTotal = 0;
		}

		if ($MontoTotal > 0) {
			$TotalMonto += $MontoTotal;
			$TotalAnul  += $AnulTotal;
			$TotalGan	+= $Ganancia1;


			?>

			<tr>
				<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?= $row['id'] ?></td>
				<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"> [ <b><?= $row['id'] ?></b> ]
					<?= $row['nombres'] ?> </td>
				<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><?= $TransTotal ?></td>
				<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD <?= formatDinero($MontoTotal) ?></td>
				<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD <?= formatDinero($Ganancia1) ?></td>
				<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;">$RD <?= formatDinero($AnulTotal) ?></td>
			</tr> ';

	<?
		}
	}

	?>


	<tr>
		<td colspan="3">&nbsp;</td>
		<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD <?= FormatDinero($TotalMonto) ?></b></td>
		<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD <?= FormatDinero($TotalGan) ?></b></td>
		<td style="border-bottom:solid 1px #E3E3E3; background-color:#F2F2F2;"><b>$RD <?= FormatDinero($TotalAnul) ?></b></td>
	</tr>
</table>