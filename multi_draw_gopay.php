<?php
// error_reporting(E_ALL);

mysql_connect('localhost','root','');
mysql_select_db('funtasti_db');

// DATA PESERTA BY KOTA
$dataKotaPeserta = array();
$kotaPesertaQuery = "SELECT COUNT(no_trx) peserta, users_tb.full_name,users_tb.no_telp,kode_tb.no_trx,kode_tb.kode_input,users_tb.email_address,users_tb.no_id_ktp,users_tb.alamat,users_tb.kota,users_tb.datetime_create from users_tb join kode_tb ON kode_tb.no_id_ktp = users_tb.no_id_ktp GROUP BY kota ORDER BY peserta DESC";

$kotaPesertaData = mysql_query($kotaPesertaQuery);
while ($rowsKotaPeserta = mysql_fetch_assoc($kotaPesertaData)) {
	$dataKotaPeserta[] = $rowsKotaPeserta;
}


// DATA PEMENANG 1
$dataPemenang = array();

$dataPemenangQuery = "SELECT pem.id, users_tb.id_user, users_tb.full_name, users_tb.no_id_ktp, users_tb.no_telp, users_tb.kota, users_tb.alamat, kode_tb.no_trx, kode_tb.kode_input
            FROM users_tb 
            INNER JOIN pemenang_tb pem ON pem.id_peserta = users_tb.id_user
            INNER JOIN kode_tb ON users_tb.no_id_ktp = kode_tb.no_id_ktp 
            WHERE pem.jenis_undian = 1 AND pem.jenis_hadiah = 1 AND pem.stat = 1 AND users_tb.no_id_ktp REGEXP '^[0-9]+$' AND users_tb.no_telp REGEXP '^[0-9]+$' GROUP BY users_tb.no_id_ktp";

$dataPemenangExec = mysql_query($dataPemenangQuery);
while ($rowDataPemenang = mysql_fetch_assoc($dataPemenangExec)) {
	$dataPemenang[] = $rowDataPemenang;
}


$listNew = array();
$no = 1;
foreach ($dataPemenang as $key => $value) {
	$listNew[$key]['ID'] 			= $no;
	$listNew[$key]['NO_TRX'] 		= strtoupper($value['no_trx']);
	$listNew[$key]['KODE'] 		= strtoupper($value['kode_input']);
	$listNew[$key]['NAMA'] 			= strtoupper($value['full_name']);
	$listNew[$key]['NO_KTP'] 		= "'".$value['no_id_ktp'];
	$listNew[$key]['NO_TELPON'] 	= "'".$value['no_telp'];
	$listNew[$key]['KOTA'] 		= strtoupper($value['kota']);
	$no++;
}

