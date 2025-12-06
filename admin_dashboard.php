<?php
session_start();
include 'koneksi.php'; // koneksi menggunakan $conn

// Cek login
if (!isset($_SESSION['nama_petugas'])) {
    header('Location: login.php');
    exit();
}

$nama_petugas = $_SESSION['nama_petugas'];

// Ambil nama petugas
$nama_pengguna = "Admin"; // default
$stmt = mysqli_prepare($conn, "SELECT nama_petugas FROM petugas WHERE nama_petugas = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $nama_petugas);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nama_pengguna_db);
    if (mysqli_stmt_fetch($stmt)) {
        $nama_pengguna = $nama_pengguna_db;
    }
    mysqli_stmt_close($stmt);
}

// Statistik pengaduan
$query_today = mysqli_query($conn, "SELECT COUNT(*) AS total_pengaduan_today FROM pengaduan WHERE DATE(waktu_pembuatan) = CURDATE()");
$result_today = mysqli_fetch_assoc($query_today);
$total_pengaduan_today = $result_today['total_pengaduan_today'] ?? 0;

$query_total = mysqli_query($conn, "SELECT COUNT(*) AS total_pengaduan FROM pengaduan");
$result_total = mysqli_fetch_assoc($query_total);
$total_pengaduan = $result_total['total_pengaduan'] ?? 0;

$query_respon = mysqli_query($conn, "SELECT COUNT(*) AS total_respon FROM pengaduan WHERE status = 'Ditanggapi' OR status = 'Selesai'");
$result_respon = mysqli_fetch_assoc($query_respon);
$total_respon = $result_respon['total_respon'] ?? 0;

$query_pending = mysqli_query($conn, "SELECT COUNT(*) AS total_pending FROM pengaduan WHERE status = 'Pending' OR status = 'Proses'");
$result_pending = mysqli_fetch_assoc($query_pending);
$total_pending = $result_pending['total_pending'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPAN</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/admin.css">
    
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">

    
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="profile-section">
            <div class="profile-img">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="profile-name"><?php echo htmlspecialchars($nama_pengguna); ?></div>
            <div class="profile-role">Administrator</div>
        </div>

        <button class="nav-btn active" onclick="location.reload()">
            <i class="fas fa-home"></i> <span class="nav-text">Dashboard</span>
        </button>
        
        <button class="nav-btn" onclick="showDashboard()">
            <i class="fas fa-clipboard-list"></i> <span class="nav-text">Daftar Laporan</span>
        </button>
    

        <div style="margin-top: auto;">
            <button class="nav-btn logout-btn" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i> <span class="nav-text">Logout</span>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Welcome Banner -->
        <div class="welcome-box d-flex align-items-center justify-content-between flex-wrap">
            <div class="welcome-text">
                <h1>Selamat Datang, <span><?php echo htmlspecialchars($nama_pengguna); ?></span>!</h1>
                <p class="text-secondary mb-0">Anda login sebagai Administrator. Berikut adalah ringkasan hari ini.</p>
            </div>
            <img src="img/dengar_suaraku.png" alt="Logo" class="welcome-img d-none d-md-block">
        </div>

        <!-- Stats Grid -->
        <div class="row g-4">
            <!-- Card 1: Hari Ini -->
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <i class="fas fa-calendar-day stat-icon"></i>
                    <div class="stat-number"><?php echo $total_pengaduan_today; ?></div>
                    <div class="stat-label">Laporan Hari Ini</div>
                </div>
            </div>

            <!-- Card 2: Total Laporan -->
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <i class="fas fa-folder-open stat-icon"></i>
                    <div class="stat-number"><?php echo $total_pengaduan; ?></div>
                    <div class="stat-label">Total Laporan</div>
                </div>
            </div>

            <!-- Card 3: Pending/Proses -->
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <i class="fas fa-hourglass-half stat-icon"></i>
                    <div class="stat-number text-warning"><?php echo $total_pending; ?></div>
                    <div class="stat-label">Perlu Diproses</div>
                </div>
            </div>

            <!-- Card 4: Selesai/Ditanggapi -->
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <i class="fas fa-check-circle stat-icon"></i>
                    <div class="stat-number" style="color: #28a745;"><?php echo $total_respon; ?></div>
                    <div class="stat-label">Selesai / Ditanggapi</div>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript Logic -->
    <script>
        function showDashboard() {
            window.location.href = 'daftar_laporan_admin.php'; 
        }

        function logout() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar dari sesi ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                cancelButtonColor: '#333',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                background: '#111',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Logout Berhasil!',
                        text: 'Mengalihkan ke halaman login...',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        background: '#111',
                        color: '#fff'
                    }).then(() => {
                        window.location.href = 'index.php'; // Atau logout.php jika ada file khusus logout
                    });
                }
            })
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>