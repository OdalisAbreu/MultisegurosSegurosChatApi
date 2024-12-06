<?php

session_start();
ini_set('display_errors',1);
setlocale(LC_ALL,"es_ES");
set_time_limit(0);
include("../inc/conexion_inc.php");
include("../inc/fechas.func.php");
Conectarse();


$Rhora = date("H:i");
$Rdia  = date("l");


$dia = "dia = '".$Rdia."' ";
$hora = "hora = '".$Rhora."' ";

echo "DIA: ".$dia."<br>";
echo "HORA: ".$hora."<br><br>";

$query=mysql_query("SELECT * FROM crontab WHERE activo ='si' ");
while($res = mysql_fetch_array($query)){
	
	
	echo "<b>LEYENDO BASE DE DATOS</b><br>";
	echo "<b>id</b> ".$res['id']."<br>";
	echo "<b>peticion</b> ".$res['peticion']."<br>";
	echo "<b>dia</b> ".$res['dia']."<br>";
	echo "<b>hora</b> ".$res['hora']."<br>";
	echo "------------------<br><br><br>";
	
	// SI ES DIARIO LA PETICION
	if($res['dia'] == 'Daily'){
		
		if($res['hora'] == $Rhora){
			echo "Peticion Diario: ".$res['peticion'];
			 $ss = file_get_contents(''.$res['peticion'].'');
			echo $ss;
			echo "<br><br>";
		}
		
	}else if($res['dia'] == $Rdia){
	// SI ES UN DIA EN ESPECIFICO
	
		if($res['hora'] ==$Rhora){
			echo "Peticion Especifico: ".$res['peticion'];
			 $ss = file_get_contents(''.$res['peticion'].'');
			echo $ss;
			echo "<br><br>	";
		}
	
	}
	
}
?>