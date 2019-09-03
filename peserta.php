<?php
// error_reporting(E_ALL);

mysql_connect('localhost','root','');
mysql_select_db('funtasti_db');

$dataKotaPeserta = array();
$kotaPesertaQuery = "SELECT COUNT(no_trx) peserta, users_tb.full_name,users_tb.no_telp,kode_tb.no_trx,kode_tb.kode_input,users_tb.email_address,users_tb.no_id_ktp,users_tb.alamat,users_tb.kota,users_tb.datetime_create from users_tb join kode_tb ON kode_tb.no_id_ktp = users_tb.no_id_ktp GROUP BY kota ORDER BY peserta DESC";

$kotaPesertaData = mysql_query($kotaPesertaQuery);
while ($rowsKotaPeserta = mysql_fetch_assoc($kotaPesertaData)) {
	$dataKotaPeserta[] = $rowsKotaPeserta;
}

// DATA PESERTA
$dataPeserta = array();

$dataPesertaQuery = "SELECT users_tb.full_name,users_tb.no_telp,kode_tb.no_trx,kode_tb.kode_input,users_tb.email_address,users_tb.no_id_ktp,users_tb.alamat,users_tb.kota,users_tb.datetime_create from users_tb join kode_tb ON kode_tb.no_id_ktp = users_tb.no_id_ktp";

$dataPesertaExec = mysql_query($dataPesertaQuery);
while ($rowDataPeserta = mysql_fetch_assoc($dataPesertaExec)) {
	$dataPeserta[] = $rowDataPeserta;
}


$listNewPeserta = array();
$no = 1;
foreach ($dataPeserta as $key => $value) {
	$listNewPeserta[$key]['ID'] 			= $no;
	$listNewPeserta[$key]['NO_TRX'] 		= strtoupper($value['no_trx']);
	$listNewPeserta[$key]['KODE'] 			= strtoupper($value['kode_input']);
	$listNewPeserta[$key]['NAMA'] 			= strtoupper($value['full_name']);
	$listNewPeserta[$key]['KOTA'] 			= strtoupper($value['kota']);
	$listNewPeserta[$key]['TGL_INPUT'] 		= strtoupper($value['datetime_create']);
	$no++;
}

if(isset($_GET['q'])) {

	include_once("tools/xlsxwriter.class.php");

	if($_GET['q'] == 'o') {
		// CLEAR / DESTROY SESSION
		header('Location: index.php');
	} else if($_GET['q'] == 'r') {
		// echo "Reset Data Pemenang";

		$reset_peserta = "UPDATE users_tb SET status_user = 0, win = 0, win_datetime = '0000-00-00 00:00:00' WHERE win = 1";
		$reset_peserta_exec = mysql_query($reset_peserta);

		if($reset_peserta_exec) {
			$truncate_pemenang = "TRUNCATE TABLE pemenang_tb";
			$update_stat_pemenang_exec = mysql_query($truncate_pemenang);

			if($update_stat_pemenang_exec) {
				$resetHandle = fopen('status_req.txt', 'w') or die('Cannot open file resetHandle');
				fwrite($resetHandle, '0'); 

				header('Location: peserta.php');
			}
		}
	} else if($_GET['q'] == 'ep') {
		// echo "Export Data Kota";
		$filename = "Data_Peserta_All_".date('YmdHis').".xlsx";
		$rows = $dataPeserta;

	}


	// EXPORT ACTION
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->setAuthor('Juri Pebrianto by Mobiwin.co.id'); 
	foreach($rows as $row){
		$writer->writeSheetRow('Sheet1', $row);
	}
	$writer->writeToStdOut();
	$writer->writeToFile('assets/exports/'.$filename);
	// echo $writer->writeToString();
	exit(0);
}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<title>TITLE</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">

	<!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="assets/images/favicon-singa-merah.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/images/favicon-singa-merah.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/images/favicon-singa-merah.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/images/favicon-singa-merah.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/images/favicon-singa-merah.png">
    <link rel="icon" type="image/png" href="assets/images/favicon-singa-merah.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/images/favicon-singa-merah.png" sizes="96x96">
    <link rel="icon" type="image/png" href="assets/images/favicon-singa-merah.png" sizes="16x16">
	
	
	<link href="assets/common-css/ionicons.css" rel="stylesheet">
	
	<link rel="stylesheet" type="text/css" href="assets/bootstrap-3.3.7-dist/css/bootstrap.min.css">
	
	<link rel="stylesheet" href="assets/common-css/jquery.classycountdown.css" />
		
	<link href="assets/05-comming-soon/css/styles.css" rel="stylesheet">
	
	<link href="assets/05-comming-soon/css/responsive.css" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="assets/DataTables/datatables.min.css"/>
	
