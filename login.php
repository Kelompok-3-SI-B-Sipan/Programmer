<?php
session_start();
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>SIPAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <Link rel="stylesheet" href="css/login.css">
    
    
</head>
<body>

    <div class="container d-flex justify-content-center align-items-center h-100">
        <div class="login-card">
            <h2 class="login-title"><i class="fas fa-user-circle"></i> Log In</h2>

            <form action="" method="POST">
                
                <div class="mb-3">
                    <label for="role" class="form-label">Pilih Peran</label>
                    <div class="input-group-icon">
                        <i class="fas fa-id-badge"></i>
                        <select class="form-control form-select" id="role" name="role" required>
                            <option value="">-- Pilih Akses --</option>
                            <option value="user">Pengguna</option>
                            <option value="petugas">Petugas</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autocomplete="off">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" name="login" class="btn login-btn w-100">
                        Masuk Sekarang <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="mb-0">Belum punya akun? <a href="signup.php" class="text-primary">Daftar disini</a></p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
    if (isset($_POST['login'])) {
        $role = $_POST['role'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($role == 'user') {
            // Login pengguna (tamu)
            $safe_username = mysqli_real_escape_string($conn, $username);
            $safe_password = mysqli_real_escape_string($conn, $password);
            
            $data_user = mysqli_query($conn, "SELECT * FROM pengguna WHERE username ='$safe_username' AND password ='$safe_password'");
            $r_user = mysqli_fetch_array($data_user);
            
            if ($r_user) {
                $_SESSION['nama_pengguna'] = $r_user['nama_pengguna'];
                $_SESSION['alamat'] = $r_user['alamat'];
                $_SESSION['tlp'] = $r_user['tlp'];
                
                // SweetAlert Sederhana: Login Berhasil
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil!',
                        showConfirmButton: false,
                        timer: 1500,
                        background: '#1a1a1a',
                        color: '#fff',
                        iconColor: '#00ff00'
                    }).then(() => {
                        window.location.href = 'user_dashboard.php';
                    });
                </script>";
            } else {
                // SweetAlert Sederhana: Login Gagal
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: 'Username atau Password salah!',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#ff0000'
                    });
                </script>";
            }
        } elseif ($role == 'petugas') {
            // Login petugas
            $safe_username = mysqli_real_escape_string($conn, $username);
            $safe_password = mysqli_real_escape_string($conn, $password);

            $data_petugas = mysqli_query($conn, "SELECT * FROM petugas WHERE username ='$safe_username' AND password ='$safe_password'");
            $r_petugas = mysqli_fetch_array($data_petugas);
            
            if ($r_petugas) {
                $_SESSION['nama_petugas'] = $r_petugas['nama_petugas'];
                
                // SweetAlert Sederhana: Login Berhasil
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Berhasil!',
                        showConfirmButton: false,
                        timer: 1500,
                        background: '#1a1a1a',
                        color: '#fff',
                        iconColor: '#00ff00'
                    }).then(() => {
                        window.location.href = 'admin_dashboard.php';
                    });
                </script>";
            } else {
                // SweetAlert Sederhana: Login Gagal
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: 'Data petugas tidak ditemukan.',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#ff0000'
                    });
                </script>";
            }
        }
    }
    ?>
</body>
</html>