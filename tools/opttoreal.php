<?php

mysql_connect('localhost','development','d4t4ny4dev');
mysql_select_db('undian');


$optQuery = mysql_query("SELECT * FROM fiesta_peserta_opt");
while ($optRows = mysql_fetch_assoc($optQuery)) {
	$optNewData[] = $optRows;
}

// echo "<pre>"; print_r($optNewData); exit();


foreach ($optNewData as $key => $value) {
	# code...
	$sqlExec = "INSERT INTO fiesta_peserta (id,nama,alamat,kota,no_ktp,no_telpon,kode_struk,outlet,win,stat,datetime_input) VALUES ('e".$value['id']."','".$value['nama']."','".$value['alamat']."','".mysql_escape_string($value['kota'])."','".$value['no_ktp']."','".$value['no_telpon']."','".$value['kode_struk']."','".$value['outlet']."','0','".$value['stat']."','".date('Y-m-d H:i:s')."')";
	$query = mysql_query($sqlExec);

	echo $sqlExec."\n\n\n";
}


?>