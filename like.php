<?php
// Koneksi ke database disini
require_once('includes/init.php');

$user_role = get_role();
$sesi = get_sesi();

if($_SESSION['username']==""){
    redirect_to("login.php");
} 

if(isset($_POST['foto_id'])) {
    $fotoId = $_POST['foto_id'];
    $userId=$_SESSION['user_id'];
    
    // Periksa apakah user sudah melakukan like sebelumnya
    $query_check_like = mysqli_query($koneksi, "SELECT * FROM like_foto WHERE foto_id = '$fotoId' AND user_id = '$userId'");
    $num_likes = mysqli_num_rows($query_check_like);

    if($num_likes > 0) {
        // Jika sudah like, hapus like
        $query_delete_like = mysqli_query($koneksi, "DELETE FROM like_foto WHERE foto_id = '$fotoId' AND user_id = '$userId'");
        if($query_delete_like) {
            $response['status'] = 'unliked';
        } else {
            $response['status'] = 'error';
        }
    } else {
        // Jika belum like, tambahkan like
        $query_insert_like = mysqli_query($koneksi, "INSERT INTO like_foto (foto_id, user_id, tanggal_like) VALUES ('$fotoId', '$userId', NOW())");
        if($query_insert_like) {
            $response['status'] = 'liked';
        } else {
            $response['status'] = 'error';
        }
    }

    // Menghitung jumlah total like setelah like atau unlike
    $query_total_like = mysqli_query($koneksi, "SELECT COUNT(*) AS total_like FROM like_foto WHERE foto_id = '$fotoId'");
    $total_like = mysqli_fetch_assoc($query_total_like)['total_like'];
    $response['total_like'] = $total_like;

    // Mengirim respons dalam format JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Jika tidak ada data foto_id yang dikirimkan melalui POST
    $response['status'] = 'error';
    echo json_encode($response);
}
?>
