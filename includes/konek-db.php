<?php 
$koneksi = mysqli_connect("localhost","root","","website_galeri_foto");
 
// Check connection
if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}

?>
 