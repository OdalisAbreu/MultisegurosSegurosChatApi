<?php
    class records{
        
        function newRecord($userId, $type, $references){
           $record =  mysql_query("INSERT INTO `multiseg_2`.`records` (`id_user`, `type`, `references`, `date`) VALUES ('".$userId."', '".$type."', '".$references."', '".date("Y-m-d H:i:s")."')");
           if (!$record) {
            exit('144/'.die('Consulta inválida: ' . mysql_error()).'/00');
            }
        }
    }