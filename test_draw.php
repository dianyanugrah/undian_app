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
		$rows = $dataKotaPeserta;

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
					
					<h1 class="title" id="pad6" style="color: #F84982;"><b>PENGUNDIAN PERIODE 1</b></h1>
					<button class="notify-btn" id="draw_action"><b id="draw_title">MULAI MENGUNDI</b></button>
					<button style="display: none;" class="notify-btn" id="draw_stop"><b id="draw_title">STOP PENGUNDIAN</b></button>
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
	
	<div id="pemenangList" class="main-area-wrapper" style="display: none">
		<div class="center-text">
			<!-- LIST PEMENANG -->
			<div id="showlist" class="display-table">
				<div class="display-table-cell">
					<h1 style="color: #fff;text-shadow: 1px 1px #000;" class="title"><b>PEMENANG TRAVELOKA VOUCHER</b></h1>
					<h3 style="color: #fff;text-shadow: 1px 1px #000;" class="title"><b>FUNTASTIK FUNWARI</b></h3>

					<table class="table table-bordered">
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
					    <tbody style="color: #fff" id="showAjax"></tbody>
					    <tbody id="export_btn" style="display: none">
					      	<tr>
					      		<td colspan="7">
					      			<a href="multi_draw_traveloka.php?q=p&u=1" id="export_winner" style="background-color: #DAB000; color: #fff; padding: 15px"><i class="ion-android-arrow-down"></i> Export Data Pemenang</a>
					      		</td>
					      	</tr>
					    </tbody>
				  	</table>
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
		var random2 = new Array('J9552520010707t3h7','j5033810010707m5y7','C390123001070749z3','b3163050010707x3x2', 'EG0182000108078KT3', 'EG018200010807C678', 'UC848390010807YDW5','BA825000010707WKH5');
		Array.prototype.vlookup = function(index) {
		      return this[index];
		  }

		  function getRandomInt (min, max) {
		      return Math.floor(Math.random() * (max - min + 1)) + min;
		  }
		var flagWinner = 0;

	 	<?php
			$myfile = fopen("status_req.txt", "r") or die("Unable to open file!");
			echo "flagWinner = ".fgets($myfile);
			fclose($myfile);
		?>

		$(document).ready(function() {
			$('#tabel-data').DataTable();
			if(flagWinner > 0) {
				$("#draw_action").attr("disabled", true);
				$('#draw_title').html('<i class="ion-time"></i> Pengundian selesai di laksanakan');
				$("#pemenangList").show();

				$.ajax({ 
			        url: 'win_traveloka.php',
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

        
    	$(function (e) {

    		var nomor = 1;
			function show_winner() {
				$.ajax({ 
			        url: 'win_traveloka.php',
			        data: {"action": "show_multi_winner1"},
			        type: 'post',
			        success: function(result) {
		            	if(result.process === 1) {
		            		if(result.jumlah === 1) {
		            			console.log(result.pemenang)
		            			$("#showAjax").append('<tr><td>'+nomor+'</td><td>'+result.pemenang[0].id_user+'</td><td>'+result.pemenang[0].no_trx+'</td><td>'+result.pemenang[0].kode_input+'</td><td>'+result.pemenang[0].full_name+'</td><td>'+result.pemenang[0].no_telp.substring(0, 8)+'xxx</td><td>'+result.pemenang[0].kota+'</td></tr>');
		            			nomor++;

		            			$('html, body').animate({ scrollTop:3500 },"fast");
		            		} else {
		            			clearInterval(ajaxGet);
		            			clearInterval(luckyDrawSpin);
		            			$('#pad6').html('<b>PENGUNDIAN PERIODE 1</b>');
		            			flagWinner = 1;

								$("#export_btn").show();
		            		}
		            	} else {
		            		alert('Success show_winner Exception:'+result.process);
		            	}
			        },
			        error:function(jqXHR,textStatus,errorThrown ){
				      	alert('Eroor show_winner Exception 2:'+errorThrown );
				   	}
			    });
			}

		      var luckyDrawSpin = null;
		      function StartSpin() {
		        luckyDrawSpin = setInterval(function() {
		          var r = getRandomInt(0,random2.length-1);
		              $('#pad6').text(random2.vlookup(r));
		              $('#drawid').text(r+1);
		          },5);
		      }

		      $('#draw_action').click(function() {
		        // callback disini
		        StartSpin();
		        console.log('sudah klik start');
		        $("#draw_action").attr("disabled", true);
				$("#draw_action").hide();
				$("#draw_stop").show();
				$("#showinput").hide();
				$('#draw_title').html('<i class="ion-time"></i> Sedang mengundi...');
		      });

		      $('#draw_stop').click(function() {
		      	$.ajax({ 
			        url: 'win_traveloka.php',
			        data: {"action": "multi_draw"},
			        type: 'post',
			        success: function(result) {
			        	console.log(result)
		            	if(result.process === 1) {
							$("#tools").show();
							$("#pemenangList").show();
							ajaxGet = setInterval(function() {
								show_winner()
							},500);
		            	} else {
		            		alert('Success Exception 1:'+result.process);
		            	}
			        },
			        error:function(jqXHR,textStatus,errorThrown ){
				      	alert('Eroor Exception 1:'+errorThrown );
				   	}
			    });

	        	$("#draw_stop").attr("disabled", true);
	        	$('#draw_stop').html('<i class="ion-stopwatch"></i> Pengundian selesai.');
	        	// $('#pad6').html('<b>PENGUNDIAN PERIODE 1</b>')
		        clearInterval(luckyDrawSpin);
		      });

		  })
	</script>
	
</body>
</html>