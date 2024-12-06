<?
	ini_set('display_errors',0);
	set_time_limit(0);
	include("../../inc/conexion_inc.php");
	include("../../inc/fechas.func.php");
	include("../../inc/nombres.func.php");
	Conectarse();
	
	// --------------------------------------------	
	if($_GET['fecha1']){
		$fecha1 = $_GET['fecha1'];
	}else{ 
		$fecha1 = fecha_despues(''.date('d/m/Y').'',-1);
	}
	// --------------------------------------------
	if($_GET['fecha2']){
		$fecha2 = $_GET['fecha2'];
	}else{
		$fecha2 = fecha_despues(''.date('d/m/Y').'',-1);
	}
	// -------------------------------------------
	
   	$fd1		= explode('/',$fecha1);
	$fh1		= explode('/',$fecha2);
	$fDesde 	= $fd1[2].'-'.$fd1[1].'-'.$fd1[0];
	$fHasta 	= $fh1[2].'-'.$fh1[1].'-'.$fh1[0];
	
	$wFecha2 = "fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 23:59:59' ";

	//ID DE LA DOMINICANA
	$aseguradora ="AND id_aseg='3' ";
	
	$wFecha = "fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 23:59:59' ";
	 
	// --------------------- Index ID ------------------------ //
	$qIndex = mysql_query("SELECT id_inicio FROM indexa WHERE fecha ='".$fDesde."' ");
 	$Index	= mysql_fetch_array($qIndex);
	  if($Index['id_inicio']){
	     $wIndexId = "(id > ".$Index['id_inicio'].") AND ";
	   }
	// -------------------------------------------------------
	
	//PARA LOS REVERSOS
	$qR=mysql_query("SELECT id_trans FROM seguros_reversos WHERE $wFecha");
		  while($rev=mysql_fetch_array($qR)){ 
		  $reversadas .= "[".$rev['id_trans']."]";
    }
	
	
