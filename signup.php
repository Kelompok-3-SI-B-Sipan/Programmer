<?php
session_start();
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SIPAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <link rel="stylesheet" href="css/daftar.css">

    
</head>
<body>

<div class="signup-card">
    <h2 class="signup-title"><i class="fas fa-user-plus"></i> Buat Akun</h2>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="nama_pengguna" placeholder="Nama lengkap Anda" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat lengkap..." required></textarea>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select class="form-select" name="jenis_kelamin" required>
                    <option value="" disabled selected>Pilih...</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" class="form-control" name="tlp" placeholder="08..." required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Buat username unik" required>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="******" required>
        </div>
        
        <div class="d-grid gap-2">
            <button type="submit" name="signup" class="btn signup-btn"><i class="fas fa-paper-plane me-2"></i> Daftar Sekarang</button>
        </div>
    </form>

    <div class="text-center mt-4 login-link">
        <p class="mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</div>

<?php
if (isset($_POST['signup'])) {

    // Pastikan semua field ada
    $nama_pengguna = $_POST['nama_pengguna'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $tlp = $_POST['tlp'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validasi sederhana
    if(empty($nama_pengguna) || empty($alamat) || empty($jenis_kelamin) || empty($tlp) || empty($username) || empty($password)) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({icon: 'error', title: 'Gagal', text: 'Semua field harus diisi.', background: '#111', color: '#fff', confirmButtonColor: '#ff0000'});
        });
        </script>";
        exit;
    }

    // Cek username sudah ada
    $stmt_check = $conn->prepare("SELECT username FROM pengguna WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({icon: 'error', title: 'Gagal', text: 'Username sudah digunakan, silakan pilih yang lain.', background: '#111', color: '#fff', confirmButtonColor: '#ff0000'});
        });
        </script>";
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO pengguna (nama_pengguna, alamat, jenis_kelamin, tlp, username, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("ssssss", $nama_pengguna, $alamat, $jenis_kelamin, $tlp, $username, $password);

        if ($stmt_insert->execute()) {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({icon: 'success', title: 'Berhasil!', text: 'Akun berhasil dibuat, mengalihkan ke login...', background: '#111', color: '#fff', showConfirmButton: false, timer: 2000}).then(() => { window.location.href='login.php'; });
            });
            </script>";
        } else {
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({icon: 'error', title: 'Kesalahan!', text: 'Gagal membuat akun. Silakan coba lagi.', background: '#111', color: '#fff', confirmButtonColor: '#ff0000'});
            });
            </script>";
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
