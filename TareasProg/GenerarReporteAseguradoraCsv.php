<?
	ini_set('display_errors',0);
	set_time_limit(0);
	include("../inc/conexion_inc.php");
	include("../inc/fechas.func.php");
	include("../inc/nombres.func.php");
	Conectarse();
	


function Ventas($id){
	
	$dist_id = $id;
	
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
	
	
	 $wFecha = "fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59' AND ";
	 
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

function Tipo($id){
	$queryt=mysql_query("SELECT * FROM  seguro_tarifas WHERE veh_tipo='".$id."' LIMIT 1");
	$rowt=mysql_fetch_array($queryt);
	return $rowt['nombre']."|".$rowt['dpa']."|".$rowt['rc']."|".$rowt['rc2']."|".$rowt['fj']."|".$rowt['id_serv_rep'];
}

function Marcas($id){
	$querym=mysql_query("SELECT * FROM  seguro_marcas WHERE ID='".$id."' LIMIT 1");
	$rowm=mysql_fetch_array($querym);
	return trim($rowm['DESCRIPCION']);
}

function Modelos($id){
	$querymo=mysql_query("SELECT * FROM  seguro_modelos WHERE id='".$id."' LIMIT 1");
	$rowmo=mysql_fetch_array($querymo);
	return trim($rowmo['descripcion']);
}

function GetPrefijo($id){
	$queryp=mysql_query("SELECT * FROM  seguros WHERE id='".$id."' LIMIT 1");
	$rowp=mysql_fetch_array($queryp);
	return trim($rowp['prefijo']);
}

///-------------------------------------------------	
function Cedula($id){
  	$cedula = str_replace("-","",$id);
	$in  = $cedula;
  	return substr($in,0,3)."-".substr($in,3,-1)."-".substr($in,-1);
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
	
	   return $Vard = $fecha_vigente1[2].'-'.$fecha_vigente1[1].'-'.$fecha_vigente1[0]; 
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
	
 $html ='No, Poliza,Nombres,Apellidos,Cedula,Direccion,Ciudad,Telefono,Tipo,Marca,Modelo,Year,Chassis,Placa,Fecha Emision,Inicio Vigencia,Fin Vigencia,DPA,RC,RC2,FJ,Prima';
  $html .="\r";
  //$html .="\r\n";
  $qR=mysql_query("SELECT * FROM seguro_transacciones_reversos WHERE id !=''");
	 while($rev=mysql_fetch_array($qR)){ 
	    $reversadas .= "[".$rev['id_trans']."]";
	 }
	 
	$quer1 = mysql_query("SELECT * FROM seguro_transacciones WHERE $wFecha id_aseg='".$dist_id."' AND x_id !='XXXXX' order by id ASC");
	while($u=mysql_fetch_array($quer1)){
	
	if((substr_count($reversadas,"[".$u['id']."]")>0)){
	}else{
	
	$t++;
	
	//DATOS DEL VEHICULO
	$veh =  explode("|", Vehiculo($u['id_vehiculo']));
		
	//VER SERVICIOS OPCIONALES VENDIDOS	
	
	echo "PRIMER ENVIO<BR>";
	echo "<br>id:".$t."==> ".$u['id']."<br>";
	echo "<br>vjg:".$t."==> ".$u['vigencia_poliza']."<br>";
	echo "<br>tipo:".$t."==> ".$veh[0]."<br>";
	echo "<br>servicio:".$t."==> ".$u['serv_adc']."<br>";
	echo "PRIMER ENVIO<BR>";			
				
	$MServ		  = SerfdedEed($u['serv_adc'],$u['vigencia_poliza'],$veh[0]);
	$precioInic 	  = Precio($veh[0],$u['vigencia_poliza']);
	$precio	 	  = $precioInic + $MServ;
		
	$tipo =  explode("|", Tipo($veh[0]));
	$id_serv_rep = $tipo[5];  //id para tomar monto para el reporte en los servicios
	
	$marca = Marcas($veh[1]);
	$modelo = Modelos($veh[2]);
	$cliente =  explode("|", Clientes($u['id_cliente']));
	$pref = GetPrefijo($u['id_aseg']);
	$idseg = str_pad($u['id_poliza'], 6, "0", STR_PAD_LEFT);
	$prefi = $pref."-".$idseg;
	$direccion = str_replace(",", "", $cliente[3]);
	

$html .='
	'.$t.','.$prefi.','.$cliente[0].','.$cliente[1].','.Cedula($cliente[2]).','.$direccion.','.Ciudad($cliente[4]).','.Telefono($cliente[5]).','.$tipo[0].','.$marca.','.$modelo.','.$veh[3].','.$veh[5].','.$veh[4].','.Fecha($u['fecha']).','.Fecha($u['fecha_inicio']).','.Fecha($u['fecha_fin']).','.$tipo[1].','.$tipo[2].','.$tipo[3].','.$tipo[4].','.$precio.'';
	$html .="\r";
    } } 
	
	$carpeta = 'Csv/ASEGURADORA/'.$dist_id.'';
	if (!file_exists($carpeta)) {
		mkdir($carpeta, 0777, true);
	}
	
	//$sfile	= "Archivos/CLIENTES/Transacciones_$fDesde.xls"; // Ruta del archivo a generar 
	$sfile	= "Csv/ASEGURADORA/".$dist_id."/$fDesde.csv"; // Ruta del archivo a generar 

	$fp		= fopen($sfile,"w");
	
	fwrite($fp,$html); 
	fclose($fp); 
	
	echo $html;
   
 }
 
 

$sqaw =mysql_query("SELECT * FROM seguro_transacciones WHERE id_aseg='".$_GET['id_aseg']."' order by id desc limit 1");
	$paw=mysql_fetch_array($sqaw);
	
	if($paw['id']){
		
		echo "<br>Ventas:[".$paw['id_aseg']."] ".Ventas($paw['id_aseg'])."<br>";
		
	}

?>

