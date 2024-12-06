<?php
	
	///exit();
	session_start();
	ini_set('display_errors',0);	
	include('inc/conexion_inc.php'); 
	include('inc/auditoria.balance.func.php');
	Conectarse();  
	
if($_POST){

function get_Tarifas($usuario,$password){  
	

   $rs2 = mysql_query(
   "SELECT id,user,password,activo,balance
    FROM personal WHERE 
    user = '".$usuario."' 
    AND password='".$password."'
    LIMIT 1");       
  
   $numU = mysql_num_rows($rs2);
   
   if ($numU =='1'){
	        
		$p = mysql_fetch_array($rs2);
		//VALIDAR SI ESTA ACTIVO O NO
		if($p['activo'] =='si'){
			
			$query=mysql_query("
			SELECT id,asegurado_nombres,asegurado_apellidos,asegurado_cedula,asegurado_pasaporte,asegurado_telefono1,asegurado_direccion,ciudad
			FROM seguro_clientes
			ORDER BY id ASC");
			while($row=mysql_fetch_array($query)){
			   
			  echo $row['id']."|".$row['asegurado_nombres']."|".$row['asegurado_apellidos']."|".$row['asegurado_cedula']."|".$row['asegurado_pasaporte']."|".$row['asegurado_telefono1']."|".$row['asegurado_direccion']."|".$row['ciudad'].";";
		  
		   }
		    Auditoria($p['id'],$p['password'],$p['tipo_conex'],'OK=mostrando tarifas','ver_tarif','00','',$p['balance']);
			//return json_encode($resp);
			
		}else if($p['activo'] =='no'){
			Auditoria($p['id'],$p['password'],$p['tipo_conex'],'ERROR=usuario inactivo','ver_tarif','18','',$p['balance']);
			return "18/Usuario inactivo/00";
		}
		
		 
	}else{
		
		Auditoria($p['id'],$p['password'],'','ERROR=error en parametros','ver_tarif','17','','');
		return "17/Usuario o Password incorrectos/00"; 
	} 
	   
}
	
		$usuario 	= $_POST['usuario'];
		$password 	= $_POST['clave']; 
		print get_Tarifas($usuario,$password);
		
}else{
	
	Auditoria('','Desconocido','http-request','Acceso no permitido - Metodo: '.$_SERVER['REQUEST_METHOD'],'ver_tarif','19','','');
	exit('ACCESO NO PERMITIDO');
}
	
?>