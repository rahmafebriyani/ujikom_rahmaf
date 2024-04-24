<?php
require_once('includes/init.php');

$user_role = get_role();
$sesi = get_sesi();

if($_SESSION['username']==""){
    redirect_to("login.php");
} 

$page = "Dashboard";
require_once('template/header.php');

$errors = array();
$sukses = array();

if(isset($_GET['foto_id'])){
    $query = mysqli_query($koneksi,"SELECT f.*, u.* from foto f inner join user u on f.user_id=u.user_id where f.foto_id='$_GET[foto_id]'");
    if(mysqli_num_rows($query)<0){
        redirect_to("dashboard.php");
    }
    $data = mysqli_fetch_assoc($query);
}else{
    redirect_to("dashboard.php");
}

if(isset($_POST['submit'])):	
    $isi_komentar = $_POST['isi_komentar']; // Ambil isi komentar dari formulir

    // Validasi
    if(empty($isi_komentar)) {
        $errors[] = 'Isi komentar tidak boleh kosong';
    }

    if(empty($errors)) {
        // Simpan komentar ke database
        $foto_id = $_GET['foto_id'];
        $user_id = $_SESSION['user_id'];
        $tanggal_komentar = date('Y-m-d'); // Ambil tanggal saat ini

        $query_insert_komentar = mysqli_query($koneksi, "INSERT INTO komentar_foto (foto_id, user_id, isi_komentar, tanggal_komentar) VALUES ('$foto_id', '$user_id', '$isi_komentar', '$tanggal_komentar')");
        if($query_insert_komentar) {
            $sukses[] = 'Komentar berhasil dikirim';
        } else {
            $errors[] = 'Gagal menyimpan komentar';
        }
    }
endif;

?>

<div class="mb-4">
    <?php
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $msg = '';
    switch($status):
        case 'sukses-baru':
            $msg = 'Komentar berhasil dikirim';
            break;
        case 'sukses-hapus':
            $msg = 'Data behasil dihapus';
            break;
        case 'sukses-edit':
            $msg = 'Data behasil diupdate';
            break;
    endswitch;

    if($msg):
        echo '<div class="alert alert-success">'.$msg.'</div>';
    endif;
    ?>
    <div class="row">
        <?php
            $query_like = mysqli_query($koneksi, "SELECT COUNT(*) AS total_like FROM like_foto WHERE foto_id = '{$data['foto_id']}'");
            $total_like = mysqli_fetch_assoc($query_like)['total_like'];
            // Mengambil jumlah komentar dari database
            $query_komentar = mysqli_query($koneksi, "SELECT COUNT(*) AS total_komentar FROM komentar_foto WHERE foto_id = '{$data['foto_id']}'");
            $total_komentar = mysqli_fetch_assoc($query_komentar)['total_komentar'];
            $ual= mysqli_query($koneksi, "SELECT * from like_foto where user_id = '$_SESSION[user_id]' and foto_id='$data[foto_id]'");
            $ual_c = mysqli_num_rows($ual);
            if($ual_c>0){
                $user_already_liked=true;
            }else{
                $user_already_liked=false;
            }
                
        ?>

        <div class="col-12 col-lg-4 col-xl-4 col-md-4">
            <!-- ini fixed, tidak bisa di scroll-->
            <div class="card" style="width: 18rem;">
                <img class="card-img-top" src="<?=$data['lokasi_file'];?>" alt="Card image cap" height="360px">
                <div class="card-body">
                    <p class="card-text p-0" style="line-height: 0.4;"><a href="profile-list.php?user_id=<?=$data['user_id'];?>"><?= $data['nama_lengkap']; ?></a></p>
                    <p class="card-text p-0" style="line-height: 0.4;"><?=$data['judul_foto'];?></p>
                    <p class="p-0 card-text" style="line-height: 0.4;"><small><?=$data['deskripsi_foto'];?></small></p>
                    <p class="card-text"style="line-height: 0.8;"><small class="text-muted">Diposting pada <?= convertTanggal($data['tanggal_unggah']); ?></small></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="#" class="btn-like" data-foto-id="<?=$data['foto_id'];?>" style="color: <?=($user_already_liked ? '#36b9cc' : '#858796');?>">
                                <i class="fa fa-thumbs-up mr-1" ></i> Like
                            </a>
                            <span class="ml-2"><span class="like-count"><?=$total_like;?></span> likes</span>
                        </div>
                        <div>
                            Komentar (<?=$total_komentar;?>)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8 col-xl-8 col-md-8">
            <!-- row loop komentar (bisa di scroll) -->
            <?php
            $query_komentar = mysqli_query($koneksi, "SELECT k.*, u.nama_lengkap 
            FROM komentar_foto k 
            INNER JOIN user u ON k.user_id = u.user_id 
            WHERE k.foto_id = '{$data['foto_id']}' 
            ORDER BY k.tanggal_komentar DESC");
            while($komentar = mysqli_fetch_assoc($query_komentar)):
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <p class="card-text" style="line-height: 0.4;"><?= $komentar['isi_komentar']; ?></p>
                    <p class="card-text"style="line-height: 0.4;"><small class="text-muted">Dikirim oleh <?= $komentar['nama_lengkap']; ?> pada <?= convertTanggal($komentar['tanggal_komentar']); ?></small></p>
                </div>
            </div>
            <?php endwhile; ?>
            <!-- ada form untuk kirim komentar actionnya ke foto.php -->
            <form action="foto.php?foto_id=<?= $data['foto_id']; ?>" method="post">
                <div class="form-group">
                    <textarea class="form-control" name="isi_komentar" placeholder="Tulis komentar Anda" rows="3"></textarea>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Kirim Komentar</button>
            </form>
        </div>

    </div>
</div>

<?php
require_once('template/footer.php');
?>
<script>
    $(document).ready(function() {
        $('.btn-like').on('click', function(e) {
            e.preventDefault();
            var button = $(this); // Simpan referensi ke tombol like
            var fotoId = button.data('foto-id');
            var likeCount = button.closest('.card-body').find('.like-count'); // Cari elemen yang menampilkan jumlah like

            $.ajax({
                type: 'POST',
                url: 'like.php',
                data: { foto_id: fotoId },
                success: function(response) {
                    if (response.status == 'liked') {
                        button.css('color', '#36b9cc');
                        likeCount.text(parseInt(likeCount.text()) + 1);
                    } else if (response.status == 'unliked') {
                        button.css('color', '#858796');
                        likeCount.text(parseInt(likeCount.text()) - 1);
                    }
                }
            });
        });
    });
</script>


