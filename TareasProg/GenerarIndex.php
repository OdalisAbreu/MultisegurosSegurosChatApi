<?
session_start();
ini_set('display_errors', 1);
include("../inc/conexion_inc.php");
Conectarse();

$sq = mysql_query("SELECT id FROM seguro_transacciones ORDER BY id DESC LIMIT 1");
$p = mysql_fetch_array($sq);
$p['id'] = $p['id'] + 1;

$sqrec = mysql_query("SELECT id FROM recarga_retiro ORDER BY id DESC LIMIT 1");
$prec = mysql_fetch_array($sqrec);
$prec['id'] = $prec['id'] + 1;

//INDEX TRANSACCIONES	
mysql_query("INSERT INTO indexa(id_inicio,tipo,fecha) 
	VALUES
	('" . $p['id'] . "','trans','" . date("Y-m-d H:i:s") . "')");

//INDEX RECARGAS/RETIRS/INGRESOS	
mysql_query("INSERT INTO indexa(id_inicio,tipo,fecha) 
	VALUES
	('" . $prec['id'] . "','rec','" . date("Y-m-d H:i:s") . "')");
