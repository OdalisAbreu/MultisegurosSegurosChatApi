<?
	ini_set('display_errors',1);
	set_time_limit(0);
	include("../inc/conexion_inc.php");
	include("../inc/fechas.func.php");
	include("../inc/nombres.func.php");
	Conectarse();
	
// --------------------------------------------	
	if($_GET['fecha1']){
		$fecha1 = $_GET['fecha1'];
	}else{ 
		$fecha1 = fecha_despues(''.date('d/m/Y').'',-30);
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

function Ventas($id){
	global $fDesde, $fHasta, $fecha1, $fecha2;	
	
	$wFecha = "fecha >= '$fDesde 00:00:00' AND fecha < '$fHasta 23:59:59'";
	
	$dist_id = $id;
	
	
	 
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
	$rowsc=mysql_fetch_array($query);
	return $rowsc['veh_tipo']."|".$rowsc['veh_marca']."|".$rowsc['veh_modelo']."|".$rowsc['veh_ano']."|".$rowsc['veh_matricula']."|".$rowsc['veh_chassis'];
	
}

function Clientes($id){
	$query=mysql_query("
	SELECT * FROM  seguro_clientes
	WHERE id='".$id."' LIMIT 1");
	$row=mysql_fetch_array($query);
	return $row['asegurado_nombres']."|".$row['asegurado_apellidos']."|".$row['asegurado_cedula']."|".$row['asegurado_direccion']."|".$row['ciudad']."|".$row['asegurado_telefono1'];
	
}

function Tipos($id){
	$queryt=mysql_query("SELECT * FROM  seguro_tarifas WHERE veh_tipo='".$id."' LIMIT 1");
	$rowt=mysql_fetch_array($queryt);
	return $rowt['nombre']."|".$rowt['dpa']."|".$rowt['rc']."|".$rowt['rc2']."|".$rowt['fj']."|".$rowt['id_serv_rep'];
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

/*function GetPrefijo($id){
	$queryp=mysql_query("SELECT * FROM  seguros WHERE id='".$id."' LIMIT 1");
	$rowp=mysql_fetch_array($queryp);
	return $rowp['prefijo'];
}*/

///-------------------------------------------------	
/*function Cedula($id){
  	$cedula = str_replace("-","",$id);
	$in  = $cedula;
  	return substr($in,0,3)."-".substr($in,3,-1)."-".substr($in,-1);
}*/

///-------------------------------------------------	
function Telefono($id){
  $telefono = str_replace("-","",$id);
  $in  = $telefono;
  return substr($in,0,3)."-".substr($in,3,3)."-".substr($in,-4);
}


/*function FechaReporte($id){
	$clear1 = explode(' ',$id);
	  return $clear1[0];
}*/

function Ciudad($id){
	$queryp1=mysql_query("SELECT * FROM  ciudad WHERE id='".$id."' LIMIT 1");
	$rowp1=mysql_fetch_array($queryp1);
	
	$queryp2=mysql_query("SELECT * FROM  municipio WHERE id='".$rowp1['id_muni']."' LIMIT 1");
	$rowp2=mysql_fetch_array($queryp2);
	
	$queryp3=mysql_query("SELECT * FROM   provincia WHERE id='".$rowp2['id_prov']."' LIMIT 1");
	$rowp3=mysql_fetch_array($queryp3);
	
	return $rowp3['descrip'];
	
}



function Precio($id,$vigencia){
	$qprec=mysql_query("SELECT veh_tipo, 3meses, 6meses, 12meses FROM  seguro_tarifas WHERE veh_tipo='".$id."' LIMIT 1");
	while($rprec=mysql_fetch_array($qprec)){
		if($vigencia=='3')	return $rprec['3meses']; 	
		if($vigencia=='6') 	return $rprec['6meses']; 	
		if($vigencia=='12')	return $rprec['12meses']; 	
	}
}


function MontoServicio($id,$vigencia){
	//echo "Vig: ".$vigencia;
		$r6 = mysql_query("SELECT id, 3meses, 6meses, 12meses FROM servicios WHERE id='".$id."' ");
		//echo "SELECT id, 3meses, 6meses, 12meses FROM servicios WHERE id='".$id."' ";
		   while($row6=mysql_fetch_array($r6)){
			  if($vigencia ==3)  return $row6['3meses'];
			  if($vigencia ==6)  return $row6['6meses'];
			  if($vigencia ==12) return $row6['12meses']; 
		 }	
	}
	
function MontoServicioRep($idserv,$vigencia,$tipo){
	$qprec2=mysql_query("SELECT veh_tipo, id_serv_rep FROM  
	seguro_tarifas WHERE veh_tipo='".$tipo."'");
	$rprec2=mysql_fetch_array($qprec2);
	
	
	//echo "<b>ID:</b> ".$idSerRep = $rprec2['id_serv_rep']."<br>";
		
	
		$SeOpcional =  explode("-", $rprec2['id_serv_rep']);
		for($i =0; $i < count($SeOpcional); $i++){
			if($idserv==$SeOpcional[$i] ){
				//echo "ES IDENTICO<BR>";
				$MontoServiciod += MontoServicio($SeOpcional[$i],$vigencia);
			}
		}
		
		return $MontoServiciod;
		
			
	}
	
	
	function SerfdedEed($serv_adc,$vigencia,$tipo){
		
		$ServOpcional =  explode("-", $serv_adc);
			for($i =0; $i < count($ServOpcional); $i++){
				/*echo "<br>id:".$i."==> ".$ServOpcional[$i]."<br>";
				echo "<br>vjg:".$i."==> ".$vigencia."<br>";
				echo "<br>tipo:".$i."==> ".$tipo."<br>";*/
				 $MontoServiciosde += MontoServicioRep($ServOpcional[$i],$vigencia,$tipo);
				//echo "<br>===============================<br>";
				
			}
			return $MontoServiciosde;
		
	}
	
 $html .='   
  <table cellpadding="4" cellspacing="0">
  	
	<tr>
		<td colspan="21"> 
		
		
		
		<table width="100%" cellpadding="9" cellspacing="0">
	<tr>
    	<td colspan="8">
		
		<b style="font-size: 70px; color: #d9261c;">Multi</b><b style="font-size: 70px; color: #828282;">Seguros 
			</b>	
			</td>
    	
   
	  <td align="center" colspan="13">
		  <font style="font-size: 24px; color: #828282; font-weight: bold;">
		  	<b>REPORTE DIARIO DE VENTAS</b>
		  </font>
		  
		  <br>
		  <font style="font-size: 18px; color: #828282; font-weight: bold;">
		  	'.NombreProgS($dist_id).'
		  <font><br>
		  <font style="font-size: 14px; color: #828282; font-weight: bold;">
		  	<b>Desde:</b> '.$fecha1.' <b>Hasta:</b> '.$fecha2.'
			</font>
	  </td>
  </tr>
	
</table>


		</td>
	</tr>
	
	
   <tr style="background-color:#B1070A; color:#FFFFFF; font-size:14px;">
   		<td>#</td>
    	<td>No.Poliza</td>
    	<td>Aseguradora</td>
        <td>Nombres</td>
        <td>Apellidos</td>
        <td>C&eacute;dula</td>
        <td>Direcci&oacute;n</td>
        <td>Ciudad</td>
        <td>Tel&eacute;fono</td>
        <td>Tipo</td>
        <td>Marca</td>
        <td>Modelo</td>
        <td>A&ntilde;o</td>
        <td>Chassis</td>
        <td>Placa</td>
        <td>Fecha Emisi&oacute;n</td>
        <td>Inicio Vigencia</td>
        <td>Fin Vigencia</td>
        <td>Prima</td>
   </tr> ';
    
	
	$qR=mysql_query("SELECT * FROM seguro_transacciones_reversos WHERE id !=''");
	 while($rev=mysql_fetch_array($qR)){ 
	    $reversadas .= "[".$rev['id_trans']."]";
	 }
	 
	$w_user = "(
	serv_adc LIKE '%".$dist_id."-%'
	";
	
	// PUNTOS DE VENTAS
	$quer1 = mysql_query("
	SELECT id FROM servicios WHERE id_suplid ='".$dist_id."'");
	while($u=mysql_fetch_array($quer1)){
	
		$w_user .= " OR serv_adc LIKE '%".$u['id']."-%'";
	
		$quer2 = mysql_query("
		SELECT id FROM servicios WHERE id_suplid ='".$u['id']."'"); 
		while($u2=mysql_fetch_array($quer2)){
		$w_user .= " OR serv_adc LIKE '%".$u2['id']."-%'";	
		}
	
	}
	$w_user .= " )";
	
	$quer1 = mysql_query("SELECT * FROM seguro_transacciones WHERE $w_user AND $wFecha order by id ASC");
	//echo "<b>CONSULTA INTERNA</b> SELECT * FROM seguro_transacciones WHERE $w_user AND $wFecha order by id ASC";
	while($u=mysql_fetch_array($quer1)){
	
	$t++;
	
	//DATOS DEL VEHICULO
	$veh			 =  explode("|", Vehiculo($u['id_vehiculo']));
		print_r($veh);				
	$MServ		 	 = SerfdedEed($u['serv_adc'],$u['vigencia_poliza'],$veh[0]);
	$precioInic 	 	 = Precio($veh[0],$u['vigencia_poliza']);
	$precio	 	 	 = $precioInic + $MServ;
	$Tprecio 	 	+= $precio; 	
	
	$tipo 			=  explode("|", Tipo($veh[0]));
	$id_serv_rep 	= $tipo[5];  //id para tomar monto para el reporte en los servicios
	
	$marca 			= Marcas($veh[1]);
	$modelo 			= Modelos($veh[2]);
	$cliente 		=  explode("|", Clientes($u['id_cliente']));
	$pref 			= GetPrefijo($u['id_aseg']);
	$idseg 			= str_pad($u['id_poliza'], 6, "0", STR_PAD_LEFT);
	$prefi 			= $pref."-".$idseg;
	
	$tipo[1] 		= substr(formatDinero($tipo[1]), 0, -3);
	$tipo[2] 		= substr(formatDinero($tipo[2]), 0, -3);
	$tipo[3] 		= substr(formatDinero($tipo[3]), 0, -3);
	$tipo[4] 		= substr(formatDinero($tipo[4]), 0, -3);
	$precio 			= substr(formatDinero($precio), 0, -3);
	
	

$html .='
	<tr>
   		<td>'.$t.'</td>
        <td>'.$prefi.'</td>
		<td>'.NombreSeguroS($u['id_aseg']).'</td>
        <td>'.$cliente[0].'</td>
        <td>'.$cliente[1].'</td>
        <td>'.Cedula($cliente[2]).'</td>
        <td>'.$cliente[3].'</td>
        <td>'.Ciudad($cliente[4]).'</td>
        <td>'.Telefono($cliente[5]).'</td>
        <td>'.$tipo[0].'</td>
        <td>'.$marca.'</td>
        <td>'.$modelo.'</td>
        <td>'.$veh[3].'</td>
        <td>'.$veh[5].'</td>
        <td align="right">'.$veh[4].'</td>
        <td align="right">'.$u['fecha'].'</td>
        <td align="right">'.FechaReporte($u['fecha_inicio']).'</td>
        <td align="right">'.FechaReporte($u['fecha_fin']).'</td>
        
        <td align="right">'.$precio.'</td>     
   </tr>'; 
		 
		  

    }
	
	
$html .='
<tr>
	<td colspan="17"></td>
	<td colspan="4"><h4>Total de primas</h4></td>
	<td><h4>'.formatDinero($Tprecio).'</h4></td>
</tr>';
$html .='</table>';
	
	$carpeta = 'Excel/SUPLIDORES/'.$dist_id.'';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}
	
	$sfile	= "Excel/SUPLIDORES/".$dist_id."/MS_RDV_$fDesde.xls"; // Ruta del archivo a generar 

	$fp		= fopen($sfile,"w");
	
	fwrite($fp,$html); 
	fclose($fp); 
	
	echo $html;
  
  
 } 




$w_user = "(
	serv_adc LIKE '%".$_GET['id_suplid']."-%'
	";
	
	// PUNTOS DE VENTAS
	$quer1 = mysql_query("
	SELECT id FROM servicios WHERE id_suplid ='".$_GET['id_suplid']."'");
	while($u=mysql_fetch_array($quer1)){
	
		$w_user .= " OR serv_adc LIKE '%".$u['id']."-%'";
	
		$quer2 = mysql_query("
		SELECT id FROM servicios WHERE id_suplid ='".$u['id']."'"); 
		while($u2=mysql_fetch_array($quer2)){
		$w_user .= " OR serv_adc LIKE '%".$u2['id']."-%'";	
		}
	
	}
	$w_user .= " )";
	
	
$sqaws =mysql_query("SELECT * FROM seguro_transacciones WHERE $w_user AND $wFecha2 limit 1");
//echo "<b>CONSULTA EXTERNA</b> SELECT * FROM seguro_transacciones WHERE $w_user AND $wFecha2 limit 1";
	$paws=mysql_fetch_array($sqaws);
	
	if($paws['id']>1){
		//echo $paws['id'];
		Ventas($_GET['id_suplid']);
		
		
	}
	
	

?>

