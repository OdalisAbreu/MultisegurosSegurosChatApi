<?php
ini_set('display_errors', 1);
set_time_limit(0);
include "../inc/conexion_inc.php";
include "../../lib/Reports/Emisiones/Emisiones.php";
Conectarse();

function generateEmissionsReport($id_aseg, $fechaDesde, $fechaHasta)
{
    $data = getDataEmisiones($id_aseg, $fechaDesde, $fechaHasta);

    $requestData = transformDataToObjectArrayWithHeaders($data);

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json",
            'method'  => 'POST',
            'content' => json_encode($requestData)
        )
    );

    $outputFileName = "MS_EM_" . date('Y-m-d', strtotime("-1 days"));

    $url = "http://localhost:8081/api/excel_no_template?fileName=$outputFileName&folder=$id_aseg";
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
}

$fechaDesde = date('Y-m-d', strtotime("-1 days")) . " 00:00:00";
//date_format(date_create_from_format("d/m/Y H:i:s", $_GET["fechaDesde"] . " 00:00:00"), "Y-m-d H:i:s");
$fechaHasta = date('Y-m-d', strtotime("-1 days")) . " 23:59:59";
//date_format(date_create_from_format("d/m/Y H:i:s", $_GET["fechaHasta"] . " 23:59:59"), "Y-m-d H:i:s");

$whereFecha = "fecha >= '$fechaDesde' AND fecha < '$fechaHasta' ";


$aseguradorasQuery = mysql_query("SELECT 
    id,
    id_dist,
    nombre,
    prefijo,
    id_suplid,
    logo_color,
    logo_mono,
    fecha,
    activo
FROM
    seguros
WHERE
    activo = 'si'");

$ventas = "";


while ($aseguradoraRow = mysql_fetch_array($aseguradorasQuery)) {
    $query = "SELECT * FROM seguro_transacciones WHERE id_aseg='" .
        $aseguradoraRow['id'] .
        "' AND $whereFecha  order by id desc limit 1";
    $sqaw = mysql_query($query);

    echo $query;
    $paw = mysql_fetch_array($sqaw);

    if ($paw['id']) {
        echo "<br>Reportes:[" .
            $paw['id_aseg'] .
            "] " .
            generateEmissionsReport($paw['id_aseg'], $fechaDesde, $fechaHasta) .
            "<br>";
    }
}
