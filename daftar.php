<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$nama_lengkap = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : '';
$alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';

// echo sha1("user1234");
// die();
if(isset($_POST['submit'])):
	
	// Validasi
	if(empty($username)) {
		$errors[] = 'Username tidak boleh kosong';
	} elseif (!ctype_digit($username) || strlen($username) > 11) {
        $errors[] = 'Username harus berupa angka dan tidak lebih dari 11 digit';
    }
	if(!$password) {
		$errors[] = 'Password tidak boleh kosong';
	}
	
	if(empty($errors)):
		// Query untuk memeriksa apakah username atau email sudah terdaftar
		$query = mysqli_query($koneksi,"SELECT * FROM user WHERE username = '$username' OR email ='$email'");
		$cek = mysqli_num_rows($query);
		
		if($cek > 0){
			$errors[] = 'Username atau email sudah terdaftar!';
		} else {
            $password = sha1($password);
			// Jika username dan email belum terdaftar, maka masukkan data pengguna ke dalam tabel user
			$insert_query = "INSERT INTO user (username, password, email, nama_lengkap, alamat) VALUES ('$username', '$password', '$email', '$nama_lengkap', '$alamat')";
			$insert_result = mysqli_query($koneksi, $insert_query);
			
			if(!$insert_result) {
				$errors[] = 'Gagal menambahkan pengguna. Silakan coba lagi.';
			} else {
				// Berhasil memasukkan data pengguna
				redirect_to('login.php?status=sukses-baru');		
			}
		}
	endif;

endif;

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />

        <title>Website Galeri Foto</title>

        <!-- Custom fonts for this template-->
        <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />

        <!-- Custom styles for this template-->
        <link href="assets/css/sb-admin-2.min.css" rel="stylesheet" />
		<link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
		<link rel="icon" href="assets/img/favicon.ico" type="image/x-icon">
        <style>
            .bg-circle {
            border-radius: 50%; /* Membuat latar belakang menjadi lingkaran */
            justify-content: center;
            align-items: center;
            padding:20px;
            }
        </style>
    </head>

    <body class="bg-gradient-info">
		<nav class="navbar navbar-expand-lg navbar-dark bg-white shadow-lg pb-3 pt-3 font-weight-bold">
            <div class="container">
                <a class="navbar-brand text-info" style="font-weight: 800;" href="index.php">Website Galeri Foto</a>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link text-info" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-info" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <!-- Outer Row -->
            <div class="row d-plex justify-content-center mt-5">				
				<div class="col-xl-8 col-lg-8 col-md-8 mt-5">
                    <div class="card o-hidden border-0 shadow-lg my-5 p-10">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-5">
                                        <div class="text-center">
                                            
                                            <h1 class="h5 text-left text-gray-900 mb-4 mt-3">Buat Akun</h1>
                                        </div>
										<?php if(!empty($errors)): ?>
											<?php foreach($errors as $error): ?>
												<div class="alert alert-danger text-center"><?php echo $error; ?></div>
											<?php endforeach; ?>
										<?php endif; ?>	

                                        <form class="user" action="daftar.php" method="post">
                                            <div class="form-group">
                                                <input required autocomplete="off" type="text" value="<?php echo htmlentities($username); ?>" class="form-control form-control-user" id="exampleInputUser" placeholder="Username" name="username" />
                                            </div>
                                            <div class="form-group">
                                                <input required autocomplete="off" type="text" class="form-control form-control-user" id="email" name="email" placeholder="Email" />
                                            </div>
                                            <div class="form-group">
                                                <input required autocomplete="off" type="text" class="form-control form-control-user" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" />
                                            </div>
                                            <div class="form-group">
                                                <input required autocomplete="off" type="password" class="form-control form-control-user" id="exampleInputPassword" name="password" placeholder="Password" />
                                            </div>
                                            <div class="form-group">
                                                <textarea required autocomplete="off" type="text" class="form-control form-control-user" id="alamat" name="alamat" placeholder="Alamat"></textarea>
                                            </div>
                                            <button name="submit" type="submit" class="btn btn-info btn-user btn-block"><i class="fas fa-fw fa-sign-in-alt mr-1"></i> Daftar</button>
                                            <a href="login.php" class="btn btn-danger btn-user btn-block"><i class="fas fa-fw fa-sign-up-alt mr-1"></i> Masuk</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="assets/vendor/jquery/jquery.min.js"></script>
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="assets/js/sb-admin-2.min.js"></script>
    </body>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p class="text-center text-white">&copy; <?= date('Y') ?> Website Galeri Foto. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

</html>
