	<?

	function GetTransID_utd()
	{

		mysql_query(
			"INSERT INTO no_operacion (fecha_hora) 
		VALUES ('" . date('Y-m-d H:i:s') . "')"
		);
		return mysql_insert_id();
	}

	function GetSeguroDeVida($conf)
	{

		if (!$conf['user_id'])
			$api[0] = 14;


		$api[0] = '00';


		if ($api[0] == '00') {

			return array(
				"00",
				"DeVida:Completado!", $api['seguroid'], $parametros['reference'], $api['TransactionId']
			);
		} elseif ($api[0] == '06') {

			return array("12", "UTD2:" . $api['Message'], $api['Code']); // Num Invalido	

		} else {

			return array("15", $api[1], 0);
		}
	}

	?>
