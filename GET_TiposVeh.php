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
   
   if ($numU =='1'){
	        
		$p = mysql_fetch_array($rs2);
		//VALIDAR SI ESTA ACTIVO O NO
		if($p['activo'] =='si'){
			
			Auditoria($p['id'],$p['password'],$p['tipo_conex'],'OK=mostrando Tip Vehiculo','ver_marca','00','',$p['balance']);
			$query=mysql_query("
			SELECT veh_tipo as ID,TRIM(nombre) AS DESCRIPCION
			FROM seguro_tarifas
			WHERE activo='si'
			ORDER BY id ASC");
			while($row=mysql_fetch_array($query)){
			  echo $row['ID']."|".$row['DESCRIPCION'].";";
		   }
		   
		}else if($p['activo'] =='no'){
			Auditoria($p['id'],$p['password'],$p['tipo_conex'],'ERROR=usuario inactivo','ver_marca','18','',$p['balance']);
			return "18/Usuario inactivo/00";
		}
		
	}else{
	 Auditoria($_POST['usuario'],$_POST['clave'],'','Usuario y/o Clave incorrectos','ver_marca','14','','');	 
	 exit('14/Usuario o Clave incorrectos/00'); 
	} 
	   
}else{
	Auditoria('','Desconocido','http-request','Acceso no permitido - Metodo: '.$_SERVER['REQUEST_METHOD']." Cedula: ".$_POST['cedula'],'ver_marca','19','','');
	exit('ACCESO NO PERMITIDO');
}
	
?>