</head>
<body style="background-color: #8e8e8f;">
	
	<div class="main-area-wrapper">
		<div class="main-area center-text" style="background-image:url(assets/images/gambar3.png);">
			
			<div class="display-table">
				<div class="display-table-cell">
					
					<h1 class="title"><b>PENGUNDIAN PERIODE 1</b></h1>
					<h3 class="title"><b>FUNTASTIK FUNWARI</b></h3>
					<!-- <p class="desc font-white">Jumlah peserta undian Funtastik Funwari</p> -->
					
					<!-- <div id="normal-countdown">
						<div class="time-sec"><h3 id="pad1" class="main-time" style="line-height: 90px">1</h3></div>
						<div class="time-sec"><h3 id="pad2" class="main-time" style="line-height: 90px">4</h3></div>
						<div class="time-sec"><h3 id="pad3" class="main-time" style="line-height: 90px">8</h3></div>
						<div class="time-sec"><h3 id="pad4" class="main-time" style="line-height: 90px">9</h3></div>
						<div class="time-sec"><h3 id="pad5" class="main-time" style="line-height: 90px">8</h3></div>
					</div> -->

					<ul id="tools" class="social-btn">
						<li class="list-heading">Tools</li>
						<li><a id="reset_database" style="background-color: #F43846; padding-right: 15px"><i class="ion-refresh"></i> Reset Database</a></li>

						<li><a id="multidraw" style="background-color: #53F83A; padding-right: 15px" href="multi_draw_traveloka.php"><i class="ion-shuffle"></i> Draw Traveloka</a></li>

						<li><a id="singledraw" style="background-color: #FF8C00; padding-right: 15px" href="multi_draw_gopay.php"><i class="ion-shuffle"></i> Draw Gopay</a></li>

						<li><a id="datapeserta" style="background-color: #000; padding-right: 15px" href="#showlistpeserta"><i class="ion-archive"></i> Data Kota</a></li>

						<li><a id="peserta" style="background-color: #2F4F4F; padding-right: 15px" href="#showlistpeserta2"><i class="ion-archive"></i> Peserta</a></li>
					</ul>
					
				</div><!-- display-table -->
			</div><!-- display-table-cell -->
		</div><!-- main-area -->
	</div><!-- main-area-wrapper -->
	
	<div id="pesertaList" class="main-area-wrapper">
		<div class="center-text">
			<!-- DATA PESERTA -->
			<div id="showlistpeserta2" class="display-table">
				<div class="display-table-cell">
					<h3 style="color: #fff;text-shadow: 1px 1px #000;" class="title"><b>DAFTAR PESERTA UNDIAN</b></h3>

					<div class="col-md-12">
						<div class="row">
							<div class="panel panel-primary">
							  	<div class="panel-body">
							    	<table id="tabel-data" class="table table-striped table-bordered" width="100%" cellspacing="0">
									    <thead>
									      <tr class="danger">
									        <th style="text-align: center;">NO</th>
									        <th style="text-align: center;">NO TRX</th>
									        <th style="text-align: center;">KODE</th>
									        <th style="text-align: center;">NAMA</th>
									        <th style="text-align: center;">KOTA</th>
									        <th style="text-align: center;">TGL INPUT</th>
									      </tr>
									    </thead> 
									    <tbody>

									    	<?php 
									    	$no = 1;
									    	foreach ($dataPeserta as $key => $value) { ?>
									    	<tr>
									    		<td><?php echo $no; ?></td>
									    		<td><?php echo $value['no_trx'];?></td>
									    		<td><?php echo $value['kode_input'];?></td>
									    		<td><?php echo $value['full_name'];?></td>
									    		<td><?php echo $value['kota'];?></td>
									    		<td><?php echo $value['datetime_create'];?></td>
									    	</tr>
									    	<?php $no++; } ?>
									    </tbody>
									</table>
							  	</div>
							  	<div class="panel-footer">
							  		<a href="peserta.php?q=ep" id="export_peserta" style="background-color: #DAB000; color: #fff; padding: 15px"><i class="ion-android-arrow-down"></i> Export Data</a>
							  	</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="dataList" class="main-area-wrapper" style="display: none">
		<div class="center-text">
			<!-- DATA STATSTIK -->
			<div id="showlistpeserta" class="display-table">
				<div class="display-table-cell">
					<h3 style="color: #fff;text-shadow: 1px 1px #000;" class="title"><b>DATA KOTA PESERTA UNDIAN</b></h3>

					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-primary">
								  	<div class="panel-heading">
								    	<h3 class="panel-title">Data Kota</h3>
								  	</div>
								  	<div class="panel-body">
								    	<table id="tabel-data-kota" class="table table-bordered">
										    <thead>
										      <tr class="danger">
										        <th style="text-align: center;">NO</th>
										        <th style="text-align: center;">NAMA KOTA</th>
										        <th style="text-align: center;">JUMLAH PESERTA</th>
										      </tr>
										    </thead>
										    <tbody>
										    	<?php 
										    	$no = 1; 
										    	foreach ($dataKotaPeserta as $key => $value) { ?>
										      	<tr>
										      		<td><?php echo $no; ?></td>
										      		<td style="text-align: left;"><?php echo $value['kota']; ?></td>
										      		<td><?php echo $value['peserta']; ?></td>
										      	</tr>
										      	<?php $no++; } ?>
										    </tbody>
									  	</table>
								  	</div>
								  	<div class="panel-footer">
								  		<a href="draw_backup.php?q=ek" id="export_kota" style="background-color: #DAB000; color: #fff; padding: 15px"><i class="ion-android-arrow-down"></i> Export Data</a>
								  	</div>
								</div>
							</div>								
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- SCIPTS -->
	
	<script src="assets/common-js/jquery-3.1.1.min.js"></script>
	<script src="assets/plugin/simple-bootstrap-paginator.js"></script>
	<script src="assets/js/pagination.js"></script>
	<script type="text/javascript" src="assets/DataTables/datatables.min.js"></script>
	<!-- <script src="assets/common-js/jquery.countdown.min.js"></script> -->
	
	<!-- <script src="assets/common-js/scripts.js"></script> -->


	<script type="text/javascript">
		$(document).ready(function(){
	        $('#tabel-data').DataTable();
	        $('#tabel-data-kota').DataTable();
	    });
		var flagWinner = 1;

	 	<?php
			$myfile = fopen("status.txt", "r") or die("Unable to open file!");
			echo "flagWinner = ".fgets($myfile);
			fclose($myfile);
		?>

		$(document).ready(function() {

			if(flagWinner > 0) {
				$("#draw_action").attr("disabled", true);
				$('#draw_title').html('<i class="ion-time"></i> Pengundian selesai dilaksanakan');
				$("#pesertaList").show();
				$('html, body').animate({ scrollTop:3500 },"fast");
			}
		});

		var pesertaClick = 0;
		$('#peserta').click(function() {
			if(pesertaClick === 0) {
				pesertaClick = 1
				$("#pesertaList").show();
				$("#dataList").hide();
			} else {
				pesertaClick = 0
				$("#pesertaList").hide();
			}
		});

		var datapesertaClick = 0;
		$('#datapeserta').click(function() {
			if(datapesertaClick === 0) {
				datapesertaClick = 1
				$("#dataList").show();
				$("#pesertaList").hide();
			} else {
				datapesertaClick = 0
				$("#dataList").hide();
			}
		});

        $('#reset_database').click(function() {
	        	if (confirm('Anda ingin memulihkan data pemenang undian ?')) {
				    window.location.href = "peserta.php?q=r";
				    setTimeout(location.reload.bind(location), 100);
				}
		});
	</script>
	
</body>
</html>