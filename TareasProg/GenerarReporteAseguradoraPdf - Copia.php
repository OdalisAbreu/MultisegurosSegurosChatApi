<?php
	
	ini_set('display_errors',1);
	set_time_limit(0);
	include("../inc/conexion_inc.php");
	include("../inc/fechas.func.php");
	include("../inc/nombres.func.php");
	Conectarse();

	date_default_timezone_set('America/Santo_Domingo');
	require_once('tcpdf/config/lang/eng.php');
	require_once('tcpdf/tcpdf.php');

	$ancho = "690";
	$altura = "1100";
	
		$html = '<table width="'.$ancho.'px;" height="'.$altura.'px;" style="font-size:15px;" border="1" align="center" cellpadding="4" cellspacing="0">
<tr>
	<td colspan="3">
    	
        <table width="'.$ancho.'px;"> 
        	<tr>
            	<td width="63%"><strong>CERTIFICADO DE SEGURO - VEHICULOS     DE MOTOR</strong></td>
                <td width="13%">logo1</td>
                <td width="24%">logo2</td>
            </tr>
        </table>
        
    </td>
</tr>
  <tr>
    <td  valign="top"><strong>ASEGURADO: </strong></td>
    <td  valign="top"><strong>POLIZA NO.: </strong></td>
    <td  valign="top"><strong>ASEGURADORA:</strong></td> 
  </tr>
  <tr>
    <td  valign="top"><strong>DIRECCION:</strong></td>
    <td valign="top" colspan="2"><strong>FECHA DE EMISION:</strong></td>
  </tr>
  <tr>
    <td  valign="top"><strong>TELEFONO:</strong></td>
    <td valign="top" colspan="2"><strong>INICIO DE VIGENCIA:</strong></td>
  </tr>
  <tr>
    <td  valign="top"><strong>VENDEDOR:</strong></td>
    <td valign="top" colspan="2"><strong>FIN DE VIGENCIA:</strong></td>
  </tr>
  
  
   <tr>
    <td valign="top" colspan="3">
    	<hr style="font-size:1px; color:#DEDEDE">
    </td>
  </tr>
  
  <tr>
    <td valign="top" colspan="3" style="text-align:justify">
    	<p><strong>Términos y Partes </strong></p>