function Vehiculo($id){
	$query=mysql_query("
	SELECT * FROM  seguro_vehiculo
	WHERE id='".$id."' LIMIT 1");
	$row=mysql_fetch_array($query);
	return $row['veh_tipo']."|".$row['veh_marca']."|".$row['veh_modelo']."|".$row['veh_ano']."|".$row['veh_matricula']."|".$row['veh_chassis'];
	
}

function Clientes($id){
	$query=mysql_query("
	SELECT * FROM  seguro_clientes
	WHERE id='".$id."' LIMIT 1");
	$row=mysql_fetch_array($query);
	return $row['asegurado_nombres']."|".$row['asegurado_apellidos']."|".$row['asegurado_cedula']."|".$row['asegurado_direccion']."|".$row['ciudad']."|".$row['asegurado_telefono1'];
	
}



function Marcas($id){
	$querym=mysql_query("SELECT * FROM  seguro_marcas WHERE ID='".$id."' LIMIT 1");
	$rowm=mysql_fetch_array($querym);
	return $rowm['DESCRIPCION'];
}

function Modelos($id){
	$querymo=mysql_query("SELECT * FROM  seguro_modelos WHERE id='".$id."' LIMIT 1");
	$rowmo=mysql_fetch_array($querymo);
	return $rowmo['descripcion'];
}





///-------------------------------------------------	
function Telefono($id){
  $telefono = str_replace("-","",$id);
  $in  = $telefono;
  return substr($in,0,3)."-".substr($in,3,3)."-".substr($in,-4);
}


function Fecha($id){
	$clear1 = explode(' ',$id);  
	$fecha_vigente1 = explode('-',$clear1[0]); 
	   
	   if($fecha_vigente1[1] =='01'){ $mes ='Ene'; }
	   if($fecha_vigente1[1] =='02'){ $mes ='Feb'; }
	   if($fecha_vigente1[1] =='03'){ $mes ='Mar'; }
	   if($fecha_vigente1[1] =='04'){ $mes ='Abr'; }
	   if($fecha_vigente1[1] =='05'){ $mes ='May'; }
	   if($fecha_vigente1[1] =='06'){ $mes ='Jun'; }
	   if($fecha_vigente1[1] =='07'){ $mes ='Jul'; }
	   if($fecha_vigente1[1] =='08'){ $mes ='Ago'; }
	   if($fecha_vigente1[1] =='09'){ $mes ='Sep'; }
	   if($fecha_vigente1[1] =='10'){ $mes ='Oct'; }
	   if($fecha_vigente1[1] =='11'){ $mes ='Nov'; }
	   if($fecha_vigente1[1] =='12'){ $mes ='Dic'; }
	   return $Vard = $fecha_vigente1[2].'-'.$mes.'-'.substr($fecha_vigente1[0],-2);
}

function FechaHoraNew($id){
	$clear1 = explode(' ',$id);  
	$fecha_vigente1 = explode('-',$clear1[0]); 
	   
	   if($fecha_vigente1[1] =='01'){ $mes ='Ene'; }
	   if($fecha_vigente1[1] =='02'){ $mes ='Feb'; }
	   if($fecha_vigente1[1] =='03'){ $mes ='Mar'; }
	   if($fecha_vigente1[1] =='04'){ $mes ='Abr'; }
	   if($fecha_vigente1[1] =='05'){ $mes ='May'; }
	   if($fecha_vigente1[1] =='06'){ $mes ='Jun'; }
	   if($fecha_vigente1[1] =='07'){ $mes ='Jul'; }
	   if($fecha_vigente1[1] =='08'){ $mes ='Ago'; }
	   if($fecha_vigente1[1] =='09'){ $mes ='Sep'; }
	   if($fecha_vigente1[1] =='10'){ $mes ='Oct'; }
	   if($fecha_vigente1[1] =='11'){ $mes ='Nov'; }
	   if($fecha_vigente1[1] =='12'){ $mes ='Dic'; }
	   
	   $Hero = explode(':',$clear1[1]);
	   if($Hero[0] =='00'){ $hora = "12"; $t ='AM'; }
	   if($Hero[0] =='01'){ $hora = "1"; $t ='AM'; }
	   if($Hero[0] =='02'){ $hora = "2"; $t ='AM'; }
	   if($Hero[0] =='03'){ $hora = "3"; $t ='AM'; }
	   if($Hero[0] =='04'){ $hora = "4"; $t ='AM'; }
	   if($Hero[0] =='05'){ $hora = "5"; $t ='AM'; }
	   if($Hero[0] =='06'){ $hora = "6"; $t ='AM'; }
	   if($Hero[0] =='07'){ $hora = "7"; $t ='AM'; }
	   if($Hero[0] =='08'){ $hora = "8"; $t ='AM'; }
	   if($Hero[0] =='09'){ $hora = "9"; $t ='AM'; }
	   if($Hero[0] =='10'){ $hora = "10"; $t ='AM'; }
	   if($Hero[0] =='11'){ $hora = "11"; $t ='AM'; }
	   if($Hero[0] =='12'){ $hora = "12"; $t ='PM'; }
	   if($Hero[0] =='13'){ $hora = "1"; $t ='PM'; }
	   if($Hero[0] =='14'){ $hora = "2"; $t ='PM'; }
	   if($Hero[0] =='15'){ $hora = "3"; $t ='PM'; }
	   if($Hero[0] =='16'){ $hora = "4"; $t ='PM'; }
	   if($Hero[0] =='17'){ $hora = "5"; $t ='PM'; }
	   if($Hero[0] =='18'){ $hora = "6"; $t ='PM'; }
	   if($Hero[0] =='19'){ $hora = "7"; $t ='PM'; }
	   if($Hero[0] =='20'){ $hora = "8"; $t ='PM'; }
	   if($Hero[0] =='21'){ $hora = "9"; $t ='PM'; }
	   if($Hero[0] =='22'){ $hora = "10"; $t ='PM'; }
	   if($Hero[0] =='23'){ $hora = "11"; $t ='PM'; }
	   
	   
	   
	   
	   return $fecha_vigente1[2].'-'.$mes.'-'.substr($fecha_vigente1[0],-2)." ".$hora.":".$Hero[0]." ".$t;
}

function Ciudad($id){
	$queryp1=mysql_query("SELECT * FROM  ciudad WHERE id='".$id."' LIMIT 1");
	$rowp1=mysql_fetch_array($queryp1);
	
	$queryp2=mysql_query("SELECT * FROM  municipio WHERE id='".$rowp1['id_muni']."' LIMIT 1");
	$rowp2=mysql_fetch_array($queryp2);
	
	$queryp3=mysql_query("SELECT * FROM   provincia WHERE id='".$rowp2['id_prov']."' LIMIT 1");
	$rowp3=mysql_fetch_array($queryp3);
	
	return $rowp3['descrip'];
	
}

 $html .='   
  <table cellpadding="4" cellspacing="0">
  	
	<tr>
		<td colspan="21"> 
		
		
		
		<table width="100%" cellpadding="9" cellspacing="0">
	<tr>
    	<td colspan="6">
		
		<b style="font-size: 60px; color: #d9261c;">Multi</b><b style="font-size: 60px; color: #828282;">Seguros 
			</b>	
			</td>
    	
   
	  <td align="center" colspan="7">
		  <font style="font-size: 22px; color: #828282; font-weight: bold;">
		  	<b>REPORTE DIARIO DE VENTAS</b>
		  </font>
		  
		  <br>
		  <font style="font-size: 18px; color: #828282; font-weight: bold;">
		  	General de Seguros
		  <font><br>
		  <font style="font-size: 14px; color: #828282; font-weight: bold;">
		  	<b>Desde:</b> '.$fecha1.' <b>Hasta:</b> '.$fecha2.'
			</font>
	  </td>
	  <td align="center" colspan="4"> 
	  	<b style="font-size: 18px;">Fecha del Reporte</b><br>
		<font style="font-size: 14px; color: #828282; font-weight: bold;">
			'.FechaHoraNew(date("Y-m-d H:i:s")).'
		</font>
	  </td>
  </tr>
	
</table>


		</td>
	</tr>
	
	
   <tr style="background-color:#B1070A; color:#FFFFFF; font-size:14px;">
   		<td></td>
        <td>No. Poliza</td>
        <td>Nombres</td>
        <td>Apellidos</td>
        <td>C&eacute;dula</td>
        <td>Ciudad</td>
        <td>Tel&eacute;fono</td>
        <td>Tipo</td>
        <td>Marca</td>
        <td>Modelo</td>
        <td>A&ntilde;o</td>
        <td>Chassis</td>
        <td>Placa</td>
        <td>Fecha Emisi&oacute;n con hora</td>
		<td>Agencia</td>
        <td>Inicio Vigencia</td>
        <td>Fin Vigencia</td>
        <td>Prima</td>
   </tr> ';
    
	
	$quer1 = mysql_query("SELECT * FROM seguro_transacciones WHERE $wFecha $aseguradora order by id DESC");
	while($u=mysql_fetch_array($quer1)){
	
	$t++;
		
	$veh =  explode("|", Vehiculo($u['id_vehiculo']));	
	$tipo =  explode("|", Tipo($veh[0]));
	$marca = Marcas($veh[1]);
	$modelo = Modelos($veh[2]);
	$cliente =  explode("|", Clientes($u['id_cliente']));
	$pref = GetPrefijo($u['id_aseg']);
	$idseg = str_pad($u['id_poliza'], 6, "0", STR_PAD_LEFT);
	$prefi = $pref."-".$idseg;

	$SUMtotal +=$u['monto'];
$html .='
	<tr>
   		<td>'.$t.'</td>
        <td>'.$prefi.'</td>
        <td>'.$cliente[0].'</td>
        <td>'.$cliente[1].'</td>
        <td>'.CedulaPDF($cliente[2]).'</td>
        <td>'.Ciudad($cliente[4]).'</td>
        <td>'.Telefono($cliente[5]).'</td>
        <td>'.TipoVehiculo($veh[0]).'</td>
        <td>'.$marca.'</td>
        <td>'.$modelo.'</td>
        <td>'.$veh[3].'</td>
        <td>'.$veh[5].'</td>
        <td>'.$veh[4].'</td>
        <td>'.FechaHoraNew($u['fecha']).'</td>
		<td>'.$u['id'].'</td>
        <td>'.Fecha($u['fecha_inicio']).'</td>
        <td>'.Fecha($u['fecha_fin']).'</td>
        <td>$'.formatDinero($u['monto']).'</td>
   </tr>';
		 
		  

    }
	

$html .='
	<tr>
		<td colspan="17" align="right" style="font-size: 15px; color: #828282; font-weight: bold;">Total:</td>
        <td style="font-size: 15px; color: #828282; font-weight: bold;">$'.formatDinero($SUMtotal).'</td>
   </tr>
</table>';
	
	$carpeta = 'Excel/CLIENTES/'.$dist_id.'';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}
	
	//$sfile	= "Archivos/CLIENTES/Transacciones_$fDesde.xls"; // Ruta del archivo a generar 
	$sfile	= "EXCEL/GEN/MS_RDV_$fDesde.xls"; // Ruta del archivo a generar 

	$fp		= fopen($sfile,"w");
	
	fwrite($fp,$html); 
	fclose($fp); 
	
	echo $html;
    ?>








