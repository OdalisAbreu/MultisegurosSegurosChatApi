<?php
ini_set('display_errors', 1);
set_time_limit(0);
require 'PHPMailerAutoload.php';
include("../../inc/conexion_inc.php");
include("../../inc/nombres.func.php");
include("../../inc/fechas.func.php");
Conectarse();

//echo "carpeta raiz: ".$_SERVER['DOCUMENT_ROOT']."<br>";
//INFO
$fechaw = fecha_despues('' . date('d/m/Y') . '', -1);
$edaw = explode('/', $fechaw);
$fechasdaw = $edaw[2] . "-" . $edaw[1] . "-" . $edaw[0];
$wFecha2 = "fecha >= '$fechasdaw 00:00:00' AND fecha <= '$fechasdaw 23:59:59' ";
// exit();


function enviarEmailHtml($dist_id)
{

	//explode
	$fech = fecha_despues('' . date('d/m/Y') . '', -1);
	$ed = explode('/', $fech);
	$fechasd = $ed[2] . "-" . $ed[1] . "-" . $ed[0];
	$fecha1 = $ed[0] . "-" . $ed[1] . "-" . $ed[2];

	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'mail.seguroschat.com';
	$mail->SMTPAuth = true;
	$mail->Username = 'operaciones@seguroschat.com';
	$mail->Password = '@x43RMcKh9@L';
	$mail->SMTPSecure = 'ssl';
	$mail->From = '	operaciones@seguroschat.com';
	$mail->FromName = 'SegurosChat';
	$mail->Port = '587';
	$mail->SMTPDebug = true;

	$query = mysql_query("SELECT * FROM suplidores WHERE id_seguro ='" . $dist_id . "' LIMIT 1");
	$row = mysql_fetch_array($query);

	$desg = explode(",", $row['email_finanzas']);
	$cant = count($desg);
	$cant = $cant - 1;
	if ($_GET['DEBUG']) {
		echo "DEBUG";
	} else {
		for ($i = 0; $i <= $cant; $i++) {
			$mail->AddAddress("" . $desg[$i] . "", "");
		}
	}
	$mail->addAddress('grullon.jose@gmail.com');
	$mail->AddBCC('odalisdabreu@gmail.com');

	/*if($dist_id=='1'){
		$carpeta = 'DOM';
	}else if($dist_id=='3'){
		$carpeta = 'GEN';
	}*/


	$archivo1 = '/ws6_3_8/TareasProg/Excel/ASEGURADORA/' . $dist_id . '/MS_RDV_' . $fechasd . '.xls';
	$archivo2 = "/excelFiles/$dist_id/MS_EM_$fechasd.xlsx";
	echo "$archivo1,$archivo2";


	$archivo1 = realpath(__DIR__ . '/../../../') . $archivo1;
	$archivo2 = realpath(__DIR__ . '/../../../') . $archivo2;

	if (!file_exists($archivo1) && !file_exists($archivo2)) {
		return;
	}

	$mail->AddAttachment($archivo1);
	$mail->AddAttachment($archivo2);

	$mail->WordWrap = 50;
	$mail->Subject = 'Ventas de ' . NomAseg($dist_id) . ' del ' . $fecha1 . ' ';
	$mail->Body    = 'para ver el mensaje necesita HTML.';
	$mail->IsHTML(true);


	//$mail->SMTPDebug  = 2;
	$mail->MsgHTML(
		"Buenos d&iacute;as, el archivo de las ventas esta anexado.
		<p>
		--------------------------------------------------------------------------------
<br /><br />
Este mensaje puede contener informaci&oacute;n privilegiada y confidencial. Dicha informaci&oacute;n es exclusivamente para el uso del individuo o entidad al cual es enviada. Si el lector de este mensaje no es el destinatario del mismo, queda formalmente notificado que cualquier divulgaci&oacute;n, distribuci&oacute;n, reproducci&oacute;n o copiado de esta comunicaci&oacute;n est&aacute; estrictamente prohibido. Si este es el caso, favor de eliminar el mensaje de su computadora e informar al emisor a trav&eacute;s de un mensaje de respuesta. Las opiniones expresadas en este mensaje son propias del autor y no necesariamente coinciden con las de SegurosChat.<br />
<br />
<br />
Gracias.<br />
<br />
<br />
 SegurosChat"
	);

	if (!$mail->send()) {
		echo "15/error enviando/15";
	} else {
		echo realpath(__FILE__) . "00/mensaje enviado/00";
	}
}



$sq = mysql_query("SELECT * FROM seguros WHERE activo ='si' ");
while ($p = mysql_fetch_array($sq)) {

	$sqaw = mysql_query("SELECT * FROM seguro_transacciones WHERE id_aseg='" . $p['id'] . "' AND $wFecha2 order by id desc limit 1");
	$paw = mysql_fetch_array($sqaw);

	if ($paw['id']) {
		enviarEmailHtml($p['id']);
	}
}
