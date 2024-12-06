<?php


	ini_set('display_errors',1);
	set_time_limit(0);
	require 'PHPMailerAutoload.php';
	include("../../inc/conexion_inc.php");
	include("../../inc/nombres.func.php");
	include("../../inc/fechas.func.php");
	Conectarse(); 
	
	
		// exit();
function enviarEmailHtml($html,$dist_id){
	
	//explode
	$fech = fecha_despues(''.date('d/m/Y').'',-1);
	$ed = explode('/',$fech);
	$fechasd = $ed[2]."-".$ed[1]."-".$ed[0];
	$fecha1 = $ed[0]."-".$ed[1]."-".$ed[2];
	
	$mail = new PHPMailer;
	$mail->isSMTP();                                     
	$mail->Host = 'mail.multiseguros.com.do';  
	$mail->SMTPAuth = true;                             
	$mail->Username = 'operaciones@multiseguros.com.do';  
	$mail->Password = '@x43RMcKh9@L'; 
	$mail->SMTPSecure = 'tls'; 
	$mail->From = 'operaciones@multiseguros.com.do';
	$mail->FromName = 'MultiSeguros';
	$mail->SMTPDebug = true;
	
	
	$query=mysql_query("SELECT * FROM suplidores WHERE id_seguro ='".$dist_id."' LIMIT 1");
    $row=mysql_fetch_array($query);
   
    $desg = explode(",", $row['email_finanzas']);
	$cant = count($desg); 
	$cant = $cant -1;
	 
	$mail->addAddress('grullon.jose@gmail.com');

	echo $archivo = '/ws6_3_8/TareasProg/Excel/CLIENTES/'.$dist_id.'/MS_RDV_'.$fechasd.'.xls';
	$archivo = realpath(__DIR__.'/../../../').$archivo; 
	$mail->AddAttachment($archivo);
	
	
	$mail->WordWrap = 50; 
	$mail->Subject = 'Ventas de '.ClientePers($dist_id).' del '.$fecha1.' '; 
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
 MultiSeguros");

	if(!$mail->send()) {
		//echo 'Message could not be sent.';
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "15/error enviando/15";
		
	} else {
		//echo 'Message has been sent';
		echo realpath(__FILE__)."00/mensaje enviado/00"; 
	}

}
	
	
	
	$sq =mysql_query("SELECT * FROM privilegios WHERE privilegios LIKE '%1%' ");
	//echo "SELECT * FROM seguros WHERE id='".$_GET['id_aseg']."' AND activo ='si'";
	$p=mysql_fetch_array($sq);
		
	$html = file_get_contents(
	"http://127.0.0.1/ws2/TareasProg/AwsMailer/ReporteClientes.php");
	enviarEmailHtml($html,$p['id_pers']);
	exit();
