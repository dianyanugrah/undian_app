<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	if($_POST['action'] == 'single_draw') {

		mysql_connect('localhost','root','');
		mysql_select_db('funtasti_db');

		$limit = $_POST['limit'];
		// Array PEmenang
		$pemenang_list = array();

		// PEMENANG SINGLE DRAW BY LIMIT REQUEST
		$query_rand_1 = "SELECT users_tb.id_user, users_tb.full_name, users_tb.no_id_ktp, users_tb.no_telp, users_tb.kota, users_tb.alamat, kode_tb.no_trx, kode_tb.kode_input
            FROM users_tb 
            INNER JOIN kode_tb ON users_tb.no_id_ktp = kode_tb.no_id_ktp 
            WHERE users_tb.status_user = 0 AND win = 0 AND users_tb.no_id_ktp REGEXP '^[0-9]+$' AND users_tb.no_telp REGEXP '^[0-9]+$' GROUP BY users_tb.no_id_ktp
            ORDER BY RAND() LIMIT $limit";
		$sql_rand_1 = mysql_query($query_rand_1);
		while ($rows_rand_1 = mysql_fetch_assoc($sql_rand_1)) {
			$pemenang_list[] = $rows_rand_1;
		}

		shuffle($pemenang_list); 
		

		//EXEC TO DB
		foreach ($pemenang_list as $key => $value) {
			$save_win_query = "INSERT INTO pemenang_tb (id_peserta,jenis_hadiah,jenis_undian, datetime_winner,stat) VALUES (".$value['id_user'].",2,2,'".date('Y-m-d H:i:s')."', 1)";
			$save_win_exec = mysql_query($save_win_query);

			if($save_win_exec) {
				$winner_win_query = "UPDATE users_tb SET status_user = 1, win = 1, win_datetime = '".date('Y-m-d H:i:s')."' WHERE no_id_ktp = ".$value['no_id_ktp']."";
				$save_win_exec = mysql_query($winner_win_query);
			}
		}

		// TO AJAX
		$win = array(
			'process' => 1
		);

		header('Content-type: application/json');
		echo json_encode($win);

		$handleAppend = fopen('status_req.txt', 'w') or die('Cannot open file handleAppend');
		fwrite($handleAppend, '1'); exit();

	} else if($_POST['action'] == 'show_single_winner') {

		mysql_connect('localhost','root','');
		mysql_select_db('funtasti_db');

		// Array PEmenang
		$pemenang_list = array();

		$query_rand = "SELECT pem.id, users_tb.id_user, users_tb.full_name, users_tb.no_id_ktp, users_tb.no_telp, users_tb.kota, users_tb.alamat, kode_tb.no_trx, kode_tb.kode_input
            FROM users_tb 
            INNER JOIN pemenang_tb pem ON pem.id_peserta = users_tb.id_user
            INNER JOIN kode_tb ON users_tb.no_id_ktp = kode_tb.no_id_ktp 
            WHERE pem.jenis_undian = 2 AND pem.jenis_hadiah = 2 AND pem.stat = 1 AND users_tb.no_id_ktp REGEXP '^[0-9]+$' AND users_tb.no_telp REGEXP '^[0-9]+$' GROUP BY users_tb.no_id_ktp";
		$sql_rand = mysql_query($query_rand);
		while ($rows_rand = mysql_fetch_assoc($sql_rand)) {
			$pemenang_list[] = $rows_rand;
		}


		$win = array(
			'process' => 1,
			'jumlah' => count($pemenang_list),
			'pemenang' => $pemenang_list
		);

		header('Content-type: application/json');
		echo json_encode($win);

	} else if($_POST['action'] == 'show_single_winner1') {

		mysql_connect('localhost','root','');
		mysql_select_db('funtasti_db');

		// Array Pemenang Single
		$pemenang_data = array();

		$query_data = "SELECT pem.id, users_tb.id_user, users_tb.full_name, users_tb.no_id_ktp, users_tb.no_telp, users_tb.kota, users_tb.alamat, kode_tb.no_trx, kode_tb.kode_input
            FROM users_tb 
            INNER JOIN pemenang_tb pem ON pem.id_peserta = users_tb.id_user
            INNER JOIN kode_tb ON users_tb.no_id_ktp = kode_tb.no_id_ktp 
            WHERE pem.show_ajax = 0 AND pem.jenis_undian = 2 AND pem.jenis_hadiah = 2 AND pem.stat = 1 AND users_tb.no_id_ktp REGEXP '^[0-9]+$' AND users_tb.no_telp REGEXP '^[0-9]+$'  GROUP BY no_id_ktp ORDER BY RAND() LIMIT 1";
		$sql_data = mysql_query($query_data);
		while ($rows_data = mysql_fetch_assoc($sql_data)) {
			$pemenang_data[] = $rows_data;
		}

		if(count($pemenang_data) > 0) {
			foreach ($pemenang_data as $key => $value) {
				$winner_update_query = "UPDATE pemenang_tb SET show_ajax = 1 WHERE id = ".$value['id']."";
				$update_winner_exec = mysql_query($winner_update_query);
			}
		}


		// TO AJAX
		$win = array(
			'process' => 1,
			'jumlah' => count($pemenang_data),
			'pemenang' => $pemenang_data
		);

		header('Content-type: application/json');
		echo json_encode($win);

	} else if($_POST['action'] == 'multi_draw') {

		mysql_connect('localhost','root','');
		mysql_select_db('funtasti_db');
		// Array PEmenang
		$pemenang_list = array();

		// PEMENANG SINGLE DRAW BY LIMIT REQUEST
		$query_rand_1 = "SELECT users_tb.id_user, users_tb.full_name, users_tb.no_id_ktp, users_tb.no_telp, users_tb.kota, users_tb.alamat, kode_tb.no_trx, kode_tb.kode_input
            FROM users_tb 
            INNER JOIN kode_tb ON users_tb.no_id_ktp = kode_tb.no_id_ktp 
            WHERE users_tb.status_user = 0 AND win = 0 AND users_tb.no_id_ktp REGEXP '^[0-9]+$' AND users_tb.no_telp REGEXP '^[0-9]+$' GROUP BY users_tb.no_id_ktp
            ORDER BY RAND() LIMIT 5";
		$sql_rand_1 = mysql_query($query_rand_1);
		while ($rows_rand_1 = mysql_fetch_assoc($sql_rand_1)) {
			$pemenang_list[] = $rows_rand_1;
		}

		shuffle($pemenang_list); 
		

		//EXEC TO DB
		foreach ($pemenang_list as $key => $value) {
			$save_win_query = "INSERT INTO pemenang_tb (id_peserta,jenis_hadiah, jenis_undian,datetime_winner,stat) VALUES (".$value['id_user'].",2,1,'".date('Y-m-d H:i:s')."', 1)";
			$save_win_exec = mysql_query($save_win_query);

			if($save_win_exec) {
				$winner_win_query = "UPDATE users_tb SET status_user = 1, win = 1, win_datetime = '".date('Y-m-d H:i:s')."' WHERE no_id_ktp = ".$value['no_id_ktp']."";
				$save_win_exec = mysql_query($winner_win_query);
			}
		}

		// TO AJAX
		$win = array(
			'process' => 1
		);

		header('Content-type: application/json');
		echo json_encode($win);

		$handleAppend = fopen('status_req.txt', 'w') or die('Cannot open file handleAppend');
		fwrite($handleAppend, '1'); exit();

	} else if($_POST['action'] == 'show_multi_winner') {

		mysql_connect('localhost','root','');
		mysql_select_db('funtasti_db');

		// Array PEmenang
		$pemenang_list = array();

		$query_rand = "SELECT pem.id, users_tb.id_user, users_tb.full_name, users_tb.no_id_ktp, users_tb.no_telp, users_tb.kota, users_tb.alamat, kode_tb.no_trx, kode_tb.kode_input
            FROM users_tb 
            INNER JOIN pemenang_tb pem ON pem.id_peserta = users_tb.id_user
            INNER JOIN kode_tb ON users_tb.no_id_ktp = kode_tb.no_id_ktp 
            WHERE pem.jenis_undian = 1 AND pem.jenis_hadiah = 2 AND pem.stat = 1 AND users_tb.no_id_ktp REGEXP '^[0-9]+$' AND users_tb.no_telp REGEXP '^[0-9]+$' GROUP BY users_tb.no_id_ktp";
		$sql_rand = mysql_query($query_rand);
		while ($rows_rand = mysql_fetch_assoc($sql_rand)) {
			$pemenang_list[] = $rows_rand;
		}


		$win = array(
			'process' => 1,
			'jumlah' => count($pemenang_list),
			'pemenang' => $pemenang_list
		);

		header('Content-type: application/json');
		echo json_encode($win);

	} else if($_POST['action'] == 'show_multi_winner1') {

		mysql_connect('localhost','root','');
		mysql_select_db('funtasti_db');

		// Array Pemenang Single
		$pemenang_data = array();


		$query_data = "SELECT pem.id, users_tb.id_user, users_tb.full_name, users_tb.no_id_ktp, users_tb.no_telp, users_tb.kota, users_tb.alamat, kode_tb.no_trx, kode_tb.kode_input
            FROM users_tb 
            INNER JOIN pemenang_tb pem ON pem.id_peserta = users_tb.id_user
            INNER JOIN kode_tb ON users_tb.no_id_ktp = kode_tb.no_id_ktp 
            WHERE pem.show_ajax = 0 AND pem.jenis_undian = 1  AND pem.jenis_hadiah = 2 AND pem.stat = 1 AND users_tb.no_id_ktp REGEXP '^[0-9]+$' AND users_tb.no_telp REGEXP '^[0-9]+$'  GROUP BY no_id_ktp ORDER BY RAND() LIMIT 1";
		$sql_data = mysql_query($query_data);
		while ($rows_data = mysql_fetch_assoc($sql_data)) {
			$pemenang_data[] = $rows_data;
		}

		if(count($pemenang_data) > 0) {
			foreach ($pemenang_data as $key => $value) {
				$winner_update_query = "UPDATE pemenang_tb SET show_ajax = 1 WHERE id = ".$value['id']."";
				$update_winner_exec = mysql_query($winner_update_query);
			}
		}


		// TO AJAX
		$win = array(
			'process' => 1,
			'jumlah' => count($pemenang_data),
			'pemenang' => $pemenang_data
		);

		header('Content-type: application/json');
		echo json_encode($win);
	}
} else {
	// mysql_connect('localhost','root','');
	// mysql_select_db('undian');

	// // Array PEmenang
	// $pemenang_list = array();

	// // PEMENANG 1 = JAKARTA + PRIMAFOOD = 5
	// $arr_rand_1 = array();
	// $query_rand_1 = "SELECT * FROM fiesta_peserta WHERE kota = 'JAKARTA' AND outlet = 'PRIMAFOOD INTERNATIONAL' AND stat = 1 ORDER BY RAND() LIMIT 5";
	// $sql_rand_1 = mysql_query($query_rand_1);
	// while ($rows_rand_1 = mysql_fetch_assoc($sql_rand_1)) {
	// 	$pemenang_list[] = $rows_rand_1;
	// }


	// // PEMENANG 2 = JABODETATANGSELBEK = 5
	// $query_rand_2 = "SELECT * FROM fiesta_peserta WHERE kota = 'BOGOR' OR kota = 'DEPOK' OR kota = 'TANGERANG' OR kota = 'TANGERANG SELATAN' OR kota = 'BEKASI' AND stat = 1 ORDER BY RAND() LIMIT 5";
	// $sql_rand_2 = mysql_query($query_rand_2);
	// while ($rows_rand_2 = mysql_fetch_assoc($sql_rand_2)) {
	// 	$pemenang_list[] = $rows_rand_2;
	// }


	// // PEMENANG 3 = SERANG CILEGON SEMARANG SURABAYA SIDOARJO BANDUNG = 5
	// $query_rand_3 = "SELECT * FROM fiesta_peserta WHERE kota = 'SERANG' OR kota = 'CILEGON' OR kota = 'SEMARANG' OR kota = 'SURABAYA' OR kota = 'SIDOARJO' OR kota = 'BANDUNG' AND stat = 1 ORDER BY RAND() LIMIT 5";
	// $sql_rand_3 = mysql_query($query_rand_3);
	// while ($rows_rand_3 = mysql_fetch_assoc($sql_rand_3)) {
	// 	$pemenang_list[] = $rows_rand_3;
	// }


	// echo "<pre>";
 //  	shuffle($pemenang_list); 
	// print_r($pemenang_list);
}

?>