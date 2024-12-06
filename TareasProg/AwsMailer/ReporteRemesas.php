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
	
	$qnh=mysql_query("SELECT * FROM remesas WHERE id ='".$dist_id."' LIMIT 1");
	///echo "SELECT * FROM remesas WHERE id ='".$dist_id."' LIMIT 1";
    $mnb=mysql_fetch_array($qnh);
	$edd = explode(' ',$mnb['fecha_desde']);
	$fp  = explode(' ',$mnb['fecha_pago']);
	$fp2  = explode('-',$fp[0]);
	$fecha_pago = $fp2[2]."-".$fp2[1]."-".$fp2[0];
	
	if($mnb['tipo_serv']=='prog'){
		echo $nombre = NombreProgS($mnb['id_aseg']);
	}else{
		echo $nombre = NombreSeguroS($mnb['id_aseg']);
	}
	$a = str_pad($mnb['num'], 4, "0", STR_PAD_LEFT);
	$num_remesa = Sigla($mnb['id_aseg']).'-'.$mnb['year'].'-'.$a;
		  
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
	
	$desg = explode(",", $mnb['email']);
	$cant = count($desg); 
	$cant = $cant -1;
	 
	 for ($i = 0; $i <= $cant; $i++) {
			$mail->AddAddress("".$desg[$i]."", "");
			echo "<script>console.log( 'Debug Objects email : " . $desg[$i] . "' );</script>";
	 }
	 
	 
	$mail->addAddress('grullon.jose@gmail.com');
	
	$archivo = '/ws6_3_8/TareasProg/Excel/ASEGURADORA/REMESAS/'.$mnb['id_aseg'].'/MS_RDR_'.$edd[0].'.xls';
	$archivo = realpath(__DIR__.'/../../../').$archivo; 
	$mail->AddAttachment($archivo);
	
	
	$mail->WordWrap = 50; 
	$mail->Subject = 'Pago remesa de '.$nombre.' No. '.$num_remesa.' '; 
	$mail->Body    = 'para ver el mensaje necesita HTML.';
	$mail->IsHTML(true);  
	
	//$mail->AddAttachment($sfile);// <--- Adjuntando archivo
	
	if(date("H")>12){
		$texto = "Buenas Tardes";
	}else{
		$texto = "Buenos Dias";
	}
	
	//$mail->SMTPDebug  = 2;
	$mail->MsgHTML(
		"
		
		".$texto.", estimado cliente:
		<p>
		<br />

El balance fue transferido de la cuenta del ".NombreBancoRep($mnb['banc_emp'])." a la cuenta del ".NombreBancoSuplidoresRep($mnb['banc_benef'])." a nombre de ".NombreSuplidoresRep($mnb['banc_benef']).", con el No. de remesa <b>".$num_remesa."</b> y el documento <b>#".$mnb['num_doc']."</b>, depositado el dia <b>".$fecha_pago."</b>



<p>
<b>Descripci&oacute;n del documento:</b><br>".$mnb['descrip']."
<p><p>
Este mensaje puede contener información privilegiada y confidencial. Dicha información es exclusivamente para el uso del individuo o entidad al cual es enviada. Si el lector de este mensaje no es el destinatario del mismo, queda formalmente notificado que cualquier divulgación, distribución, reproducción o copiado de esta comunicación está estrictamente prohibido. Si este es el caso, favor de eliminar el mensaje de su computadora e informar al emisor a través de un mensaje de respuesta. <br />

<br />
<br />
 MultiSeguros");

	if(!$mail->send()) {
		//echo 'Message could not be sent.';
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
		return "15/error enviando/15";
		
	} else {
		//echo 'Message has been sent';
		return "00/mensaje enviado/00"; 
	}

}
	
	
	
	/*$sq =mysql_query("SELECT * FROM privilegios WHERE privilegios LIKE '%1%' ");
	//echo "SELECT * FROM seguros WHERE id='".$_GET['id_aseg']."' AND activo ='si'";
	$p=mysql_fetch_array($sq);*/

if($_GET['key']=='Ed4F45%'){	
	$html = file_get_contents(
	"http://127.0.0.1/ws2/TareasProg/AwsMailer/ReporteRemesas.php");
	enviarEmailHtml($html,$_GET['id']);
	exit();
}
