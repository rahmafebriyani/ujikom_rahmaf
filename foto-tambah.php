<?php require_once('includes/init.php'); ?>
<?php cek_login(); ?>

<?php
$sesi = get_sesi();
$errors = array();
$sukses = false;

if(isset($_POST['submit'])):	
	//judul_foto, deskripsi_foto, tanggal_unggah, lokasi_file, user_id
	$judul_foto = isset($_POST['judul_foto']) ? trim($_POST['judul_foto']) : '';
	$deskripsi_foto = isset($_POST['deskripsi_foto']) ? trim($_POST['deskripsi_foto']) : '';

	// Validasi
	if(empty($judul_foto)) {
		$errors[] = 'Judul foto tidak boleh kosong';
	}
	if(empty($deskripsi_foto)) {
		$errors[] = 'Deskripsi foto tidak boleh kosong';
	}
	
	//lokasi upload foto
	$lokasi_upload = 'assets/img/upload/';
	$nama_file = $_FILES['file']['name'];
	$lokasi_temp = $_FILES['file']['tmp_name'];
	$lokasi_tujuan = $lokasi_upload . $nama_file;

	if(move_uploaded_file($lokasi_temp, $lokasi_tujuan)) {
		// Jika upload sukses, lanjutkan dengan proses insert ke database
		$tanggal_unggah = date('Y-m-d');
		$user_id = $_SESSION['user_id'];

		$insert_query = "INSERT INTO foto (judul_foto, deskripsi_foto, tanggal_unggah, lokasi_file, user_id) VALUES ('$judul_foto', '$deskripsi_foto', '$tanggal_unggah', '$lokasi_tujuan', '$user_id')";
		$insert_result = mysqli_query($koneksi, $insert_query);

		if($insert_result) {
			// $sukses = true;
			redirect_to('dashboard.php?status=sukses-baru');		
		} else {
			$errors[] = 'Gagal menyimpan data foto';
		}
	} else {
		$errors[] = 'Gagal mengupload foto';
	}
endif;
?>

<?php require_once('template/header.php'); ?>


<div class="d-sm-flex align-items-center justify-content-between mb-4">

	<a href="dashboard.php" class="btn btn-secondary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
		<span class="text">Kembali</span>
	</a>
</div>

<?php if(!empty($errors)): ?>
	<div class="alert alert-info">
		<?php foreach($errors as $error): ?>
			<?php echo $error; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>	

<?php if($sukses): ?>
	<div class="alert alert-success">Data berhasil disimpan</div>
<?php endif; ?>	

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-fw fa-image"></i> Posting Foto</h6>
    </div>
	
	<form action="foto-tambah.php" method="post" enctype="multipart/form-data">
		<div class="card-body">
			<div class="row">
				<div class="form-group col-12">
					<label class="font-weight-bold">Judul Foto</label>
					<input autocomplete="off" type="text" name="judul_foto" required class="form-control"/> 
				</div>
				<div class="form-group col-12">
					<label class="font-weight-bold">Deskripsi Foto</label>
					<textarea name="deskripsi_foto" class="form-control"></textarea>
				</div>
				<div class="form-group col-12">
					<label class="font-weight-bold">Upload Foto</label>
					<input type="file" name="file" class="form-control-file">
				</div>
			</div>
		</div>
		<div class="card-footer text-right">
            <button name="submit" value="submit" type="submit" class="btn btn-info"><i class="fa fa-save"></i> Simpan</button>
            <button type="reset" class="btn btn-info"><i class="fa fa-sync-alt"></i> Reset</button>
        </div>
	</form>
</div>


<?php require_once('template/footer.php'); ?>
