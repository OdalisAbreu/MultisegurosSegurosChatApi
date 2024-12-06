<?php
	
	///exit();
	session_start();
	ini_set('display_errors',0);	
	include('inc/conexion_inc.php');
	include('inc/auditoria.balance.func.php'); 
	Conectarse();  
	
if($_POST){	

   $rs2 = mysql_query("
	SELECT id,activo,user,password,balance,tipo_conex,usar_bl_princ,id_dist FROM personal WHERE 
	user = '".$_POST['usuario']."' AND password='".$_POST['clave']."' 
	LIMIT 1");       
   $numU = mysql_num_rows($rs2);
  
   if ($numU =='1'){
	         
		$p = mysql_fetch_array($rs2);
		//VALIDAR SI ESTA ACTIVO O NO
		if($p['activo'] =='si'){
			
			
			$query=mysql_query("
			SELECT *
			FROM seguro_modelos
			WHERE activo='si'
			ORDER BY id ASC");
			
			while($row=mysql_fetch_array($query)){
				//return $row['descripcion'];
			  echo $row['ID']."|".$row['descripcion']."|".$row['IDMARCA'].";";
		   }
		   
		  Auditoria($p['id'],$p['password'],$p['tipo_conex'],'OK=mostrando modelos','ver_modelos','00','',$p['balance']); 
			
		}else if($p['activo'] =='no'){
			
			return "18/Usuario inactivo/00";
			Auditoria($p['id'],$p['password'],$p['tipo_conex'],'ERROR=usuario inactivo','ver_modelos','18','',$p['balance']);
		}
		
	}else{
		
		return "17/Usuario o Password incorrectos/00";
		
		Auditoria($p['id'],$p['password'],$p['tipo_conex'],'ERROR=error en parametros','ver_modelos','17','',''); 
	} 
	   
}else{
	Auditoria('','Desconocido','http-request','Acceso no permitido - Metodo: '.$_SERVER['REQUEST_METHOD']." Cedula: ".$_POST['cedula'],'ver_modelos','19','','');
	exit('ACCESO NO PERMITIDO');
}
	
?>