<?php

function redirect_to($url = '') {
	header('Location: '.$url);
	exit();
}

function cek_login() {
	
	if(isset($_SESSION['username'])) {
		// do nothing
	} else {
		redirect_to("login.php");
	}	
}

function get_role() {
	
	if(isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
		if($_SESSION['role'] == '1') {
			return 'user';
		} 
	} else {
		return false;
	}	
}

function get_sesi(){
	$sesi=1;
	if(isset($_SESSION['sesi']) && isset($_SESSION['sesi'])) {
		$sesi = $_SESSION['sesi'];
	} 
	return $sesi;	
}


function truncateText($text, $maxLength, $ellipsis = '...') {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . $ellipsis;
    } else {
        return $text;
    }
}

function convertTanggal($tanggal) {
    // Mengubah format tanggal menjadi objek DateTime
    $date = new DateTime($tanggal);
    // Mendapatkan nama bulan dalam Bahasa Indonesia
    $bulan = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    );
    // Mendapatkan tanggal, bulan, dan tahun
    $tanggal = $date->format('j');
    $bulan = $bulan[$date->format('n')];
    $tahun = $date->format('Y');
    // Menggabungkan dan mengembalikan hasil
    return $tanggal . ' ' . $bulan . ' ' . $tahun;
}


?>