<?php
session_start();

// Cek apakah sesi 'nama_pengguna' sudah ada
if (isset($_SESSION['nama_pengguna'])) {
    $username = $_SESSION['nama_pengguna'];
} else {
    // Jika belum ada sesi 'nama_pengguna', arahkan ke halaman login
    header('Location: login.php');
    exit();
}

// Sertakan file koneksi
require_once 'koneksi.php'; // Pastikan path sudah benar sesuai struktur folder Anda

// Inisialisasi variabel
$status = 0; // Total diterima
$di_tolak = 0; // Total ditolak
$total_pengaduan = 0; // Total semua pengaduan

// Query untuk menghitung total pengaduan berdasarkan username yang login
$sql = "SELECT 
            SUM(status = 'Selesai') AS diterima, 
            SUM(status = 'Ditolak') AS ditolak, 
            COUNT(*) AS total_pengaduan 
        FROM pengaduan 
        WHERE nama_pengguna = ?"; // Gunakan tabel dan kolom baru

// Persiapkan statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username); // Ikat parameter 'username'
$stmt->execute();
$result = $stmt->get_result();

// Ambil hasilnya
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['diterima'] ?? 0;
    $di_tolak = $row['ditolak'] ?? 0;
    $total_pengaduan = $row['total_pengaduan'] ?? 0;
} else {
    $status = 0;
    $di_tolak = 0;
    $total_pengaduan = 0;
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <link rel="stylesheet" href="css/pengguna.css">

</head>
<body>

    <!-- Overlay untuk Mobile -->
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-bullhorn"></i> SUARAKU
        </div>

        <div class="profile-section">
            <div class="profile-img">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo htmlspecialchars($username); ?></div>
            <div class="profile-role">Pengguna</div>
        </div>

        <div class="nav-menu">
            <button class="nav-btn active" onclick="location.reload()">
                <i class="fas fa-th-large"></i> Dashboard
            </button>
            <button class="nav-btn" onclick="showReport()">
                <i class="fas fa-pen-nib"></i> Buat Laporan
            </button>
            <button class="nav-btn" onclick="showDashboard()">
                <i class="fas fa-history"></i> Riwayat Laporan
            </button>
        </div>

        <button class="nav-btn logout" onclick="confirmLogout()">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        
        <!-- Top Bar (Mobile Toggle + Date) -->
        <div class="top-bar">
            <button class="hamburger-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="date-display">
                <i class="far fa-clock"></i> <span id="current-time">Memuat waktu...</span>
            </div>
        </div>

        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Halo, <?php echo htmlspecialchars($username); ?>! 👋</h1>
                <p>Selamat datang di Aplikasi <b>SUARAKU</b> - Sistem Aspirasi Anonim.</p>
                <p class="mt-2 text-muted" style="font-size: 0.8rem;">Sampaikan suara Anda, aspirasi, serta pantau status laporan Anda di sini.</p>
            </div>
            <img src="img/sekolah.png" alt="School Logo" class="welcome-img">
        </div>

        <!-- Stats Grid -->
        <div class="row g-4">
            <!-- Card 1: Diterima -->
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-success">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3><?php echo $status; ?></h3>
                            <p>Laporan Diterima</p>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Ditolak -->
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-danger">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3><?php echo $di_tolak; ?></h3>
                            <p>Laporan Ditolak</p>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Total -->
            <div class="col-lg-4 col-md-6">
                <div class="stat-card card-primary">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3><?php echo $total_pengaduan; ?></h3>
                            <p>Total Laporan</p>
                        </div>
                        <div class="icon-box">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions (Fitur Baru Profesional) -->
        <h4 class="section-title">Aksi Cepat</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <a href="pengaduan.php" class="quick-action-card">
                    <div class="quick-icon"><i class="fas fa-plus"></i></div>
                    <div>
                        <h6 class="mb-0 text-white">Buat Laporan Baru</h6>
                        <small class="text-muted">Isi formulir pengaduan masalah</small>
                    </div>
                    <i class="fas fa-arrow-right ms-auto text-muted"></i>
                </a>
            </div>
            <div class="col-md-6">
                <a href="daftar_laporan_pengguna.php" class="quick-action-card">
                    <div class="quick-icon"><i class="fas fa-search"></i></div>
                    <div>
                        <h6 class="mb-0 text-white">Cek Status Laporan</h6>
                        <small class="text-muted">Lihat progres laporan Anda</small>
                    </div>
                    <i class="fas fa-arrow-right ms-auto text-muted"></i>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <footer class="dashboard-footer">
            <p>&copy; <?php echo date('Y'); ?> <b>SIPAN</b> - MAN 1 Kota Gorontalo. All Rights Reserved.</p>
        </footer>

    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // TOGGLE SIDEBAR MOBILE
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // REAL-TIME CLOCK
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('current-time').innerText = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock(); // Run immediately

        // ALERT SELAMAT DATANG (Otomatis muncul saat masuk)
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Selamat Datang Kembali!',
                text: 'Halo, <?php echo htmlspecialchars($username); ?>',
                icon: 'success',
                background: '#1a1a1a', 
                color: '#fff',
                iconColor: '#ff0000', 
                showConfirmButton: false, 
                timer: 2500, 
                timerProgressBar: true,
                position: 'center',
                backdrop: `rgba(0,0,0,0.7)`
            });
        });

        function showDashboard() { 
            window.location.href = 'daftar_laporan_pengguna.php'; 
        }
        
        function showReport() { 
            window.location.href = 'pengaduan.php'; 
        }
        
        function confirmLogout() {
            Swal.fire({
                title: 'Yakin ingin keluar?',
                text: "Sesi Anda akan diakhiri.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                cancelButtonColor: '#333',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                background: '#111',
                color: '#fff',
                iconColor: '#ff0000'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
                }
            })
        }
    </script>
</body>
</html>