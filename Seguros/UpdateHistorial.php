<?
ini_set('display_errors', 1);
set_time_limit(0);
include("inc/conexion_inc.php");
Conectarse();

// --------------------------------------------	
if ($_GET['fecha1']) {
	$fecha1 = $_GET['fecha1'];
} else {
	$fecha1 = fecha_despues('' . date('d/m/Y') . '', -1);
}
// --------------------------------------------
if ($_GET['fecha2']) {
	$fecha2 = $_GET['fecha2'];
} else {
	$fecha2 = fecha_despues('' . date('d/m/Y') . '', -1);
}
// -------------------------------------------

$fd1		= explode('/', $fecha1);
$fh1		= explode('/', $fecha2);
$fDesde 	= $fd1[2] . '-' . $fd1[1] . '-' . $fd1[0];
$fHasta 	= $fh1[2] . '-' . $fh1[1] . '-' . $fh1[0];

//$wFecha2 = "AND fecha >= '$fDesde 00:00:00' AND fecha <= '$fHasta 23:59:59' ";
$wFecha2 = "AND fecha >= '2018-07-22 00:00:00' AND fecha <= '2018-10-14 23:59:59' ";

function IfMontoCostoTarifasHistory($veh_tipo, $vigencia, $idaseg)
{
	$queryT = mysql_query("SELECT id,veh_tipo,3meses,6meses,12meses 
	   FROM seguro_costos WHERE veh_tipo ='" . $veh_tipo . "' AND id_seg = '" . $idaseg . "' LIMIT 1");
	$rowT = mysql_fetch_array($queryT);

	if ($vigencia == 3)  return $rowT['3meses'];
	if ($vigencia == 6)  return $rowT['6meses'];
	if ($vigencia == 12) return $rowT['12meses'];
}

function Datos($id)
{
	$queryT = mysql_query("SELECT id, vigencia_poliza, id_vehiculo, id_aseg
	   FROM seguro_transacciones WHERE id ='" . $id . "'  LIMIT 1");
	$rowT = mysql_fetch_array($queryT);
	return $rowT['vigencia_poliza'] . "|" . $rowT['id_vehiculo'] . "|" . $rowT['id_aseg'];
}

function VehiculoHistory($id)
{
	$query = mysql_query("SELECT * FROM  seguro_vehiculo WHERE id='" . $id . "' LIMIT 1");
	$row = mysql_fetch_array($query);
	return $row['veh_tipo'];
}

function IfMontoTarifasHistory($veh_tipo, $vigencia)
{
	$queryT = mysql_query("SELECT id,veh_tipo,3meses,6meses,12meses FROM seguro_tarifas 
		   WHERE veh_tipo ='" . $veh_tipo . "'  LIMIT 1");
	$rowT = mysql_fetch_array($queryT);

	if ($vigencia == 3)  return $rowT['3meses'];
	if ($vigencia == 6)  return $rowT['6meses'];
	if ($vigencia == 12) return $rowT['12meses'];
}


function CostoServicioHistory($id, $vigencia)
{
	$r6 = mysql_query("SELECT 3meses_costos,6meses_costos,12meses_costos FROM servicios_backup WHERE id='" . $id . "'LIMIT 1");

	if ($id > 0) {
		while ($row6 = mysql_fetch_array($r6)) {
			if ($vigencia == 3)  return $row6['3meses_costos'];
			if ($vigencia == 6)  return $row6['6meses_costos'];
			if ($vigencia == 12) return $row6['12meses_costos'];
		}
	}
}


?>

<div class="row">
	<div class="col-lg-12" style="margin-top:-35px;">
		<h3 class="page-header">Actualizar el historial por servicio opcional </h3>
	</div>
</div>





<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="filter-bar">

				<table style="padding-left:3%; padding-bottom:2%; padding-top:3%;" class="table table-striped table-bordered table-hover">

					<tr>
						<td>

							<label>Desde:</label>
							<input type="text" name="fecha1" id="fecha1" class="input-mini" value="<?= $fecha1 ?>" style="width: 95px; height:30px; padding-bottom:2px; padding-left:5px; margin-left:5px;">
							<label style="margin-left:5px;">Hasta:</label>
							<input type="text" name="fecha2" id="fecha2" class="input-mini" value="<?= $fecha2 ?>" style="width: 95px; height:30px; padding-bottom:2px; padding-left:5px; margin-left:5px;">
							<button name="bt_buscar" type="button" id="bt_buscar" class="btn btn-success" style="margin-left:10px; margin-left:15px; margin-left:5px;">
								Actualizar
							</button>


						</td>

					</tr>

				</table>

				<script type="text/javascript">
					$('#bt_buscar').click(
						function() {
							var fecha1 = $('#fecha1').val();
							var fecha2 = $('#fecha2').val();


							CargarAjax2('../ws_dev/Seguros/UpdateHistorial.php?fecha1=' + fecha1 + '&fecha2=' + fecha2 + '&consul=1', '', 'GET', 'cargaajax');
							$(this).attr('disabled', true);
							setTimeout("$('#bt_buscar').fadeOut(0); $('#descargar').fadeOut(0); $('#recargar2').fadeIn(0); ", 0);
						});

					$('#bt_buscar').fadeIn(0);
					$(function() {
						$("#fecha1").datepicker({
							dateFormat: 'dd/mm/yy'
						});
						$("#fecha2").datepicker({
							dateFormat: 'dd/mm/yy'
						});
					});
				</script>



			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th colspan="2">Descripcion de Datos</th>
							</tr>
						</thead>
						<tbody>
							<?

							if ($_GET['consul'] == '1') {

								$query = mysql_query("SELECT * FROM seguro_trans_history WHERE tipo ='seg' $wFecha2  order by id desc");
								while ($row = mysql_fetch_array($query)) {

									$idaseg = $row['id_aseg'];

									echo "<table width='400' border='1' cellspacing='1' cellpadding='3'>";

									echo "<tr>
				<td>ID REGISTRO</td>
      			<td>" . $row['id'] . "</td>
			</tr>";

									echo "<tr>
				<td>FECHA</td>
      			<td>" . $row['fecha'] . "</td>
			</tr>";

									echo "<tr>
				<td>ID TRANS</td>
      			<td>" . $row['id_trans'] . "</td>
			</tr>";

									echo "<tr>
				<td>TIPO</td>
      			<td>" . $row['tipo'] . "</td>
			</tr>";

									echo "<tr>
				<td>SEGURO</td>
      			<td>" . $row['id_aseg'] . "</td>
			</tr>";

									$Veh 		= explode("|", Datos($row['id_trans']));

									$vigencia_poliza 	= $Veh[0];
									$veh_tipo 			= VehiculoHistory($Veh[1]);
									$idaseg 			= $Veh[2];


									$costoseg 	= IfMontoCostoTarifasHistory($veh_tipo, $vigencia_poliza, $idaseg);

									$precioseg 	= IfMontoTarifasHistory($veh_tipo, $vigencia_poliza);
									echo "<tr>
				<td>VIGENCIA</td>
      			<td>" . $vigencia = $Veh[0] . "</td>
			</tr>";

									echo "<tr>
				<td>TIPO</td>
      			<td>" . $veh_tipo = VehiculoHistory($Veh[1]) . "</td>
			</tr>";

									echo "<tr>
				<td>ASEG</td>
      			<td>" . $id_aseg = $Veh[2] . "</td>
			</tr>";

									echo "<tr>
				<td>COSTO</td>
      			<td>" . $costoseg . "</td>
			</tr>
			<tr>
				<td>PRECIO</td>
      			<td>" . $precioseg . "</td>
			</tr>
			
		<table>";
									echo "===============================================<br><br>";


									mysql_query("UPDATE seguro_trans_history SET id_aseg ='" . $id_aseg . "',
		costo = '" . $costoseg . "', monto = '" . $precioseg . "'  WHERE id='" . $row['id'] . "' LIMIT 1");

									//echo "UPDATE seguro_trans_history SET id_aseg ='".$id_aseg."',
									//costo = '".$costoseg."' WHERE id='".$row['id']."' LIMIT 1<br>";

								}

								echo "===============================================<br><br>";
							}
							?>



						</tbody>
					</table>
				</div>
				<!-- /.table-responsive -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
</div>