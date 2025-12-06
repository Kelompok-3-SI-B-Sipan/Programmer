<?php
session_start();
include 'koneksi.php';

// Inisialisasi script SweetAlert agar tidak undefined
$swal_script = "";

// Update status laporan
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_pengaduan = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'selesai') {
        $status = 'Selesai';
    } elseif ($action === 'ditolak') {
        $status = 'Ditolak';
    } else {
        header("Location: daftar_laporan_admin.php");
        exit;
    }

    $stmt = $conn->prepare("UPDATE pengaduan SET status = ? WHERE id_pengaduan = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $id_pengaduan);

        if ($stmt->execute()) {
            // Redirect untuk refresh bersih
            header("Location: daftar_laporan_admin.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } 
}

// Hapus laporan
if (isset($_GET['idd'])) {
    $id_pengaduan = intval($_GET['idd']);

    // Pastikan nama kolom ID sesuai database (disini saya pakai id_pengaduan agar konsisten)
    $stmt = $conn->prepare("DELETE FROM pengaduan WHERE id_pengaduan = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_pengaduan);

        if ($stmt->execute()) {
            $swal_script = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data laporan berhasil dihapus.',
                        background: '#111', color: '#fff', confirmButtonColor: '#ff0000'
                    }).then(() => { window.location='daftar_laporan_admin.php'; });
                });
            </script>";
        } else {
            $swal_script = "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menghapus data.',
                        background: '#111', color: '#fff', confirmButtonColor: '#ff0000'
                    });
                });
            </script>";
        }
        $stmt->close();
    } // <-- tutup if ($stmt)
} // <-- tutup if (isset($_GET['idd']))

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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/admin_daftar.css">
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">

</head>

