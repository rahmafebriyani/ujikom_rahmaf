<?php
require_once('includes/init.php');
cek_login();

if(isset($_GET['foto_id'])) {
    $foto_id = $_GET['foto_id'];
    
    // Hapus foto dari database
    $query_delete_like = mysqli_query($koneksi, "DELETE FROM like_foto WHERE foto_id = '$foto_id'");
    $query_delete_komentar = mysqli_query($koneksi, "DELETE FROM komentar_foto WHERE foto_id = '$foto_id'");
    $query_delete_foto = mysqli_query($koneksi, "DELETE FROM foto WHERE foto_id = '$foto_id'");
    
    if($query_delete_foto) {
        // Redirect ke halaman dashboard dengan status sukses-hapus
        redirect_to("dashboard.php?status=sukses-hapus");
    } else {
        // Jika gagal menghapus, tampilkan pesan error
        echo "Gagal menghapus foto.";
    }
} else {
    // Jika tidak ada foto_id yang diberikan, redirect ke halaman dashboard
    redirect_to("dashboard.php");
}
?>
