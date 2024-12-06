<?php
	
	///exit();
	session_start();
	ini_set('display_errors',1);	
	include('inc/config.php');
	include('inc/conexion_inc.php'); 
	include('inc/auditoria.balance.func.php');
	Conectarse();  
	
if($_POST){
	
/*function Veh($id){
	$query=mysql_query("
	SELECT * FROM seguro_vehiculo
	WHERE id_cliente='".$id."' LIMIT 1");
	$row=mysql_fetch_array($query);
	return $row['serv_adc']."".$row['vigencia_poliza']."".$row['fecha_inicio']."".$row['id_vehiculo'];
}*/

function tran($id){
	$query=mysql_query("
	SELECT * FROM seguro_transacciones
	WHERE id_cliente='".$id."' LIMIT 1");
	$row			= mysql_fetch_array($query);
	$fecha_inicio 	= explode(" ",$row['fecha_inicio']);
	
	return $row['serv_adc']."|".$row['vigencia_poliza']."|".$fecha_inicio[0]."|".$row['id_vehiculo']."|".$row['id_aseg']."|".$row['id']."|".$row['id_poliza'];
	
}

function Vehiculo($id){
	$query=mysql_query("
	SELECT * FROM  seguro_vehiculo
	WHERE id='".$id."' LIMIT 1");
	$row=mysql_fetch_array($query);
	
	return $row['veh_tipo']."|".$row['veh_marca']."|".$row['veh_modelo']."|".$row['veh_ano']."|".$row['veh_matricula']."|".$row['veh_chassis'];
	
}

function GetPrefijo($id){
	$queryp=mysql_query("
	SELECT * FROM  seguros
	WHERE id='".$id."' LIMIT 1");
	$rowp=mysql_fetch_array($queryp);
	
	return $rowp['prefijo'];
	
}
 
function get_Cedula($usuario,$password,$cedula){  

   $rs2 = mysql_query(
   "SELECT id,balance,usar_bl_princ,id_dist,show_bl_princ,user,password,activo
    FROM personal WHERE 
   user = '".$usuario."' 
   AND password='".$password."'
   LIMIT 1");       
   $numU = mysql_num_rows($rs2);
   
   if ($numU =='1'){
	        
		$p = mysql_fetch_array($rs2);
		//VALIDAR SI ESTA ACTIVO O NO
		if($p['activo'] =='si'){
			
			

				$queryd=mysql_query("
				SELECT * FROM seguro_clientes
				WHERE asegurado_cedula='".$cedula."'
				ORDER BY id DESC ");
				
	       if(mysql_num_rows($queryd) > 0){
				$UU2 = mysql_num_rows($queryd);
		   }else{
				$queryd=mysql_query("
				SELECT * FROM seguro_clientes
				WHERE asegurado_pasaporte='".$cedula."'
				ORDER BY id DESC ");
				if(mysql_num_rows($queryd) > 0){
					$UU2 = mysql_num_rows($queryd);
				}else{
					$UU2 = 0;
				}
		   }
 
				if ($UU2 >0){
				
				while($row=mysql_fetch_array($queryd)){
						
						$transac = tran($row['id']);
						$Rtrans  = explode("|",$transac);
						
						$veh 	 = Vehiculo($Rtrans[3]);
						$Rveh 	 = explode("|",$veh);
						
						$pref = GetPrefijo($Rtrans[4]);
	 					$idseg = str_pad($Rtrans[6], 6, "0", STR_PAD_LEFT);
	 					$prefi = $pref."-".$idseg;
	 
					  echo $row['asegurado_nombres']."|".$row['asegurado_apellidos']."|".$row['asegurado_cedula']."|".$row['asegurado_direccion']."|".$row['asegurado_telefono1']."|".$Rveh[0]."|".$Rveh[1]."|".$Rveh[2]."|".$Rveh[3]."|".$Rveh[4]."|".$Rveh[5]."|".$Rtrans[2]."|".$Rtrans[1]."|".$Rtrans[0]."|".$Rtrans[4]."|".$prefi."|".$row['asegurado_pasaporte'].";";
					  
					  
				 }
				 
					  Auditoria($usuario,$password,$p['tipo_conex'],'OK=solicitud de reimpresion ticket, cedula:'.$_POST['cedula'],'imp_seguro','00','',$p['balance']);
					  
				}else{
					
					 Auditoria($usuario,$password,$p['tipo_conex'],'ERROR=solicitud de reimpresion ticket, cedula:'.$_POST['cedula'],'imp_seguro','20','',$p['balance']);
					 
					 exit('20/Datos no encontrados/00');
				}
		   
		   
		}else if($p['activo'] =='no'){
			
			Auditoria($usuario,$password,$p['tipo_conex'],'ERROR=solicitud de reimpresion ticket, usuario inactivo, cedula:'.$_POST['cedula'],'imp_seguro','18','',$p['balance']);
			return "18/Usuario inactivo/00";
			
		}
		
	}else{
		
		Auditoria($usuario,$password,$p['tipo_conex'],'ERROR=solicitud de reimpresion ticket, cedula:'.$_POST['cedula'],'imp_seguro','14','','');
		return "14/Usuario o Password incorrectos/00";
		 
	} 
	   
}
	
		$usuario 	= $_POST['usuario'];
		$password 	= $_POST['clave']; 
		$cedula = str_replace(array("`", "~", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "-", "=", "+", "[", "{", "]", "}", ";", ":", "'", ",", "<", ".", ">", "/", "?", "|"), "", $_POST['cedula']);
		
print get_Cedula($usuario,$password,$cedula);
		
}else{

Auditoria('','Desconocido','http-request','Acceso no permitido - Metodo: '.$_SERVER['REQUEST_METHOD']." Cedula: ".$_POST['cedula'],'imp_seguro','19','','');
	exit('ACCESO NO PERMITIDO');
	
}
	
?>