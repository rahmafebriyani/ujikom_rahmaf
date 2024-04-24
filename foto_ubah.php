<?php require_once('includes/init.php'); ?>
<?php cek_login(); ?>

<?php
$sesi = get_sesi();
$errors = array();
$sukses = false;

// Memastikan parameter foto_id telah dikirimkan
if(!isset($_GET['foto_id'])) {
    redirect_to("dashboard.php");
}

// Memeriksa apakah foto dengan id yang diberikan ada dalam database
$query = mysqli_query($koneksi, "SELECT * FROM foto WHERE foto_id = '{$_GET['foto_id']}'");
if(mysqli_num_rows($query) == 0) {
    redirect_to("dashboard.php");
}

// Mendapatkan data foto
$data_foto = mysqli_fetch_assoc($query);

if(isset($_POST['submit'])) {
    // Mengambil data yang dikirimkan melalui formulir
    $judul_foto = isset($_POST['judul_foto']) ? trim($_POST['judul_foto']) : '';
    $deskripsi_foto = isset($_POST['deskripsi_foto']) ? trim($_POST['deskripsi_foto']) : '';

    // Validasi
    if(empty($judul_foto)) {
        $errors[] = 'Judul foto tidak boleh kosong';
    }
    if(empty($deskripsi_foto)) {
        $errors[] = 'Deskripsi foto tidak boleh kosong';
    }

    // Upload foto baru jika ada
    if(!empty($_FILES['file']['name'])) {
        $lokasi_upload = 'assets/img/upload/';
        $nama_file = $_FILES['file']['name'];
        $lokasi_temp = $_FILES['file']['tmp_name'];
        $lokasi_tujuan = $lokasi_upload . $nama_file;

        if(move_uploaded_file($lokasi_temp, $lokasi_tujuan)) {
            // Jika upload sukses, lanjutkan dengan proses update data di database
            $lokasi_tujuan = mysqli_real_escape_string($koneksi, $lokasi_tujuan);
            $tanggal_unggah = date('Y-m-d');
            $user_id = $_SESSION['user_id'];

            $update_query = "UPDATE foto SET judul_foto = '$judul_foto', deskripsi_foto = '$deskripsi_foto', lokasi_file = '$lokasi_tujuan' WHERE foto_id = '$_GET[foto_id]'";
            $update_result = mysqli_query($koneksi, $update_query);

            if($update_result) {
                redirect_to('dashboard.php?status=sukses-edit');	
            } else {
                $errors[] = 'Gagal menyimpan data foto';
            }
        } else {
            $errors[] = 'Gagal mengupload foto';
        }
    } else {
        // Jika tidak ada foto yang diunggah, lakukan update data kecuali untuk lokasi file
        // $tanggal_unggah = date('Y-m-d');
        $user_id = $_SESSION['user_id'];

        $update_query = "UPDATE foto SET judul_foto = '$judul_foto', deskripsi_foto = '$deskripsi_foto' WHERE foto_id = '$_GET[foto_id]'";
        $update_result = mysqli_query($koneksi, $update_query);

        if(!$update_result) {
            $errors[] = 'Gagal menyimpan data foto';
        }
    }
}

require_once('template/header.php');
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <a href="dashboard.php" class="btn btn-secondary btn-icon-split">
        <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
        <span class="text">Kembali</span>
    </a>
</div>

<?php if(!empty($errors)): ?>
    <div class="alert alert-info">
        <?php foreach($errors as $error): ?>
            <?= $error; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?> 

<?php if($sukses): ?>
    <div class="alert alert-success">Data berhasil disimpan</div>
<?php endif; ?> 

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-fw fa-image"></i> Ubah Foto</h6>
    </div>

    <form action="foto_ubah.php?foto_id=<?= $_GET['foto_id']; ?>" method="post" enctype="multipart/form-data">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-12">
                    <label class="font-weight-bold">Judul Foto</label>
                    <input autocomplete="off" type="text" name="judul_foto" required class="form-control" value="<?= $data_foto['judul_foto']; ?>"/> 
                </div>
                <div class="form-group col-12">
                    <label class="font-weight-bold">Deskripsi Foto</label>
                    <textarea name="deskripsi_foto" class="form-control"><?= $data_foto['deskripsi_foto']; ?></textarea>
                </div>
                <div class="col-12">
                    <label class="col-12">Foto Sekarang</label>
                    <img class="" src="<?=$data_foto['lokasi_file'];?>" alt="Card image cap" height="360px" width="360px">
                </div>
                <div class="form-group col-12">
                    <label class="font-weight-bold">Upload Foto Baru <small>Jika ingin mengubah</small></label>
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
