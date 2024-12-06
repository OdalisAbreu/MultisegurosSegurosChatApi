<?php
    error_reporting(!E_ALL);
    session_start();

    include('inc/config.php');
    include('inc/conexion_inc.php');
    //@include("inc/class/Memcached_utils.php");
    Conectarse();
    //exit();


    // FUNCIONES PARA EVITAR DUPLICIDAD:
    // -----------------------------------------------
   /* function saveTransaccionAplicada($key, $data)
    {
        if ($key) {
            $m = Memcached_client::get_instance();
            return $m->add($key, $data);
        }

        return FALSE;
    }

*/
   function GetTransaccionAplicada($key){
        $m = Memcached_client::get_instance();
        $data = $m->get($key);
        if ($data) {
            return $data;
        } else {
            return FALSE;
        }
    }

    // -----------------------------------------------


    $_POST = $_REQUEST;
	


    // PROTECCION POR HORA:
    if (date("Hi") <= 0730 or date("Hi") >= 2350) {

        $respJson['cod'] = 'EHR';
        $respJson['msj'] = 'Hora no permitida.';
        $respJson['recibido'] = date("Hi");
        exit(json_encode($respJson));
    }

    if ($_GET['secretkey'] !== 'SwaassW332224446677') {

        $respJson['cod'] = 'E!';
        $respJson['msj'] = 'Error de Login';
        $respJson['recibido'] = $_GET['secretkey'];
        exit(json_encode($respJson));
    }

    if ((!$_POST['monto']) && (!$_POST['confirmar'])) {

        $respJson['cod'] = 'E002';
        $respJson['msj'] = 'Monto Invalido';
        $respJson['recibido'] = $_POST['monto'];
        exit(json_encode($respJson));

    }

	$sqlClientes = "
	SELECT id,activo,funcion_id FROM personal 
	WHERE id ='".$_POST['recargador_id']."' AND activo ='no' AND funcion_id = '34' LIMIT 1";
    $qCli1s      = mysql_query($sqlClientes);

    if(mysql_num_rows($qCli1s)>0){
        $respJson['cod'] = '14';
        $respJson['msj'] = 'Usuario Invalido id ='.$_POST['ref_local'].' ';
        exit(json_encode($respJson));
    } 
	
	


    /*
     *
     *
     *
     * Descontar Comision Bancaria:
     */

    function chargeBankCommission($param =array())
    {
        $param['bal_final'] = $param['bal_actual'] - $param['monto'];
		
        // Registrar transaccion:
        mysql_query("
          INSERT INTO credito2
		    (id_pers,monto,tipo,fecha,realizada_por,balance_anterior,balance_despues,realizado_por2,nota,cred_actual,fecha_banco,tardio)
		  VALUES
			('".$param['user_id']."','".$param['monto']."','com_bank','".date('Y-m-d H:i:s')."','6',
			  '".$param['bal_actual']."','".$param['bal_actual']."','".$param['recargador_id']."','".$param['comentario']."','".$param['deuda_final']."','" . $param['fecha_banco'] . "','" . $param['tardio'] . "')"
        );


        mysql_query("
            INSERT INTO  depositos_unicos (
                `user_id`,`realizado_por`,`fecha_reg`,`monto_contado`,`monto_pago`,
                `contado_bal_anterior`,
                `contado_bal_final` ,
                `credito_bal_anterior` ,
                `credito_bal_final` ,
                `tipo`,
                monto_total,
                monto_cred,
                monto_reverso,
                banco_id,
				fecha_banco,
				tardio
            )
            VALUES (
                '".$param['user_id']."',  '".$param['recargador_id']."',CURRENT_TIMESTAMP,
                '".$param['monto']."',  '0',  '".$param['bal_actual']."',  '".$param['bal_final']."',
                '".$param['deuda_actual']."',  '".$param['deuda_final']."', 'Retiro','".$param['monto']."','".$param['monto_cred']."',
                '".$param['monto']."','".$param['banco_id']."','".$param['fecha_banco']."','".$param['tardio']."'
            )");


        // Descontamos Balance al Cliente:
        mysql_query("UPDATE personal SET balance =(balance - ".$param['monto'].") WHERE id='".$param['user_id']."' LIMIT 1");


        return $param;

    }




    // BUSCANDO USUARIOS CON COINCIDENCIAS:
    // -----------------------------------------
    //foreach()

    if ($_POST['user_cuenta'])
        $wUserCuenta = "AND user_cuentas LIKE '%" . $_POST['user_cuenta'] . "%'";
    else
        if ($_POST['user_id'])
            $wUserID = "AND id ='" . $_POST['user_id'] . "' ";
        else
            $wUserID = "AND id ='0' ";

    $sqlCliente = "
		SELECT id,nombres,funcion_id,email FROM personal 
		WHERE
		  id !='6' AND activo !='no' $wUserID $wUserCuenta $likes
		LIMIT 1";
		
		echo "
		SELECT id,nombres,funcion_id,email FROM personal 
		WHERE
		  id !='6' AND activo !='no' $wUserID $wUserCuenta $likes
		LIMIT 1";
    $qCli1      = mysql_query($sqlCliente);
    $Cliente    = mysql_fetch_array($qCli1);

    if (!$Cliente['nombres'])
    {
        $respJson['cod']        = 'E001';
        $respJson['msj']        = 'Cliente no Encontrado';
        $respJson['recibido']   = $_POST['user_id'];
//         $respJson['sql']        = $sqlCliente;

        exit(json_encode($respJson));
    }



    $_POST['user_id'] = $Cliente['id'];


    //EVITAR QUE SE APLIQUE IGUAL
    $key = date("ymd") . "-" . $_POST['user_id'] . $_POST['monto'];
    $data = $_POST['monto'];

    if ((GetTransaccionAplicada($key) == $data) && $_POST['via'] == 'automatica') {

        $respJson['cod'] = 'EDUP1';
        $respJson['msj'] = 'Detectado como duplicado en el dia.';
        $respJson['recibido'] = $key . ":" . $data;
//         $respJson['sql'] = $sqlCliente;
        exit(json_encode($respJson));
    }




    if($_POST['via'] == 'automatica'){

        /*
         * Verificar si el cliente puede ser recargado
         * automaticamente por Bankify.
         *
         */

        $sqlCliente = "
		SELECT client_id
		FROM bankify_config
		WHERE
		  client_id = '".$Cliente['id']."' AND status =1
		LIMIT 1";
        $qCli1                      = mysql_query($sqlCliente);
        $BankConfig                 = mysql_fetch_array($qCli1);

        if($BankConfig['client_id'] !== $Cliente['id'])
        {
            $respJson['msj']        = 'No se encontro configuracion para aplicar automatico.';
            $respJson['recibido']   = $_POST['user_id'];
            exit(json_encode($respJson));
        }

    }



    // INFORMACION DE CUENTAS BANCARIAS:
    // -----------------------------------------
    if ($_POST['banco_cuenta']) {
        $qCuent = mysql_query("
          SELECT id,nombre,cuenta_no,carg_x_serv_dep,carg_x_serv_trans
          FROM cuentas_bancos
          WHERE
            cuenta_no ='" . $_POST['banco_cuenta'] . "' LIMIT 1");
        $Cuenta = mysql_fetch_array($qCuent);

        if (!$Cuenta['cuenta_no']) {
            $respJson['cod'] = 'EC';
            $respJson['msj'] = 'Error de Cuenta Bancaria';
            $respJson['recibido'] = $Cuenta['cuenta_no'];

            exit(json_encode($respJson));
        }
        $_POST['banco'] = $Cuenta['id'];
    }


    if ($_POST) {
		
		
	
		
		
        if (!$_GET['ref_local'])
            $_POST['recargador_id'] = 8511; // ID de Bankify en Murec
			
        else
            $_POST['recargador_id'] = $_GET['ref_local']; // ID de Bankify en Murec

        if ((!$_POST['confirmar']) && $_POST['iniciar'] == "si") {
            $_POST['monto']     = number_format($_POST['monto'], 0);

            if ($_POST['monto'])
                $_POST['monto'] = str_replace(array('RD$', '$', ',', '.'), "", $_POST['monto']);

			
            $fecha_actual = date("Y-m-d");
			if ($_POST['fecha_banco'] != $fecha_actual && $_POST['fecha_banco'] !=''){
					 //<-- Si es Nota de Credito
                    if ($_POST['aut_code'] !='') {

                        $sqlCodAut = mysql_query("
                            SELECT fecha_exp,user_id  FROM aut_codigos
                            WHERE codigo = '" . $_POST['aut_code'] . "' AND estado ='0'
                            ORDER BY id DESC LIMIT 1");
                        $CodAut = mysql_fetch_array($sqlCodAut);
                        $FechaAct = date("YmdHi");
                        $FechaExp = date("YmdHi", strtotime($CodAut['fecha_exp']));
                        $Minutos = $FechaExp - $FechaAct;

                        if (($FechaAct > $FechaExp) && $CodAut['fecha_exp'])
                        {

                            $respJson['cod'] = '2';
                            $respJson['msj'] = 'Codigo Aut. Expiro.';
                            $respJson['recibido'] = $_POST['aut_code'];
                            exit(json_encode($respJson));

                        } elseif (!$CodAut['fecha_exp']) {

                                $respJson['cod'] = '2';
                                $respJson['msj'] = 'Codigo Aut. No Existe.';
                                $respJson['recibido'] = $_POST['aut_code'];
                                exit(json_encode($respJson));

                            }

                        // MARCAR EL CODIGO COMO USADO:
                        mysql_query("UPDATE aut_codigos SET estado ='1' WHERE codigo = '" . $_POST['aut_code'] . "' 
						LIMIT 1");
						
						$respJson['cod'] = '1';
                        $respJson['msj'] = 'Codigo Aut. Correcto.';
                        $respJson['recibido'] = $_POST['aut_code'];
                        //exit(json_encode($respJson));
						
						$_POST['tardio'] = '1';
						
						
                    } else {

                        $respJson['cod'] = '2';
                        $respJson['msj'] = 'Codigo Aut. No Existe.';
                        $respJson['recibido'] = $_POST['aut_code'];
                        exit(json_encode($respJson));
                    }
                
				
			}
				
				

            // VERIFICAMOS QUE NO SE DUPLIQUE EL DEP:
            // ----------------------------------------
            $noDup = file_get_contents(
                "http://127.0.0.1/wsMemcache/NoDuplicarIfExisteBalanceRec.php?user_id=" . $_POST['user_id'] . "&monto=" . $_POST['monto']);

            if ($noDup == 'duplicada') {
                $respJson['cod'] = 'DUP';
                $respJson['msj'] = 'Deposito Duplicado!';
                $respJson['recibido'] = $Cuenta['cuenta_no'];

                exit(json_encode($respJson));
            }

            // INFORMACION DEL CIENTE A RECARGAR:
            // ----------------------------------------
            $qCli = mysql_query("
								SELECT balance,porc_deuda_neg FROM personal WHERE id ='" . $_POST['user_id'] . "' LIMIT 1");
            $cliente = mysql_fetch_array($qCli);
            $bal_actual = $cliente['balance'];


            // PROCESANDO EL PAGO:
            // ----------------------------------------
            // VERIFICAMOS EL MONTO DEL CREDITO ACTUAL
            $Ver_monto = mysql_query("
			SELECT id,id_pers,cred_actual  FROM credito2 WHERE id_pers = '" . $_POST['user_id'] . "' ORDER BY id DESC LIMIT 1");
            $credito        = mysql_fetch_array($Ver_monto);
            $deuda_actual   = $credito['cred_actual'];


            // ------------------------ DIRECTO ---------------------------------
            if ($_POST['tpago'] == 'directo') {

                // CODIGO DE AUTORIZACION:
                // ------------------------------------------
                if ($_POST['banco'] == '20') { //<-- Si es Nota de Credito
                    if ($_POST['aut_code']) {

                        $sqlCodAut = mysql_query("
                            SELECT fecha_exp,user_id  FROM aut_codigos
                            WHERE codigo = '" . $_POST['aut_code'] . "' AND estado ='0'
                            ORDER BY id DESC LIMIT 1");
                        $CodAut = mysql_fetch_array($sqlCodAut);
                        $FechaAct = date("YmdHi");
                        $FechaExp = date("YmdHi", strtotime($CodAut['fecha_exp']));
                        $Minutos = $FechaExp - $FechaAct;

                        if (($FechaAct > $FechaExp) && $CodAut['fecha_exp'])
                        {

                            $respJson['cod'] = 'C001';
                            $respJson['msj'] = 'Codigo Aut. Expiro.';
                            $respJson['recibido'] = $_POST['aut_code'];
                            exit(json_encode($respJson));

                        } else
                            if (!$CodAut['fecha_exp']) {

                                $respJson['cod'] = 'C002';
                                $respJson['msj'] = 'Codigo Aut. No Existe.';
                                $respJson['recibido'] = $_POST['aut_code'];
                                exit(json_encode($respJson));

                            }

                        // MARCAR EL CODIGO COMO USADO:
                        mysql_query("UPDATE aut_codigos SET estado ='1' WHERE codigo = '" . $_POST['aut_code'] . "'");

                    } else {

                        $respJson['cod'] = 'C002';
                        $respJson['msj'] = 'Codigo Aut. No Existe.';
                        $respJson['recibido'] = $_POST['aut_code'];
                        exit(json_encode($respJson));
                    }
                }


                // DIVIDIENDO EL DEPOSITO:
                // ----------------------------------------

                $monto_restante = $_POST['monto'];
                $bal_final      = $cliente['balance']; // MIENTRAS NO SE CALCULA NADA AL CONTADO ES EL MISMO BAL.


                if ($deuda_actual > 0) {

                    // RENEGOCIANDO LA DEUDA, SEGUN CONFIG
                    // ajustando porc%
                    if ($cliente['porc_deuda_neg'] && ($deuda_actual > 0)) {
                        if ($cliente['porc_deuda_neg'] < 10) {
                            $cliente['porc_deuda_neg'] = "0.0" . str_replace(".", "", $cliente['porc_deuda_neg']);
                        } else {
                            $cliente['porc_deuda_neg'] = "0." . str_replace(".", "", $cliente['porc_deuda_neg']);
                        }

                        $MontoApagar = $_POST['monto'] * $cliente['porc_deuda_neg'];
                    } else {

                        if ($deuda_actual >= $_POST['monto'])
                            $MontoApagar = $_POST['monto'];    //<-- Seguimos proc. normal.
                        else
                            $MontoApagar = $deuda_actual;
                    }
                    // -----------------------------------

                    $monto_restante = ($_POST['monto'] - $MontoApagar);

                    // REALIZANDO PAGO.
                    // SI ENCONTRAMOS DEUDA, LA PAGAMOS:

                    // reajustamos la
                    if ($MontoApagar >= $deuda_actual) {
                        $deuda_final = 0;
                        $SobranDelPago = $MontoApagar - $deuda_actual;
                        $monto_restante += $SobranDelPago; // <-- Sumamos lo que sobra del pago.
                    } else {
                        $deuda_final = $deuda_actual - $MontoApagar;
                        //$monto_restante	+= $SobranDelPago; // <-- Sumamos lo que sobra del pago.
                    }

                    $Monto_Pago = $MontoApagar;

                    mysql_query("INSERT INTO credito2
				(id_pers,monto,tipo,fecha,realizada_por,balance_anterior,balance_despues,realizado_por2,nota,cred_actual,id_banco,fecha_banco,tardio) 
				VALUES
				('" . $_POST['user_id'] . "','" . $Monto_Pago . "','abono_credito','" . date('Y-m-d H:i:s') . "','6',
				'" . $bal_actual . "','" . $bal_actual . "','" . $_POST['recargador_id'] . "','" . $_POST['comentario'] . "','" . $deuda_final . "','" . $_POST['banco'] . "','" . $_POST['fecha_banco'] . "','" . $_POST['tardio'] . "')");
                }


                if ($monto_restante > 0) {
                    // SI SOBRA DESPUES DE PAGAR LA DEUDA, COLOCAMOS AL CONTADO:
                    $bal_final = $cliente['balance'] + $monto_restante;
                    $Monto_Contado = $monto_restante;

                    mysql_query("
				INSERT INTO recarga_balance_cuenta 
				(id_pers,monto,fecha,autorizada_por,balance_anterior,balance_despues,cuenta_banco,realizado_por,comentario,fecha_banco,tardio)
			 	VALUES 
				('" . $_POST['user_id'] . "','" . $Monto_Contado . "',now(),'6','" . $bal_actual . "','" . $bal_final . "','" . $_POST['banco'] . "',
				'" . $_POST['recargador_id'] . "','" . $_POST['comentario'] . "','" . $_POST['fecha_banco'] . "','" . $_POST['tardio'] . "')");

                    // APLICAMOS BALANCE AL CLIENTE:
                    mysql_query("
				UPDATE personal SET balance =(balance + $monto_restante) WHERE id='" . $_POST['user_id'] . "' LIMIT 1");

                }

            }


            // ------------------------ A CREDITO ---------------------------------
            


            // ------------------------ NOTA CREDITO ------------------------------
           


            // INSERTAMOS DEPOSITO UNICO PARA REPORTERIA:
            // -------------------------------------
            if ($deuda_final < 1) {
                $deuda_final    = 0;
            }

            saveTransaccionAplicada($key, $data);

            mysql_query("
				INSERT INTO  depositos_unicos (
					`user_id`,`realizado_por`,`fecha_reg`,`monto_contado`,`monto_pago`,`contado_bal_anterior`,
					`contado_bal_final` ,
					`credito_bal_anterior` ,
					`credito_bal_final` ,
					`tipo`,
					monto_total,monto_cred,banco_id,aut_codigo,descrip_banco,porc_deuda_neg,fecha_banco,tardio
				)
				VALUES (
					'" . $_POST['user_id'] . "',  '" . $_POST['recargador_id'] . "',CURRENT_TIMESTAMP,
					'" . $Monto_Contado . "',  '" . $Monto_Pago . "',  '" . $bal_actual . "',  '" . $bal_final . "',  '" . $deuda_actual . "',  '" . $deuda_final . "',
					'Deposito','" . $_POST['monto'] . "','" . $Monto_Credito . "','" . $_POST['banco'] . "','" . $_POST['aut_code'] . "','" . $_POST['descrip'] . "',
					'" . $cliente['porc_deuda_neg'] . "','" . $_POST['fecha_banco'] . "','" . $_POST['tardio'] . "'
				)");

            /*
             *
             *
             * Descontar Cargo por Comision
             * Bancaria:
             */
            if($Cuenta['carg_x_serv_trans'] && strpos($_POST['descrip'],"INTERNET") !==false)
            {

                $resultCharge = chargeBankCommission(array(
                    'user_id'       =>$_POST['user_id'],
                    'monto'         =>$Cuenta['carg_x_serv_trans'],
                    'bal_actual'    =>$bal_actual,
                    'comentario'    =>$_POST['user_id'],
                    'deuda_final'   =>$deuda_final,
                    'recargador_id' =>10172,
                    'banco_id' 		=>$_POST['banco'],
					'fecha_banco' 	=>$_POST['fecha_banco'],
                    'tardio' 		=>$_POST['tardio'],
					
                ));

                /*
                 * '".$param['user_id']."',  '".$param['recargador_id']."',CURRENT_TIMESTAMP,
                '".$param['monto']."',  '".$param['monto']."',  '".$param['bal_actual']."',  '".$param['bal_final']."',
                '".$param['deuda_actual']."',  '".$param['deuda_final']."', 'Retiro','".$param['monto']."','".$param['monto_cred']."',
                '".$param['monto_total']."','".$param['banco_id']."'
                 */



            }elseif($Cuenta['carg_x_serv_dep']){
                $resultCharge = chargeBankCommission(array(
                    'user_id'       =>$_POST['user_id'],
                    'monto'         =>$Cuenta['carg_x_serv_dep'],
                    'bal_actual'    =>$bal_actual,
                    'comentario'    =>$_POST['user_id'],
                    'deuda_final'   =>$deuda_final,
                    'recargador_id' =>10172,
                    'banco_id'      => $_POST['banco'],
					'fecha_banco' 	=>$_POST['fecha_banco'],
                    'tardio' 		=>$_POST['tardio'],
                ));
            }


        }
        $_POST['cod']           = "A";
        $_POST['confirm']       = $_POST['confirmar'];
        $_POST['user_nombre']   = $Cliente['nombres'];
        $_POST['user_id']       = $Cliente['id'];
        $_POST['banco_nombre']  = $Cuenta['nombre'];
        $_POST['banco_cuenta']  = $Cuenta['cuenta_no'];
//         $_POST['sql']           = $sqlCliente;
        $_POST['com_bank']      = $resultCharge;
        $resp1                  = json_encode($_POST);
		if($Cliente['funcion_id']=="2" || $Cliente['funcion_id']=="5")
		{
			
			$Cliente['email'] = str_replace(" ","",$Cliente['email']);
			$ema2 = file_get_contents(
		"http://multiplesrecargas.com/plataforma/emails/NotificarRecargaBalanceDistribuidores.php?nombre=".
		urlencode($Cliente['nombres'])."&monto=".number_format($_POST['monto'])."&email=".$Cliente['email']
		."&bal_actual=".$bal_final."&comentario=".urlencode($_POST['comentario'])."&asunto=Deposito+Aplicado");
			
		}
        exit($resp1);

    }

?>