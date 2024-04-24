<?php
require_once('includes/init.php');

$user_role = get_role();
$sesi = get_sesi();

if($_SESSION['username']==""){
    redirect_to("login.php");
} 

if(isset($_GET['user_id'])){
    $user = mysqli_query($koneksi,"SELECT * from user where user_id = '$_GET[user_id]'");
    $user = mysqli_fetch_assoc($user);
}else{
    redirect_to("dashboard.php");
}

$page = "Dashboard";
require_once('template/header.php');

$errors = array();
$sukses = false;

?>

<div class="mb-4">
    <?php
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $msg = '';
    switch($status):
        case 'sukses-baru':
            $msg = 'Postingan berhasil dibuat';
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
    <div class="col-12 p-3">
        <?php 
        if($_GET['user_id']==$_SESSION['user_id']){
            ?>
            <a href="foto-tambah.php" class="btn btn-info"> <i class="fa fa-plus"></i> Posting Foto Baru </a>
        <?php
        }
        ?>
    </div>
    <?php if(!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach($errors as $error): ?>
                <?php echo $error; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>		
    <?php if(!empty($sukses)): ?>
        <div class="alert alert-info">
            <?php foreach($sukses as $sukses): ?>
                <?php echo $sukses; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>	
    <div class="row">
        <div class="col-12">
            <hr>
            <p class="h5 text-center">Galeri foto dari <?=$user['nama_lengkap'];?></p>
            <hr>
        </div>
        <?php
            $no = 1;
            $query = mysqli_query($koneksi,"SELECT f.*, u.* from foto f inner join user u on f.user_id=u.user_id where f.user_id='$_GET[user_id]' ORDER BY f.foto_id DESC");
            if(mysqli_num_rows($query)>0){
                while($data = mysqli_fetch_array($query)):
                    // Mengambil jumlah like dari database
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
                                <a href="foto.php?foto_id=<?=$data['foto_id'];?>" class="btn btn-primary">Komentar (<?=$total_komentar;?>)</a>
                            </div>
                        </div>
                        <?php
                        if($data['user_id']==$_SESSION['user_id']){
                            ?>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="foto_ubah.php?foto_id=<?= $data['foto_id']; ?>" class="btn btn-primary mb-2"><i class="fa fa-edit"></i></a>
                                <a href="foto_hapus.php?foto_id=<?= $data['foto_id']; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                            </div>
    
    
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php
            }else{
                ?>
                <div class="col-12 text-center p-4 mt-3">
                    <p class="h2">Belum ada foto</p>
                </div>
                <?php
            }
            ?>
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


