<?php
	
///exit();
	session_start();
	ini_set('display_errors',0);	
	include('inc/conexion_inc.php');
	include('inc/auditoria.balance.func.php'); 
	 Conectarse();

if($_POST){
	 
	$rs2 = mysql_query("
	SELECT id,activo,user,password,balance,tipo_conex FROM personal WHERE 
	user = '".$_POST['usuario']."' AND password='".$_POST['clave']."' 
	LIMIT 1"); 
	
    $numU = mysql_num_rows($rs2);
   
   if($numU =='1'){ 
   		
		$user = mysql_fetch_array($rs2);
		if($user['activo']=='si'){
			
		Auditoria($user['id'],$user['password'],$user['tipo_conex'],'OK=mostrando aseguradora','ver_aseguradora','00','',$user['balance']);

			 	$query=mysql_query("
				SELECT * FROM seguros
				WHERE activo='si'
				ORDER BY id ASC");
				while($row=mysql_fetch_array($query)){
				  echo $row['id']."|".$row['nombre'].";";
			   }
			   
		}else{
			Auditoria($user['id'],$user['password'],$user['tipo_conex'],'ERROR=Usuario inactivo','ver_aseguradora','18','',$user['balance']);
			exit('18/Usuario inactivo/00');
		}
   
   }else{
	  Auditoria($_POST['usuario'],$_POST['clave'],'','ERROR=Usuario y/o Clave incorrectos','ver_aseguradora','14','','');	 
	 exit('14/Usuario o clave incorrectos/00');
   }
 
 
}else{
	
	Auditoria('','Desconocido','http-request','ERROR=Acceso no permitido - Metodo: '.$_SERVER['REQUEST_METHOD'],'ver_aseguradora','19','','');	 
	exit('ACCESO NO PERMITIDO');
	
}
	
	
	
?>