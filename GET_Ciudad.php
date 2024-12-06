<?php
	
	session_start();
	ini_set('display_errors',0);	
	include('inc/conexion_inc.php');
	include('inc/auditoria.balance.func.php'); 
	Conectarse();
	
	//print_r($_POST);
	
	
	function Muni($id){
		$rs2m = mysql_query("SELECT * FROM municipio WHERE id='".$id."'");
		$numUm = mysql_fetch_array($rs2m);
		return $numUm['descrip'];
	}
			
			
	//exit();
if($_POST['clave']){
	///exit();
	
	$rs2 = mysql_query("
	SELECT id,activo,user,password,balance,tipo_conex FROM personal WHERE 
	(
   	id = '".mysql_real_escape_string($_POST['usuario'])."' OR 
	user = '".mysql_real_escape_string($_POST['usuario'])."') 
	 AND password='".$_POST['clave']."' 
	LIMIT 1");  
    $numU = mysql_num_rows($rs2);
   
   if($numU =='1'){ 
   		
		$user = mysql_fetch_array($rs2);
		if($user['activo']=='si'){
			
			
			
			Auditoria($user['id'],$user['password'],$user['tipo_conex'],'OK=mostrando ciudad','ver_ciudad','00','',$user['balance']);
				$query=mysql_query("
				SELECT * FROM ciudad
				WHERE activo='si'
				ORDER BY id ASC");
				while($row=mysql_fetch_array($query)){
				  echo $row['id']."|".$row['descrip'].";";
			   }
   
		}else{
			Auditoria($user['id'],$user['password'],$user['tipo_conex'],'Usuario inactivo','ver_ciudad','18','',$user['balance']);
			exit('18/Usuario inactivo/00');
		}
   
   }else{
	  Auditoria($_POST['usuario'],$_POST['clave'],'','Usuario y/o Clave incorrectos','ver_ciudad','14','','');	 
	 exit('14/Usuario o Clave incorrectos/00');
   }
 
}else{
	Auditoria('','Desconocido','http-request','Acceso no permitido - Metodo: '.$_SERVER['REQUEST_METHOD'],'ver_ciudad','19','','');
	exit('ACCESO NO PERMITIDO');  
}	
	
	
?>