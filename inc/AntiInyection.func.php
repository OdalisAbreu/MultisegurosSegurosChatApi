<?
	
	// anti inyection:
	function LimpiarParametros($param){
		// ANTI-SQL INYECTION
		// --------------------------
		$param 		= str_replace(
		array('',';',' ','INSERT','UPDATE','update','insert',',','FROM','from','select','WHERE','where'),"",$param);
		$param1 	= explode('/',$param);
		return $param1;
	}

?>