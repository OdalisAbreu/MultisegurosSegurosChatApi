<? 	
	// anti inyection:
	function SanidarParametros($param){
		// ANTI-SQL INYECTION
		$param1 	= explode('/',$param);
		return $param1;
	}
	// validar vigencia
	function IfVigencia($id){
	   if($id =='3' || $id =='6' || $id =='12' ){ 
	   		return "00";
	   }else{
		   	return "15";
	   }
	}
	// validar montos
	function IfMontoValido($cod_p){
		 $veh_tipo = VEH_TIPO;
		 $query=mysql_query("
		SELECT id,veh_tipo,3meses,6meses,12meses 
		FROM seguro_tarifas 
		WHERE veh_tipo ='".$veh_tipo."'");
		while($row=mysql_fetch_array($query)){
	  
		if(VIGENCIA ==3)  return $row['3meses'];
		if(VIGENCIA ==6)  return $row['6meses'];
		if(VIGENCIA ==12) return $row['12meses']; 
		
		}
	}
	
	function IfSeguroActivo($id){
		   $quT=mysql_query("
		   SELECT id,activo FROM seguros 
		   WHERE id ='".$id."' LIMIT 1");
		   $rT=mysql_fetch_array($quT);
		  
		  if($rT['activo'] =='si'){  
		  	return "00"; 
		}else if($rT['activo'] =='no'){
		  	return "15";
		}
	}

	function validateModel($idMarca, $idModel, $tipo ){
			$query = mysql_query("SELECT id, IDMARCA, tipo FROM seguro_modelos where id = $idModel");
			$model = mysql_fetch_array($query);
			if($model['tipo']){
				$tipo = $tipo + 100;
				if($model['IDMARCA'] == $idMarca){
					if(substr_count($model['tipo'],"".$tipo."-")>0){
						return 'Ok';
					}else{
						$rescat = mysql_query("SELECT id, nombre, veh_tipo from seguro_tarifas WHERE activo ='si' order by nombre");
						$cont = '0';
						$value = '';
						while($row = mysql_fetch_array($rescat)){
							if(substr_count($model['tipo'],"".$row['veh_tipo']."-")>0){
								$cont++;
								$value = $value.'  '.$row['nombre']; 
							}
						}
							if($cont > 1){
								return 'El tipo vehículo no se corresponde con el modelo, los tipos de vehículos permitidos para este modelo son: '.$value;
							}else{
								return 'El tipo vehículo no se corresponde con el modelo, el tipo de vehículo permitido para este modelo es: '.$value;
							}
					}
				}else{
					return 'La marca no se corresponde con el modelo.';
				}
			}else{
				return 'Ok';
			}
	}
	function validatePlaca($placa, $tipoVehiculo){
		//Validar el primer dijito devuelve OK 
		$query = mysql_query("SELECT * from seguro_tarifas WHERE veh_tipo ='".$tipoVehiculo."'");
    	$row = mysql_fetch_array($query);

		if($row['placas']){
			$placasActivas = explode(',',$row['placas']);
			$existe = 'false';
			foreach($placasActivas as $placaActiva){
				$digito = substr($placa, 0,1);
				if($placaActiva == $digito){
					$respuesta = 'Ok';
					$existe = 'true';
					break;
				}else{
					$digito = substr($placa, 0,2);
					if($placaActiva == $digito){
						$respuesta = 'Ok';
						$existe = 'true';
						break;
					}
				}
			  }
			  if($existe == 'false'){
				$respuesta = 'La placa '.$placa.' no corresponde al tipo de vehículo seleccionado';
			 }
		}else{
			$respuesta = 'Ok';
		}
		//Si no aplica con un digito Validar con dos devuelve OK 
		//Si no tiene nada devuelve OK
		return $respuesta;
	}	
?>