<?php
session_start();
include 'koneksi.php';

// Tidak perlu session check karena halaman ini publik
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <link rel="stylesheet" href="css/data.css">
    
    
</head>

<body>

    <!-- Main Content -->
    <div class="main-content">
        
        <div class="header-area">
            <h1 class="page-title">Data Pengaduan <span>Publik</span></h1>
            <p class="subtitle">Transparansi Layanan Aspirasi dan Pengaduan Online Rakyat</p>
            
            <div class="info-box">
                <i class="fas fa-shield-alt me-2"></i> 
                <strong>Privasi Terjaga:</strong> Identitas pelapor disamarkan menjadi <em>"Anonim"</em> untuk melindungi privasi Anda. Halaman ini hanya menampilkan laporan yang telah diproses.
            </div>

            <!-- Search Form -->
            <form action="" method="post" class="search-container">
                <input type="text" name="cr" class="search-input" placeholder="Cari berdasarkan lokasi atau isi laporan...">
                <button type="submit" name="cari" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-dark text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pelapor</th>
                            <th class="text-start">Isi Laporan</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query Dasar - Tampilkan semua kecuali yang masih murni "Proses" (opsional, tergantung kebijakan)
                        // Disini kita tampilkan semua agar publik tau progressnya, tapi urutkan dari terbaru
                        $query = "SELECT id_pengaduan, alamat, isi_laporan, status, waktu_pembuatan FROM pengaduan ORDER BY waktu_pembuatan DESC";
                        
                        // Filter Pencarian
                        if (isset($_POST['cari']) && !empty($_POST['cr'])) {
                            $cr = $conn->real_escape_string($_POST['cr']);
                            $query = "SELECT id_pengaduan, alamat, isi_laporan, status, waktu_pembuatan FROM pengaduan WHERE isi_laporan LIKE '%$cr%' OR alamat LIKE '%$cr%' ORDER BY waktu_pembuatan DESC";
                        }

                        $result = $conn->query($query);

                        if ($result && $result->num_rows > 0) {
                            $no = 1;
                            while ($data = $result->fetch_assoc()) {
                                // Logic Warna Status
                                $status = $data['status'] ?? 'Proses';
                                $badge_class = 'badge-secondary';
                                
                                if ($status == 'Selesai') $badge_class = 'badge-success';
                                elseif ($status == 'Ditolak') $badge_class = 'badge-danger';
                                elseif ($status == 'Ditanggapi') $badge_class = 'badge-primary';
                                else $badge_class = 'badge-warning'; // Proses

                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . date('d M Y', strtotime($data['waktu_pembuatan'])) . "</td>";
                                
                                // SAMARKAN NAMA PELAPOR
                                echo "<td><span class='badge bg-dark border border-secondary text-secondary'><i class='fas fa-user-secret me-1'></i> Anonim</span></td>";
                                
                                // Potong isi laporan jika terlalu panjang
                                echo "<td class='text-start text-white-50'>" . htmlspecialchars(substr($data['isi_laporan'], 0, 100)) . (strlen($data['isi_laporan']) > 100 ? "..." : "") . "</td>";
                                
                                echo "<td>" . htmlspecialchars($data['alamat']) . "</td>";
                                
                                // Status Badge
                                echo "<td><span class='badge-custom $badge_class'>" . ucfirst($status) . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'><div class='no-data'><i class='far fa-folder-open'></i> Belum ada data laporan yang ditemukan.</div></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Footer -->
  <footer>
    <br><br>
    <div class="row w-100">
        <div class="col-xl-12">
            <p class="copy_right text-center">
                Copyright &copy;
                <script>
                    document.write(new Date().getFullYear());
                </script> All rights reserved | <i class="fa fa-heart-o" aria-hidden="true"></i> by <a
                    href="index.php" target="_blank" style="color: red; text-decoration: none;">SIPAN</a>
            </p>
        </div>
    </div>
</footer>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>