<body>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        
        <!-- Header -->
        <div class="header-area">
            <h2 class="page-title">Kelola Laporan Petugas</h2>
            
            <!-- Search Form -->
            <form action="" method="post" class="search-box d-flex">
                <input type="text" name="cr" class="form-control" placeholder="Cari nama, alamat...">
                <button type="submit" name="cari" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="table-container table-responsive">
            <table class="table table-dark table-hover text-center mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pelapor</th>
                        <th>Alamat</th>
                        <th>Isi Laporan</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query Dasar
                    $query = "SELECT * FROM pengaduan ORDER BY waktu_pembuatan DESC";
                    
                    // Filter Pencarian
                    if (isset($_POST['cari']) && !empty($_POST['cr'])) {
                        $cr = $conn->real_escape_string($_POST['cr']);
                        $query = "SELECT * FROM pengaduan WHERE nama_pengguna LIKE '%$cr%' OR isi_laporan LIKE '%$cr%' OR alamat LIKE '%$cr%' ORDER BY waktu_pembuatan DESC";
                    }

                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($data = $result->fetch_assoc()) {
                            // Logic Warna Status
                            $status = $data['status'];
                            $badge_class = 'bg-secondary';
                            
                            if ($status == 'Selesai') $badge_class = 'bg-success';
                            elseif ($status == 'Ditolak') $badge_class = 'bg-danger';
                            elseif ($status == 'Ditanggapi') $badge_class = 'bg-primary';
                            else $badge_class = 'bg-warning';

                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . date('d/m/Y H:i', strtotime($data['waktu_pembuatan'])) . "</td>";
                            echo "<td class='fw-bold text-white'>" . htmlspecialchars($data['nama_pengguna']) . "</td>";
                            echo "<td>" . htmlspecialchars($data['alamat']) . "</td>";
                            echo "<td class='text-start'>" . substr(htmlspecialchars($data['isi_laporan']), 0, 50) . "...</td>";

                            // Logic Dokumentasi
                            $dok_html = "<span class='text-muted small'>-</span>";
                            if (!empty($data['dokumentasi'])) {
                                $file_path = "image/" . htmlspecialchars($data['dokumentasi']);
                                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                                
                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']) && file_exists($file_path)) {
                                    $dok_html = "<a href='$file_path' target='_blank' class='doc-icon'><i class='fa fa-image fa-lg'></i></a>";
                                } elseif (in_array($ext, ['mp4']) && file_exists($file_path)) {
                                    $dok_html = "<a href='$file_path' target='_blank' class='doc-icon'><i class='fa fa-video fa-lg'></i></a>";
                                } elseif (file_exists($file_path)) {
                                    $dok_html = "<a href='$file_path' target='_blank' class='doc-icon'><i class='fa fa-file-alt fa-lg'></i></a>";
                                }
                            }
                            echo "<td>$dok_html</td>";

                            // Status Badge
                            echo "<td><span class='status-badge $badge_class'>" . ucfirst($status ?? 'Proses') . "</span></td>";

                            // Kolom Aksi (Quick Action Buttons)
                            echo "<td>
                                    <div class='d-flex justify-content-center'>";
                                    
                                    // Cek apakah sudah ada tanggapan
                                    $check_tanggapan = $conn->query("SELECT COUNT(*) as jumlah FROM tanggapan WHERE id_pengaduan = " . $data['id_pengaduan']);
                                    $row_tanggapan = $check_tanggapan->fetch_assoc();
                                    $sudah_ditanggapi = $row_tanggapan['jumlah'] > 0;

                                    // Jika sudah ditanggapi, tampilkan tombol Lihat Tanggapan
                                    if ($sudah_ditanggapi) {
                                        echo "<a href='lihat_tanggapan_admin.php?id=" . $data['id_pengaduan'] . "' class='btn-action reply' title='Lihat Tanggapan' style='background: rgba(13, 110, 253, 0.3); color: #0d6efd;'>
                                                <i class='fas fa-eye'></i>
                                              </a>";
                                    } else {
                                        // Jika belum ditanggapi, tampilkan tombol Tanggapi
                                        echo "<a href='tanggapan_admin.php?id=" . $data['id_pengaduan'] . "' class='btn-action reply' title='Tanggapi'>
                                                <i class='fas fa-reply'></i>
                                              </a>";
                                    }

                                    // Tombol Check/Reject (Hanya jika belum selesai/ditolak)
                                    if ($status != 'Selesai' && $status != 'Ditolak') {
                                        echo "<a href='#' onclick='confirmAction(\"selesai\", " . $data['id_pengaduan'] . ")' class='btn-action check' title='Tandai Selesai'>
                                                <i class='fas fa-check'></i>
                                              </a>
                                              <a href='#' onclick='confirmAction(\"ditolak\", " . $data['id_pengaduan'] . ")' class='btn-action reject' title='Tolak'>
                                                <i class='fas fa-times'></i>
                                              </a>";
                                    }

                                    // Tombol Hapus
                                    echo "<a href='#' onclick='confirmDelete(" . $data['id_pengaduan'] . ")' class='btn-action delete' title='Hapus'>
                                            <i class='fas fa-trash'></i>
                                          </a>";

                            echo "  </div>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='py-4 text-muted'>Belum ada data laporan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Output PHP SweetAlert Script -->
    <?php echo $swal_script; ?>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logout Alert
        function logout() {
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Anda akan keluar dari sesi petugas.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff0000',
                cancelButtonColor: '#333',
                confirmButtonText: 'Ya, Logout',
                background: '#111', color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
                }
            })
        }

        // Delete Alert
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#333',
                confirmButtonText: 'Ya, Hapus',
                background: '#111', color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'daftar_laporan_admin.php?idd=' + id;
                }
            })
        }

        // Status Action Alert (Selesai/Tolak)
        function confirmAction(action, id) {
            let titleText = action === 'selesai' ? 'Tandai Selesai?' : 'Tolak Laporan?';
            let btnColor = action === 'selesai' ? '#198754' : '#dc3545';
            
            Swal.fire({
                title: titleText,
                text: "Status laporan akan diperbarui.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: btnColor,
                cancelButtonColor: '#333',
                confirmButtonText: 'Ya, Lanjutkan',
                background: '#111', color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'daftar_laporan_admin.php?action=' + action + '&id=' + id;
                }
            })
        }
    </script>
</body>
</html>