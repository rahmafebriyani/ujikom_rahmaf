<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
$ada_error = false;
$result = '';

$id = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = mysqli_query($koneksi,"SELECT * FROM pemilik WHERE id='$id'");
	$cek = mysqli_num_rows($query);
	
	if($cek <= 0) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	} else {
		//ambil data kos
		$query = mysqli_query($koneksi,"SELECT * FROM kos WHERE pemilik_id='$id'");
		while($data = mysqli_fetch_array($query)) {
			//hapus user
			mysqli_query($koneksi,"DELETE FROM user WHERE id='$data[user_id]'");
			//hapus data kos
			mysqli_query($koneksi,"DELETE FROM kos WHERE id='$data[id]'");
			//hapus data fasilitas
			mysqli_query($koneksi,"DELETE FROM fasilitas WHERE id='$data[fasilitas_id]'");
			//hapus data lingkungan
			mysqli_query($koneksi,"DELETE FROM lingkungan WHERE id='$data[lingkungan_id]'");
			//hapus hasil
			mysqli_query($koneksi,"DELETE FROM hasil WHERE pemilik_id='$id'");
		}
		mysqli_query($koneksi,"DELETE FROM pemilik WHERE id='$id'");
		redirect_to('list-pemilik.php?status=sukses-hapus');
	}
}
?>

<?php
$page = "Pemilik";
require_once('template/header.php');
?>
	<?php if($ada_error): ?>
		<?php echo '<div class="alert alert-danger">'.$ada_error.'</div>'; ?>	
	<?php endif; ?>
<?php
require_once('template/footer.php');
?>