<p>En virtud del pago de la prima estipulada y basándose en las declaraciones y garantías expresas más abajo, la Aseguradora se obliga a indemnizar al asegurado hasta una cantidad que no exceda los límites que se consignan, por las pérdidas o daños por él sostenidos de hecho y por los riesgos que, según se explican es esta póliza, puedan sufrir o causar el vehículo que se descrito en la misma, mientras esté dentro del territorio de la República Dominicana y siempre que tales pérdidas o daños hayan sido sufridos por el Asegurado debido a accidentes dentro del período de tiempo comprendido entre el día y la hora señalados como inicio de vigencia y las doce (12) meridiano del día señalado como fin de fin de vigencia. Esta póliza solamente asegura contra aquellos riesgos por los cuales aparezca específicamente cargada una prima. </p>
<p>Este Certificado de Seguro está sujeto a todos los demás términos, cláusulas, endosos y condiciones de la póliza de Vehículos de Motor aprobados por la Superintendencia de Seguros y contemplados en la Ley 146-02 sobre Seguros y Fianzas, salvo sus excepciones y los servicios opcionales que son contratados con sus respectivos suplidores.</p>
<p>&nbsp;</p>
<p><strong>Declaraciones y Garantías por el Asegurado</strong></p>
<p>Las informaciones contenidas en este documento son las declaraciones y garantías suministradas por el asegurado, quien garantiza la exactitud y veracidad de las mismas y, basándose en ellas, la Aseguradora emite esta póliza, limitándose a aplicar las primas que correspondan con arreglo a dichas declaraciones.</p>
    </td>
  </tr>
  
  <tr>
    <td valign="top" colspan="3">
    	<hr style="font-size:1px; color:#DEDEDE">
    </td>
  </tr>
  
  <tr>
    <td valign="top" colspan="3">
    	<p align="center"><strong>PLAN BASICO DE LEY - CONDICIONES PARTICULARES</strong></p>
    </td>
  </tr>
   <tr>
    <td valign="top" colspan="3">
        <table width="'.$ancho.'px;" border="0" align="center" cellpadding="4" cellspacing="0">
  <tr>
    <td ><br>&nbsp;</td>
    <td  valign="bottom"><p><strong>TIPO :</strong></p></td>
    <td  valign="bottom"><p>&nbsp;</p></td>
    <td colspan="2" valign="bottom"><p><strong>AÑO :</strong></p></td>
    <td  valign="bottom"><p>&nbsp;</p></td>
    <td ><p>&nbsp;</td>
  </tr>
  <tr>
    <td ><p>&nbsp;</td>
    <td  valign="bottom"><p><strong>MARCA :</strong></p></td>
    <td  valign="bottom"><p>&nbsp;</p></td>
    <td colspan="2" valign="bottom"><p><strong>CHASSIS :</strong></p></td>
    <td  valign="bottom"><p>&nbsp;</p></td>
    <td ><p>&nbsp;</td>
  </tr>
  <tr>
    <td ><p>&nbsp;</td>
    <td  valign="bottom"><p><strong>MODELO :</strong></p></td>
    <td  valign="bottom"><p>&nbsp;</p></td>
    <td colspan="2" valign="bottom"><p><strong>REGISTRO :</strong></p></td>
    <td  valign="bottom"><p>&nbsp;</p></td>
    <td ><p>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" valign="top" style="border-top:solid 1px #DEDEDE; width:'.$ancho.'px;">COBERTURAS   Y LIMITES (En RD$)</p>
      <p>Daños a la Propiedad Ajena                                                   XXX,XXX<br>
        Lesiones Corporales o Muerte 1 Persona                               XXX,XXX<br>
        Lesiones Corporales o Muerte Más de 1 Persona                  XXX,XXX<br>
        Lesiones Corporales o Muerte 1 Pasajero                              XXX,XXX<br>
        Lesiones Corporales o Muerte Más de 1 Pasajero                 XXX,XXX<br>
        Accidentes Personales Conductor                                          XXX,XXX <br>
        Fianza Judicial                                                                           XXX,XXX </td>
    <td colspan="3" valign="top" style="border-top:solid 1px #DEDEDE; border-left:solid 1px #DEDEDE;"><p>SERVICIOS   ADICIONALES</p>
      <p>Aumento Fianza (Hasta 500 Mil) – Incluido             XXX,XXX <br>
        Asistencia Vial (Grúa) – Incluido                              XXX,XXX<br>
        Casa del Conductor – Incluido                                  XXX,XXX </p></td>
  </tr>
  <tr>
    <td colspan="4" style="border-top:solid 1px #DEDEDE; border-left:solid 1px #DEDEDE; border-bottom:solid 1px #DEDEDE; "><strong>Prima Seguro Básico                                        RD$XX,XXXX</strong></td>
    <td colspan="3" style="border-top:solid 1px #DEDEDE; border-left:solid 1px #DEDEDE; border-bottom:solid 1px #DEDEDE; border-right:solid 1px #DEDEDE;"><strong>Prima Servicios Adicionales                               RD$XX,XXX</strong></td>
  </tr>
