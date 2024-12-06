<?php


ini_set('display_errors', 1);
set_time_limit(0);
require 'PHPMailerAutoload.php';
include("../../inc/conexion_inc.php");
include("../../inc/nombres.func.php");
include("../../inc/fechas.func.php");
Conectarse();


//INFO
$fechaw = fecha_despues('' . date('d/m/Y') . '', -1);
$edaw = explode('/', $fechaw);
$fechasdaw = $edaw[2] . "-" . $edaw[1] . "-" . $edaw[0];

// exit();
function enviarEmailHtml($html, $dist_id)
{

	//explode
	$fech = fecha_despues('' . date('d/m/Y') . '', -1);
	$ed = explode('/', $fech);
	$fechasd = $ed[2] . "-" . $ed[1] . "-" . $ed[0];
	$fecha1 = $ed[0] . "-" . $ed[1] . "-" . $ed[2];

	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'mail.segurosexpress.com';
	$mail->SMTPAuth = true;
	$mail->Username = 'seguroschat@segurosexpress.com';
	$mail->Password = 'oCgYS@7yIaOO';
	$mail->SMTPSecure = 'ssl';
	$mail->From = 'seguroschat@segurosexpress.com';
	$mail->FromName = 'SegurosChat';
	$mail->Port = '465';
	$mail->SMTPDebug = true;

	$query = mysql_query("SELECT * FROM suplidores WHERE id ='" . $dist_id . "' LIMIT 1");
	$row = mysql_fetch_array($query);

	$desg = explode(",", $row['email_tecnico']);
	$cant = count($desg);
	$cant = $cant - 1;

	for ($i = 0; $i <= $cant; $i++) {
		$mail->AddAddress("" . $desg[$i] . "", "");
	}

	//$mail->addAddress('grullon.jose@gmail.com');
	//$fechasd = "2017-08-17";
	echo $archivo = '/ws6_3_8/TareasProg/Csv/ASEGURADORA/' . $dist_id . '/' . $fechasd . '.csv';
	$archivo = realpath(__DIR__ . '/../../../') . $archivo;
	$mail->AddAttachment($archivo);


	$mail->WordWrap = 50;
	$mail->Subject = 'Ventas de ' . NomAseg($dist_id) . ' del ' . $fecha1 . ' ';
	$mail->Body    = 'para ver el mensaje necesita HTML.';
	$mail->IsHTML(true);

	//$mail->AddAttachment($sfile);// <--- Adjuntando archivo

	//$mail->SMTPDebug  = 2;
	$mail->MsgHTML(
		"
		
		Buenos Dias, el archivo de las ventas esta anexado.
		<p>
		--------------------------------------------------------------------------------
<br /><br />
Este mensaje puede contener información privilegiada y confidencial. Dicha información es exclusivamente para el uso del individuo o entidad al cual es enviada. Si el lector de este mensaje no es el destinatario del mismo, queda formalmente notificado que cualquier divulgación, distribución, reproducción o copiado de esta comunicación está estrictamente prohibido. Si este es el caso, favor de eliminar el mensaje de su computadora e informar al emisor a través de un mensaje de respuesta. Las opiniones expresadas en este mensaje son propias del autor y no necesariamente coinciden con las de MultiSeguros.<br />
<br />
<br />
Gracias.<br />
<br />
<br />
 MultiSeguros"
	);

	if (!$mail->send()) {
		//echo 'Message could not be sent.';
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "15/error enviando/15";
	} else {
		//echo 'Message has been sent';
		echo realpath(__FILE__) . "00/mensaje enviado/00";
	}
}



//$sq =mysql_query("SELECT * FROM seguros WHERE id='".$_GET['id_aseg']."' AND activo ='si'");
//echo "SELECT * FROM seguros WHERE id='".$_GET['id_aseg']."' AND activo ='si'";
//$p=mysql_fetch_array($sq);

$sqaw = mysql_query("SELECT * FROM seguro_transacciones WHERE id_aseg='" . $_GET['id_aseg'] . "' order by id desc limit 1");
$paw = mysql_fetch_array($sqaw);

if ($paw['id']) {

	$html = file_get_contents(
		"http://127.0.0.1/ws2/TareasProg/AwsMailer/ReporteAseguradoraCSV.php"
	);
	enviarEmailHtml($html, $paw['id_aseg']);
	exit();
}
