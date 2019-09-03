<?php
require('XLSXReader.php');
$xlsx = new XLSXReader('peserta/Plain-RekapFiesta-5.xlsx');

$data = $xlsx->getSheetData('Sheet1');

// echo "<pre>";

foreach ($data as $key => $value) {
	# code...
	$listNew[$key]['ID'] 			= 'a'.$value[0];
	$listNew[$key]['NAMA'] 			= strtoupper($value[1]);
	$listNew[$key]['ALAMAT'] 		= strtoupper($value[2]);
	$listNew[$key]['KOTA'] 			= strtoupper($value[3]);
	$listNew[$key]['NO_KTP'] 		= $value[4];
	$listNew[$key]['NO_TELPON'] 	= $value[5];
	$listNew[$key]['KODE_STRUK'] 	= strtoupper($value[6]);
	$listNew[$key]['OUTLET'] 		= strtoupper($value[7]);
	$listNew[$key]['KET'] 			= $value[8];
	$listNew[$key]['STAT'] 			= 1;

	// NAMA
	if(strpos(strtolower($value[1]), 'kosong') !== false) {
		$listNew[$key]['STAT'] 		= 0;

	// NAMA
	} else if(strpos(strtolower($value[1]), 'tidak ada') !== false) {
		$listNew[$key]['STAT'] 		= 0;

	// ALAMAT
	} else if(strpos(strtolower($value[2]), 'tidak ada') !== false) {
		$listNew[$key]['STAT'] 		= 0;

	// KOTA
	} else if(strpos(strtolower($value[3]), 'tidak ada') !== false) {
		$listNew[$key]['STAT'] 		= 0;

	// NO_KTP
	} else if(strpos(strtolower($value[4]), 'tidak ada') !== false) {
		$listNew[$key]['STAT'] 		= 0;

	// NO_TELPON
	} else if(strpos(strtolower($value[5]), 'tidak ada') !== false) {
		$listNew[$key]['STAT'] 		= 0;

	// KODE_STRUK
	} else if(strpos(strtolower($value[6]), 'tidak ada') !== false) {
		$listNew[$key]['STAT'] 		= 0;

	// OUTLET
	} else if(strpos(strtolower($value[7]), 'tidak ada') !== false) {
		$listNew[$key]['STAT'] 		= 0;

	// KET
	} else if(strpos(strtolower($value[8]), 'c') !== false) {
		$listNew[$key]['STAT'] 		= 0;
	} 
}
// echo "<pre>";
// print_r($listNew); exit();



// $i=0;
// foreach ($data as $key => $value) {
// 	# code...
// 	foreach ($value as $k => $v) {
// 		$newData[$i][] = str_replace("\n", '', $value[0]);
// 		$i++;	
// 	}
// }

// print_r($data);


mysql_connect('localhost','development','d4t4ny4dev');
mysql_select_db('undian');

foreach ($listNew as $key => $value) {
	# code...
	$sqlExec = "INSERT INTO fiesta_peserta_opt (id,nama,alamat,kota,no_ktp,no_telpon,kode_struk,outlet,win,stat,datetime_input) VALUES ('".$value['ID']."','".$value['NAMA']."','".$value['ALAMAT']."','".mysql_escape_string($value['KOTA'])."','".$value['NO_KTP']."','".$value['NO_TELPON']."','".$value['KODE_STRUK']."','".$value['OUTLET']."','0','".$value['STAT']."','".date('Y-m-d H:i:s')."')";
	$query = mysql_query($sqlExec);

	echo $sqlExec."\n\n\n";
}

echo "Input Done";

?>