<?

	function fecha_despues($fecha,$dias) { 
		
		list ($dia,$mes,$ano)=explode("/",$fecha); 
		if (!checkdate($mes,$dia,$ano)){return false;} 
		$dia	=	$dia+$dias; 
		$fecha	=	date( "d/m/Y", mktime(0,0,0,$mes,$dia,$ano) ); 
		return $fecha; 
} 

	// ****************
	function NombreFechaActual($fecha){ 
		
		if(date('d/m/Y') == $fecha){
			return 'Hoy'; 
		}elseif(fecha_despues(date('d/m/Y'),-1) == $fecha){
			return 'Ayer';
		}else{
			return $fecha;
		}
}

	// ****************
	function GetNombreDia($dia){ 
	
	$dia2 = array('','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo','');
	 
	return $dia2[$dia];
}

?>