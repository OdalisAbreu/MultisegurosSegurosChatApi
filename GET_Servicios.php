<?php
	
	///exit();
	session_start();
	ini_set('display_errors',0);	
	include('inc/conexion_inc.php'); 
	include('inc/auditoria.balance.func.php');
	Conectarse();  

if($_POST){

 
function get_Servicios($usuario,$password){  
	
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
			SELECT id,nombre,3meses,6meses,12meses
			FROM servicios
			WHERE activo='si'
			ORDER BY id ASC");
			while($row=mysql_fetch_array($query)){
			   
			  echo $row['id']."|".$row['nombre']."|".$row['3meses']."|".$row['6meses']."|".$row['12meses'].";";
		  
		   }
		    Auditoria($p['id'],$p['password'],$p['tipo_conex'],'OK=mostrando servicios','ver_serv','00','',$p['balance']);
			//return json_encode($resp);
			
		}else if($p['activo'] =='no'){
			Auditoria($p['id'],$p['password'],$p['tipo_conex'],'ERROR=usuario inactivo','ver_serv','18','',$p['balance']);
			return "18/Usuario inactivo/00";
		}
		
		 
	}else{
		Auditoria($p['id'],$p['password'],'','ERROR=error en parametros','ver_serv','17','','');
		return "17/Usuario o Password incorrectos/00"; 
	} 
	   
}
	
		$usuario 	= $_POST['usuario'];
		$password 	= $_POST['clave']; 
		print get_Servicios($usuario,$password);
		
}else{
	
	Auditoria('','Desconocido','http-request','Acceso no permitido - Metodo: '.$_SERVER['REQUEST_METHOD']." Cedula: ".$_POST['cedula'],'ver_serv','19','','');
	exit('ACCESO NO PERMITIDO');
}
	
?>