</table>
        
    </td>
  </tr>
  
  <tr>
  	<td ></td>
    <td>
    	
        <table style="width:150px; height:150px; border:solid 1px #DEDEDE;">
        	<tr>
            	<td align="center">Imagen Firma Autorizada y Sello</td>
            </tr>
        </table>
       
        
        
    </td>
    <td>
    	<strong><u>Total Póliza            RD$ XX,XXX</u></strong>
    </td>
  </tr>
  
  <tr>
  	<td colspan="3" style="border-top:solid 1px #DEDEDE; border-left:solid 1px #DEDEDE; border-bottom:solid 1px #DEDEDE; border-right:solid 1px #DEDEDE; height: 80px;">Disclaimers & Other Info</td>
  </tr>
  
   <tr>
  	<td colspan="3" style="height: 20px;"></td>
  </tr>
  
  
  <tr>
  	<td align="center" colspan="3">
       
        	<table align="center" cellpadding="2" style="width:400px; border-width: 2px; border-style: dashed; border-color: gray; ">
                <tr>
                    <td align="center" colspan="2"><strong>Marbete     – Carnet Desprendible</strong></td>
                </tr>
                <tr>
                    <td width="185" align="left" >NO. POLIZA</td>
                    <td width="201" align="center" ></td>
                </tr>
				<tr>
                    <td align="left" >NOMBRES</td>
                    <td align="center" ></td>
                </tr>
            	<tr>
                    <td align="left" >TIPO VEHICULO</td>
                    <td align="center" ></td>
                </tr>
            	<tr>
                    <td align="left" >CHASSIS</td>
                    <td align="center" ></td>
                </tr>
                <tr>
                    <td align="left" >MARCA</td>
                    <td align="center" ></td>
                </tr>
                <tr>
                    <td align="left" >FIANZA JUDICIAL</td>
                    <td align="center" ></td>
                </tr>
        </table>
       
    </td>
  </tr>
  
</table>

		
		
		
		<table width="100%" align="center" cellspacing="0" >
	
    <tr>
    	<td align="left" style="background-color:#2196f3; color:#FFFFFF; padding-bottom: 5px;  padding-top: 5px; padding-left: 3px;">No. Poliza</td>
        <td align="center" style="background-color:#2196f3; color:#FFFFFF; padding-bottom: 5px;  padding-top: 5px; padding-left: 3px; width: 120px;">Nombres</td>
        <td align="center" style="background-color:#2196f3; color:#FFFFFF; padding-bottom: 5px;  padding-top: 5px; padding-left: 3px; width: 120px;">Fecha Emisi&oacute;n</td>
        <td align="center" style="background-color:#2196f3; color:#FFFFFF; padding-bottom: 5px;  padding-top: 5px; padding-left: 3px; width: 120px;">Inicio Vigencia</td>
        <td align="center" style="background-color:#2196f3; color:#FFFFFF; padding-bottom: 5px;  padding-top: 5px; padding-left: 3px; width: 120px;">Prima</td>
        <td align="center" style="background-color:#2196f3; color:#FFFFFF; padding-bottom: 5px;  padding-top: 5px; padding-left: 3px; width: 120px;">Total a Remesar</td>
    </tr>';
	$quer1 = mysql_query("SELECT * FROM seguro_transacciones order by id DESC LIMIT 10");
	while($row=mysql_fetch_array($quer1)){ 
	
	$html .='
    <tr style="font-size: 16px;">
        <td>'.$row['id'].'</td>
        <td>'.$row['user_id'].'</td>
        <td>'.$row['fecha'].'</td>
        <td>'.$row['fecha_inicio'].'<br>
        '.$row['fecha_fin'].'</td>
        <td>$'.$row['monto'].'</td>
		<td>$0.00</td>
   </tr>';
   // set font



	}
$html .='</table>'; 
	// * * * Direccion del Archivo
	 
	if($html !=='0'){
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
		$carpeta = 'PDF/ASEGURADORA/'.$_GET['id_aseg'].'';
		
		if (!file_exists($carpeta)) {
			mkdir($carpeta, 0777, true);
		}
			
		$nombreFile = 'Ventas_'.$_GET['id_aseg'].'_'.date('d-m-Y H:i:s');
		$pdf->Output("PDF/ASEGURADORA/".$_GET['id_aseg']."/$nombreFile.pdf", 'F');
		echo $nombreFile.".pdf";
}

?>