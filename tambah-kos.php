<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
$sesi = get_sesi();
$errors = array();
$sukses = false;

if(isset($_POST['submit'])):	
	//table fasilitas toilet, listrik_air, parkir, lemari, kasur, internet
	//table lingkungan keamanan, kenyamanan, kebersihan
	//table kos pemilik_id, jarak, luas, fasilitas_id, lokasi, lingkungan_id, akses_jalan, daya_tampung
	
	$pemilik_id = $_POST['pemilik_id'];
	$nama_kos = $_POST['nama_kos'];
	$link_gmaps = $_POST['link_gmaps'];
	$jarak_kampus = $_POST['jarak_kampus'];
	$panjang = $_POST['panjang'];
	$lebar = $_POST['lebar'];
	$luas_kos = $panjang . ' x ' . $lebar;
	$toilet = $_POST['toilet'];
	$listrik_air = $_POST['listrik_air'];
	$parkir = $_POST['parkir'];
	$lemari = $_POST['lemari'];
	$kasur = $_POST['kasur'];
	$internet = $_POST['internet'];
	$lokasi = $_POST['lokasi'];
	$keamanan = $_POST['keamanan'];
	$kenyamanan = $_POST['kenyamanan'];
	$kebersihan = $_POST['kebersihan'];
	$akses_jalan = $_POST['akses_jalan'];
	$daya_tampung = $_POST['daya_tampung'];
	$harga = $_POST['harga'];
	
	// Validasi
	if(!$pemilik_id) {
		$errors[] = 'Pemilik tidak boleh kosong';
	}
	if(!$jarak_kampus) {
		$errors[] = 'Jarak kampus tidak boleh kosong';
	}
	if(!$luas_kos) {
		$errors[] = 'Luas kos tidak boleh kosong';
	}
	if(!$toilet) {
		$errors[] = 'Fasilitas toilet tidak boleh kosong';
	}
	if(!$listrik_air) {
		$errors[] = 'Fasilitas listrik/air tidak boleh kosong';
	}
	if(!$parkir) {
		$errors[] = 'Fasilitas parkir tidak boleh kosong';
	}
	if(!$lemari) {
		$errors[] = 'Fasilitas lemari tidak boleh kosong';
	}
	if(!$kasur) {
		$errors[] = 'Fasilitas kasur tidak boleh kosong';
	}
	if(!$internet) {
		$errors[] = 'Fasilitas internet tidak boleh kosong';
	}
	if(!$lokasi) {
		$errors[] = 'Lokasi tidak boleh kosong';
	}
	if(!$keamanan) {
		$errors[] = 'Lingkungan keamanan tidak boleh kosong';
	}
	if(!$kenyamanan) {
		$errors[] = 'Lingkungan kenyamanan tidak boleh kosong';
	}
	if(!$kebersihan) {
		$errors[] = 'Lingkungan kebersihan tidak boleh kosong';
	}
	if(!$akses_jalan) {
		$errors[] = 'Akses jalan tidak boleh kosong';
	}
	if(!$daya_tampung) {
		$errors[] = 'Daya tampung tidak boleh kosong';
	}
	if(!$harga) {
		$errors[] = 'Hitung harga terlebih dahulu';
	}
	if(!$link_gmaps) {
		$errors[] = 'Link Gmaps tidak boleh kosong';
	}
	if(!$nama_kos) {
		$errors[] = 'Nama Kos tidak boleh kosong';
	}
	

	
	if(empty($errors)):
	
		$simpan_fasilitas = mysqli_query($koneksi, "INSERT INTO fasilitas (toilet, listrik_air, parkir, lemari, kasur, internet) VALUES ('$toilet', '$listrik_air', '$parkir', '$lemari', '$kasur', '$internet')");
		if($simpan_fasilitas) {
			$fasilitas_id = mysqli_insert_id($koneksi);
			$simpan_lingkungan = mysqli_query($koneksi, "INSERT INTO lingkungan (keamanan, kenyamanan, kebersihan) VALUES ('$keamanan', '$kenyamanan', '$kebersihan')");
			if($simpan_lingkungan) {
				$lingkungan_id = mysqli_insert_id($koneksi);
				$simpan_kos = mysqli_query($koneksi, "INSERT INTO kos (pemilik_id, jarak, luas, fasilitas_id, lokasi, lingkungan_id, akses_jalan, daya_tampung,link_gmaps, nama_kos) VALUES ('$pemilik_id', '$jarak_kampus', '$luas_kos', '$fasilitas_id', '$lokasi', '$lingkungan_id', '$akses_jalan', '$daya_tampung','$link_gmaps','$nama_kos')");
				$kos_id = mysqli_insert_id($koneksi);
				if($simpan_kos) {
					//cek table hasil where pemilik_id
					$cek_hasil = mysqli_query($koneksi, "SELECT * FROM hasil WHERE pemilik_id = '$pemilik_id' and kos_id = '$kos_id'");
					if(mysqli_num_rows($cek_hasil) > 0) {
						//update
						$update_hasil = mysqli_query($koneksi, "UPDATE hasil SET harga = '$harga' WHERE pemilik_id = '$pemilik_id' and kos_id = '$kos_id'");
						if($update_hasil) {
							$sukses = true;
						}else{
							$errors[] = 'Data hasil gagal diupdate';
						}
					}else{
						//insert
						$simpan_hasil = mysqli_query($koneksi, "INSERT INTO hasil (pemilik_id, harga, kos_id) VALUES ('$pemilik_id', '$harga', '$kos_id')");
						if($simpan_hasil) {
							$sukses = true;
						}else{
							$errors[] = 'Data hasil gagal disimpan '.mysqli_error($koneksi);
						}
					}
					redirect_to('list-kos.php?status=sukses-baru');		
				}else{
					$errors[] = 'Data kos gagal disimpan'.mysqli_error($koneksi);
				}
			}else{
				$errors[] = 'Data lingkungan gagal disimpan'.mysqli_error($koneksi);
			}
		}else{
			$errors[] = 'Data fasilitas gagal disimpan'.mysqli_error($koneksi);
		}
	endif;
