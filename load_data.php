<?php
mysql_connect('localhost','root','adminadmin');
mysql_select_db('funtasti_db');

$perPage = 15;
if (isset($_GET["page"])) { 
	$page  = $_GET["page"]; 
} else { 
	$page=1; 
};  
$startFrom = ($page-1) * $perPage;  
$sqlQuery = "SELECT users_tb.id_user, users_tb.full_name,users_tb.no_telp,kode_tb.id_kode,kode_tb.no_trx,kode_tb.kode_input,users_tb.email_address,users_tb.no_id_ktp,users_tb.alamat,users_tb.kota,users_tb.datetime_create from users_tb join kode_tb ON kode_tb.no_id_ktp = users_tb.no_id_ktp ORDER BY kode_input";  
$result = mysql_query($sqlQuery); 
$paginationHtml = '';
$no = 1;
while ($row = mysql_fetch_assoc($result)) {  
	$paginationHtml.='<tr>';  
	$paginationHtml.='<td>'.$no.'</td>';
	$paginationHtml.='<td>'.$row["no_trx"].'</td>';
	$paginationHtml.='<td>'.$row["kode_input"].'</td>'; 
	$paginationHtml.='<td>'.$row["full_name"].'</td>';
	$paginationHtml.='<td>'.$row["kota"].'</td>';
	$paginationHtml.='<td>'.$row["datetime_create"].'</td>'; 
	$paginationHtml.='</tr>';  
	$no++;
} 

$jsonData = array(
	"html"	=> $paginationHtml,	
);
echo json_encode($jsonData); 
?>