if(isset($_GET['q'])) {

	include_once("tools/xlsxwriter.class.php");

	if($_GET['q'] == 'o') {
		// CLEAR / DESTROY SESSION
		header('Location: index.php');
	} else if($_GET['q'] == 'r') {
		// echo "Reset Data Pemenang";
		$update_stat_pemenang = "UPDATE pemenang_tb SET stat = 2 WHERE jenis_hadiah = 1 AND jenis_undian = 1";
		$update_stat_pemenang_exec = mysql_query($update_stat_pemenang);

		if($update_stat_pemenang_exec) {
			$resetHandle = fopen('status.txt', 'w') or die('Cannot open file resetHandle');
			fwrite($resetHandle, '0'); 

			header('Location: single_draw_gopay.php');
		}
	} else if($_GET['q'] == 'p') {
		// echo "Export Data Pemenang";
		if($_GET['u'] == 1) {
			// echo "Undian 1;
			$filename = "Data_Pemenang_Gopay_Multi_Draw".date('YmdHis').".xlsx";
			$rows = $listNew;
		}
	} else if($_GET['q'] == 'ek') {
		// echo "Export Data Kota";
		$filename = "Data_Peserta_By_Kota_".date('YmdHis').".xlsx";
		$rows = $dataKotaPeserta;

	}


	// EXPORT ACTION
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$writer = new XLSXWriter();
	$writer->setAuthor('Hello Word'); 
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
					
					<h1 class="title"><b>PENGUNDIAN HADIAH PERIODE 1</b></h1>
					<h3 class="title"><b>HADIAH GOPAY VOUCHER</b></h3>
					<p class="desc font-white">Pengundian hadiah 50.000 Gopay Voucher</p>
					
					<div id="normal-countdown">
						<div class="time-sec"><h3 id="pad1" class="main-time" style="line-height: 90px">0</h3></div>
						<div class="time-sec"><h3 id="pad2" class="main-time" style="line-height: 90px">0</h3></div>
						<div class="time-sec"><h3 id="pad3" class="main-time" style="line-height: 90px">0</h3></div>
						<div class="time-sec"><h3 id="pad4" class="main-time" style="line-height: 90px">0</h3></div>
						<div class="time-sec"><h3 id="pad5" class="main-time" style="line-height: 90px">0</h3></div>
						<div class="time-sec"><h3 id="pad6" class="main-time" style="line-height: 90px">0</h3></div>
					</div>
					<div class="col-md-12" id="showinput" style="display: none;">
						<div class="row">
							<div class="col-md-4"></div>
							<div class="col-md-4">
								<input id="limitwinner" style="color: #000" name="limitwinner" placeholder="amount winner" class="form-group">
							</div>
							<div class="col-md-4"></div>
						</div>
					</div>

					<button class="notify-btn" id="draw_action"><b id="draw_title">MULAI MENGUNDI</b></button>
					<button style="display: none;" class="notify-btn" id="draw_stop"><b id="draw_title">STOP PENGUNDIAN</b></button>

					<ul id="tools" class="social-btn">
						<li class="list-heading">Tools</li>
						<li><a id="reset" style="background-color: #F43846; padding-right: 15px"><i class="ion-refresh"></i> Reset</a></li>

						<li><a id="pemenang" style="background-color: #53F83A; padding-right: 15px" href="#showlist"><i class="ion-trophy"></i> Pemenang</a></li>

						<li><a id="singledraw" style="background-color: #FF8C00; padding-right: 15px" href="single_draw_gopay.php"><i class="ion-shuffle"></i> Single Draw Gopay</a></li>

						<li><a id="datapeserta" style="background-color: #000; padding-right: 15px" href="#showlistpeserta"><i class="ion-archive"></i> Data</a></li>

						<li><a id="peserta" style="background-color: #2F4F4F; padding-right: 15px" href="peserta.php"><i class="ion-archive"></i> Peserta</a></li>
					</ul>
					
				</div><!-- display-table -->
			</div><!-- display-table-cell -->
		</div><!-- main-area -->
	</div><!-- main-area-wrapper -->

	<div id="pemenangList" class="main-area-wrapper" style="display: none">
		<div class="center-text">
			<!-- LIST PEMENANG -->
			<div id="showlist" class="display-table">
				<div class="display-table-cell">
					<h1 style="color: #fff;text-shadow: 1px 1px #000;" class="title"><b>PEMENANG HADIAH GOPAY VOUCHER</b></h1>
					<h3 style="color: #fff;text-shadow: 1px 1px #000;" class="title"><b>FUNTASTIK FUNWARI</b></h3>
				  	<div class="col-md-12">
						<div class="row">
							<div class="panel panel-primary">
							  	<div class="panel-body">
							    	<table id="tabel-data-pemenang" class="table table-striped table-bordered" width="100%" cellspacing="0">
									    <thead>
									      <tr class="danger">
									        <th style="text-align: center;">NO</th>
									        <th style="text-align: center;">ID</th>
									        <th style="text-align: center;">NO TRX</th>
									        <th style="text-align: center;">KODE</th>
									        <th style="text-align: center;">NAMA</th>
									        <th style="text-align: center;">NO TELP</th>
									        <th style="text-align: center;">KOTA</th>
									      </tr>
									    </thead>
									    <tbody>
									    	<?php 
										    	$no = 1;
										    	foreach ($dataPemenang as $key => $value) { ?>
										    	<tr>
										    		<td><?php echo $no; ?></td>
										    		<td><?php echo $value['id_user'];?></td>
										    		<td><?php echo $value['no_trx'];?></td>
										    		<td><?php echo $value['kode_input'];?></td>
										    		<td><?php echo $value['full_name'];?></td>
										    		<td><?php echo $value['no_telp'];?></td>
										    		<td><?php echo $value['kota'];?></td>
										    	</tr>
										    	<?php $no++; } ?>
									    </tbody>
									</table>
							  	</div>
							  	<div class="panel-footer">
							  		<a href="multi_draw_gopay.php?q=p&u=1" id="export_winner" style="background-color: #DAB000; color: #fff; padding: 15px"><i class="ion-android-arrow-down"></i> Export Data Pemenang</a>
							  	</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



	<!-- <div id="dataList" class="main-area-wrapper" style="display: none"> -->
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
								    	<table id="tabel-data" class="table table-bordered">
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
								  		<a href="multi_draw_gopay.php?q=ek" id="export_kota" style="background-color: #DAB000; color: #fff; padding: 15px"><i class="ion-android-arrow-down"></i> Export Data</a>
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
	<script type="text/javascript" src="assets/DataTables/datatables.min.js"></script>
	<!-- <script src="assets/common-js/jquery.countdown.min.js"></script> -->
	
	<!-- <script src="assets/common-js/scripts.js"></script> -->


	<script type="text/javascript">
		var flagWinner = 0;

	 	<?php
			$myfile = fopen("status_req.txt", "r") or die("Unable to open file!");
			echo "flagWinner = ".fgets($myfile);
			fclose($myfile);
		?>

		$(document).ready(function() {
			$('#tabel-data').DataTable();
			$('#tabel-data-pemenang').DataTable();
			if(flagWinner > 0) {
				// $("#draw_action").attr("disabled", true);
				// $('#draw_title').html('<i class="ion-time"></i> Pengundian selesai di laksanakan');
				$("#pemenangList").show();

				$.ajax({ 
			        url: 'win_gopay.php',
			        data: {"action": "show_multi_winner"},
			        type: 'post',
			        success: function(result) {
		            	// console.log(result.pemenang)

		            	if(result.jumlah > 0) {

			            	var no = 1;
			            	var pemenangList = result.pemenang;

			            	for(let i = 0; i < pemenangList.length; i++) {

			            		// console.log(pemenangList[i].nama);

			            		$("#showAjax").append('<tr><td>'+no+'</td><td>'+pemenangList[i].id_user+'</td><td>'+pemenangList[i].no_trx+'</td><td>'+pemenangList[i].kode_input.substring(0, 20)+'xxx</td><td>'+pemenangList[i].full_name+'</td><td>'+pemenangList[i].no_telp.substring(0, 8)+'xxx</td><td>'+pemenangList[i].kota+'</td></tr>');

			            		no++;
			            	}

			            	$('html, body').animate({ scrollTop:3500 },"slow");
			            	$("#export_btn").show();
			            } else {
			            	$("#pemenangList").hide();
			            }

			        },
			        error:function(jqXHR,textStatus,errorThrown ){
				      	alert('Eroor show_winner Exception 1:'+errorThrown );
				   	}
			    });
			}
		});

		var datapesertaClick = 0;
		$('#datapeserta').click(function() {
			if(datapesertaClick === 0) {
				datapesertaClick = 1
				$("#dataList").show();
				$("#pemenangList").hide();
			} else {
				datapesertaClick = 0
				$("#dataList").hide();
			}
		});


		var pemenangClick = 1;
		$('#pemenang').click(function() {
			if(flagWinner === 1) {
				if(pemenangClick === 0) {
					pemenangClick = 1
					$("#pemenangList").show();
					$("#dataList").hide();
				} else {
					pemenangClick = 0
					$("#pemenangList").hide();
				}
			}
		});

		$('#draw_action').click(function(){
			$("#draw_action").attr("disabled", true);

			$("#draw_action").hide();
			$("#draw_stop").show();

			$("#showinput").hide();

			$('#draw_title').html('<i class="ion-time"></i> Sedang mengundi...');

			onItv = setInterval(function() {
				ubahAngkaAcak()
			},50);
			
		});

		var nomor = 1;
		function show_winner() {
			$.ajax({ 
		        url: 'win_gopay.php',
		        data: {"action": "show_multi_winner"},
		        type: 'post',
		        success: function(result) {
	            	// console.log(result.pemenang)

	            	if(result.jumlah > 0) {

		            	var no = 1;
		            	var pemenangList = result.pemenang;

		            	for(let i = 0; i < pemenangList.length; i++) {

		            		// console.log(pemenangList[i].nama);

		            		$("#showAjax").append('<tr><td>'+no+'</td><td>'+pemenangList[i].id_user+'</td><td>'+pemenangList[i].no_trx+'</td><td>'+pemenangList[i].kode_input.substring(0, 20)+'xxx</td><td>'+pemenangList[i].full_name+'</td><td>'+pemenangList[i].no_telp.substring(0, 8)+'xxx</td><td>'+pemenangList[i].kota+'</td></tr>');

		            		no++;
		            	}

		            	$('html, body').animate({ scrollTop:3500 },"slow");
		            	$("#export_btn").show();
		            } else {
		            	$("#pemenangList").hide();
		            }

		        },
		        error:function(jqXHR,textStatus,errorThrown ){
			      	alert('Eroor show_winner Exception 1:'+errorThrown );
			   	}
		    });
		}



		function angkaAcak() {
			var awal = 10;       // Nilai awal angka yang harus diacak
            var akhir= 99; 
            return Math.floor(Math.random() * (akhir - awal + 1) + awal);
        }

		function ubahAngkaAcak() {
            $("#pad1").text(angkaAcak());
            $("#pad2").text(angkaAcak());
            $("#pad3").text(angkaAcak());
            $("#pad4").text(angkaAcak());
            $("#pad5").text(angkaAcak());
            $("#pad6").text(angkaAcak());

   			// funcItv = setInterval(function() {
			// 	ubahAngkaAcak()
			// },50);

            // var funcItv = setTimeout('ubahAngkaAcak()', 50);
        }

        $('#reset').click(function() {
        	if(flagWinner === 1) {
	        	if (confirm('Anda ingin memulihkan data pemenang undian ?')) {
				    window.location.href = "multi_draw_gopay.php?q=r";
				    setTimeout(location.reload.bind(location), 100);
				}
			}
		});


        $('#draw_stop').click(function() {

        	// var limit = $('#limitwinner').val();
			// console.log(limit);
			$.ajax({ 
		        url: 'win_gopay.php',
		        data: {"action": "multi_draw"},
		        type: 'post',
		        success: function(result) {
		        	console.log(result)
	            	if(result.process === 1) {
	     //        		$("#draw_action").hide();
						// $("#draw_stop").show();
						$("#tools").show();
						$("#pemenangList").show();
						setTimeout(location.reload.bind(location), 100);
						// ajaxGet = setInterval(function() {
						// 	show_winner()
						// },500);
	            	} else {
	            		alert('Success Exception 1:'+result.process);
	            	}
		        },
		        error:function(jqXHR,textStatus,errorThrown ){
			      	alert('Eroor Exception 1:'+errorThrown );
			   	}
		    });

			// setTimeout(function() {
			// 	$("#draw_action").hide();
			// 	$("#draw_stop").show();
			// }, 2000);

			// var onItv = setTimeout('ubahAngkaAcak()', 50);

        	// $("#pemenangList").show();
        	// $("#tools").show();
        	$("#draw_stop").attr("disabled", true);
        	$('#draw_stop').html('<i class="ion-stopwatch"></i> Pengundian selesai.');

        	clearInterval(onItv);

        	$("#pad1").text('0');
            $("#pad2").text('0');
            $("#pad3").text('0');
            $("#pad4").text('0');
            $("#pad5").text('0');
            $("#pad6").text('0');
    	});
	</script>
	
</body>
</html>