endif;
?>

<?php
$page = "Kos";
require_once('template/header.php');
?>


<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-cube"></i> Data KOS</h1>

	<a href="list-kos.php" class="btn btn-secondary btn-icon-split"><span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
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

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-fw fa-plus"></i> Tambah Data Pemilihan Koks</h6>
    </div>
	
	<form action="tambah-kos.php" method="post">
		<div class="card-body">
			<div class="row">
				<div class="form-group col-md-6">
					<?php
					$query = mysqli_query($koneksi,"SELECT * FROM pemilik ORDER BY id DESC");
					?>
					<label class="font-weight-bold">Nama Pemilik</label>
					<select name="pemilik_id" required class="form-control" id="pemilik_id">
						<option value="">Pilih Pemilik</option>
						<?php while($data = mysqli_fetch_array($query)): ?>
							<option value="<?php echo $data['id']; ?>"><?php echo $data['nama']; ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Nama KOS</label>
					<input type="text" name="nama_kos" id="nama_kos" class="form-control" required>
				</div>
				<!-- link gmaps-->
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Link Gmaps</label>
					<input type="text" name="link_gmaps" id="link_gmaps" class="form-control" required placeholder="https://goo.gl/maps/xxx">
				</div>
				<div class="form-group col-md-12">
					<label class="font-weight-bold">Jarak Ke Kampus</label>
					<select name="jarak_kampus" id="jarak_kampus" required class="form-control">
						<option value="">Pilih Jarak Ke Kampus</option>
						<option value="399"><400m</option>
						<option value="799">400-800m</option>
						<option value="1190">800-1200m</option>
						<option value="1599">1200-1600m</option>
						<option value="1601">>1600m</option>
					</select>
				</div>
				<div class="col-12">
				<label class="font-weight-bold">Luas Kos</label>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Panjang</label>
					<input type="number" name="panjang" id="panjang" class="form-control" required>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Lebar</label>
					<input type="number" name="lebar" id="lebar" class="form-control" required>
				</div>
				<div class="clear-fix"></div>
				<div class="col-12">
					<p class="h5 font-weight-bold">Fasilitas Kos</p>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Fasilitas Toilet</label>
					<select name="toilet" id="toilet" required class="form-control">
						<option value="">Pilih Fasilitas Toilet</option>
						<option value="di dalam kamar">Didalam Kamar</option>
						<option value="di luar kamar">Diluar Kamar</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Fasilitas Listrik / Air</label>
					<select name="listrik_air" id="listrik_air" required class="form-control">
						<option value="">Pilih Fasilitas Listrik / Air</option>
						<option value="disediakan">Disediakan</option>
						<option value="tidak disediakan">Tidak Disediakan</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Fasilitas Parkir</label>
					<select name="parkir" id="parkir" required class="form-control">
						<option value="">Pilih Fasilitas Parkir</option>
						<option value="luas">Luas</option>
						<option value="pas-pasan">Pas-pasan</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Fasilitas Lemari</label>
					<select name="lemari" id="lemari" required class="form-control">
						<option value="">Pilih Fasilitas Lemari</option>
						<option value="ada">Ada</option>
						<option value="tidak_ada">Tidak Ada</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Fasilitas Kasur</label>
					<select name="kasur" id="kasur" required class="form-control">
						<option value="">Pilih Fasilitas Kasur</option>
						<option value="ada">Ada</option>
						<option value="tidak_ada">Tidak Ada</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Fasilitas Internet</label>
					<select name="internet" id="internet" required class="form-control">
						<option value="">Pilih Fasilitas Internet</option>
						<option value="ada">Ada</option>
						<option value="tidak_ada">Tidak Ada</option>
					</select>
				</div>
				<div class="form-group col-md-12">
					<label class="font-weight-bold">Lokasi</label>
					<select name="lokasi" id="lokasi" required class="form-control">
						<option value="">Pilih Lokasi</option>
						<option value="sangat strategis">Sangat Strategis</option>
						<option value="strategis">Strategis</option>
						<option value="cukup">Cukup</option>
						<option value="tidak strategis">Tidak Strategis</option>
					</select>
				</div>
				<div class="clear-fix"></div>
				<div class="col-12">
					<p class="h5 font-weight-bold">Lingkungan</p>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Lingkungan Keamanan</label>
					<select name="keamanan" id="keamanan" required class="form-control">
						<option value="">Pilih Lingkungan Keamanan</option>
						<option value="sangat aman">Sangat Aman</option>
						<option value="aman">Aman</option>
						<option value="cukup">Cukup Aman</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Lingkungan Kenyamanan</label>
					<select name="kenyamanan" id="kenyamanan" required class="form-control">
						<option value="">Pilih Lingkungan Kenyamanan</option>
						<option value="sangat nyaman">Sangat Nyaman</option>
						<option value="nyaman">Nyaman</option>
						<option value="cukup">Cukup Nyaman</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Lingkungan Kebersihan</label>
					<select name="kebersihan" id="kebersihan" required class="form-control">
						<option value="">Pilih Lingkungan Kebersihan</option>
						<option value="sangat bersih">Sangat Bersih</option>
						<option value="bersih">Bersih</option>
						<option value="cukup">Cukup Bersih</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Akses Jalan</label>
					<select name="akses_jalan" id="akses_jalan" required class="form-control">
						<option value="">Pilih Akses Jalan</option>
						<option value="aspal">Aspal</option>
						<option value="tidak aspal">Tidak Aspal</option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label class="font-weight-bold">Lebar Jalan</label>
					<select name="lebar_jalan" id="lebar_jalan" required class="form-control">
						<option value="">Pilih Lebar Jalan</option>
						<option value="6m">6m</option>
						<option value="5m">5m</option>
						<option value="4m">4m</option>
					</select>
				</div>
				<div class="form-group col-md-12">
					<label class="font-weight-bold">Daya Tampung</label>
					<select name="daya_tampung" id="daya_tampung" required class="form-control">
						<option value="">Pilih Daya Tampung</option>
						<option value="luas">Luas</option>
						<option value="standar">Standar</option>
					</select>
				</div>
				<div class="form-group col-md-12">
					<input type="hidden" name="harga" id="harga" value="" required>
				</div>
				<div class="form-group col-md-12">
					<div class="alert alert-info d-none" id="hasil">
						<p class="font-weight-bold">Detail Perhitungan :</p>
						<p>1. Jarak Kampus : <span id="jarak_kampus_text"></span></p>
						<p>2. Luas Kos : <span id="luas_kos_text"></span></p>
						<p class="font-weight-bold">3. Fasilitas Kos : <span id="fasilitas_kos_text"></span></p>
						<div class="pl-2">
							<p>1) Fasilitas Toilet : <span id="toilet_text"></span></p>
							<p>2) Fasilitas Listrik / Air : <span id="listrik_air_text"></span></p>
							<p>3) Fasilitas Parkir : <span id="parkir_text"></span></p>
							<p>4) Fasilitas Lemari : <span id="lemari_text"></span></p>
							<p>5) Fasilitas Kasur : <span id="kasur_text"></span></p>
							<p>6) Fasilitas Internet : <span id="internet_text"></span></p>
						</div>
						<p>4. Lokasi : <span id="lokasi_text"></span></p>
						<p class="font-weight-bold">5. Lingkungan : <span id="lingkungan_text"></span></p>
						<div class="pl-2">
							<p>1) Keamanan : <span id="keamanan_text"></span></p>
							<p>2) Kenyamanan : <span id="kenyamanan_text"></span></p>
							<p>3) Kebersihan : <span id="kebersihan_text"></span></p>
						</div>
						<p>6. Akses Jalan : <span id="akses_jalan_text"></span></p>
						<p>7. Daya Tampung : <span id="daya_tampung_text"></span></p>
						<p class="font-weight-bold">Harga : <span id="harga_text"></span></p>


					</div>
				</div>
			</div>
		</div>
		<div class="card-footer text-right">
			<a href="#" class="btn btn-danger" id="hitungButton"><i class="fa fa-calculator"></i> Hitung Harga</a>
			<a href="#" class="btn btn-secondary" data-toggle="modal" data-target="#rulesModal"><i class="fa fa-eye"></i> Tampilkan Rule</a>
            <button name="submit" value="submit" type="submit" class="btn btn-info"><i class="fa fa-save"></i> Simpan</button>
            <button type="reset" class="btn btn-info"><i class="fa fa-sync-alt"></i> Reset</button>
        </div>
	</form>
	<!-- Modal -->
<div class="modal fade" id="rulesModal" tabindex="-1" role="dialog" aria-labelledby="rulesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rulesModalLabel">Rule</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Hasil aturan akan ditampilkan di sini -->
        <div id="rulesOutput" class="p-3"></div>
      </div>
    </div>
  </div>
</div>

</div>


<?php
require_once('template/footer.php');
?>
<script src="assets/js/ts3.js"></script>