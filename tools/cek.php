<?php

mysql_connect('localhost','development','d4t4ny4dev');
mysql_select_db('undian');

$sql = "SELECT * FROM fiesta_peserta";
$query = mysql_query($sql);

$datas = array();

while ($rows = mysql_fetch_assoc($query)) {
	# code...
	$datas[] = $rows;
}

// echo "<pre>"; print_r($datas); exit();

$datasSec = array();

foreach ($datas as $key => $value) {
	# code...
	// if($value['id_peserta'] != $value['id']) {
	// 	$datasSec[$key] = $value;
	// }

	if($value['id'] - $value['id_peserta'] > 2) {
		$datasSec[$key] = $value;
	}
}

echo "<pre>"; print_r($datasSec);

?>