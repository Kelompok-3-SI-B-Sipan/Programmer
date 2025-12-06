<?php
session_start();
include 'koneksi.php';

// Memastikan sesi pengguna ada
if (!isset($_SESSION['nama_pengguna'])) {
    header('Location: login.php');
    exit();
}

// Proses hapus laporan (Dimodifikasi outputnya untuk SweetAlert)
if (isset($_GET['hapus'])) {
    $id_pengaduan = intval($_GET['hapus']); 
    $nama_pengguna = $_SESSION['nama_pengguna'];

    $delete_query = "DELETE FROM pengaduan WHERE id_pengaduan = ? AND nama_pengguna = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "is", $id_pengaduan, $nama_pengguna);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Pengaduan berhasil dihapus.',
                background: '#111',
                color: '#ffffff',
                confirmButtonColor: '#ff0000'
            }).then(() => {
                window.location.href='daftar_laporan_pengguna.php';
            });
        });
        </script>";
    } else {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal menghapus pengaduan.',
                background: '#111',
                color: '#ffffff',
                confirmButtonColor: '#ff0000'
            });
        });
        </script>";
    }
    mysqli_stmt_close($stmt);
}

// Ambil daftar pengaduan pengguna
$query = "SELECT id_pengaduan, nama_pengguna, isi_laporan, dokumentasi, status, waktu_pembuatan, tlp, alamat 
          FROM pengaduan WHERE nama_pengguna = ? ORDER BY waktu_pembuatan DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['nama_pengguna']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_pengaduan, $nama_pengguna, $isi_laporan, $dokumentasi, $status, $waktu_pembuatan, $tlp, $alamat);
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>SIPAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/pengguna_daftar.css">
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">

    
</head>
<body>

<div class="container my-5">
    <div class="content-wrapper">

        <h2 class="mb-4 text-center"><strong>Riwayat Pengaduan</strong></h2>
        <div class="table-responsive">
            <table class="table table-hover text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Isi Laporan</th>
                        <th>Dokumentasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $allowed_image_extensions = ['jpg','jpeg','png','gif'];
                    $allowed_video_extensions = ['mp4'];
                    $allowed_document_extensions = ['pdf','doc','docx','ppt','pptx','txt'];

                    while (mysqli_stmt_fetch($stmt)) {
                        $id_pengaduan        = $id_pengaduan ?? 0;
                        $dokumentasi         = $dokumentasi ?? '';
                        $isi_laporan         = $isi_laporan ?? '';
                        $status              = $status ?? 'proses';
                        $waktu_pembuatan_val = $waktu_pembuatan ?? '';
                        $tlp                 = $tlp ?? '';
                        $alamat              = $alamat ?? '';

                        $dokumentasi_safe = trim($dokumentasi);
                        $dokumentasi_path = $dokumentasi_safe !== '' ? "image/" . $dokumentasi_safe : '';
                        $file_extension = $dokumentasi_path !== '' ? strtolower(pathinfo($dokumentasi_path, PATHINFO_EXTENSION)) : '';

                        $status_cek = strtolower($status);

                        if ($status_cek == 'selesai') {
                            $badge_class = 'bg-success';
                        } elseif ($status_cek == 'ditolak') {
                            $badge_class = 'bg-danger';
                        } else {
                            $badge_class = 'bg-warning text-dark';
                        }

                        $tanggal_display = '-';
                        if ($waktu_pembuatan_val !== '' && strtotime($waktu_pembuatan_val) !== false) {
                            $tanggal_display = date('d M Y, H:i', strtotime($waktu_pembuatan_val));
                        }
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $tanggal_display; ?></td>
                            <td class="text-start" style="max-width: 300px;">
                                <?= strlen($isi_laporan) > 50 ? substr(htmlspecialchars($isi_laporan), 0, 50) . '...' : htmlspecialchars($isi_laporan); ?>
                            </td>
                            <td>
                                <?php
                                if ($dokumentasi_path !== '' && file_exists($dokumentasi_path)) {
                                    if (in_array($file_extension, $allowed_image_extensions)) {
                                        echo "<a href='" . htmlspecialchars($dokumentasi_path, ENT_QUOTES) . "' target='_blank'><i class='fa fa-image fa-lg'></i></a>";
                                    } elseif (in_array($file_extension, $allowed_video_extensions)) {
                                        echo "<a href='" . htmlspecialchars($dokumentasi_path, ENT_QUOTES) . "' target='_blank'><i class='fa fa-video fa-lg'></i></a>";
                                    } elseif (in_array($file_extension, $allowed_document_extensions)) {
                                        echo "<a href='" . htmlspecialchars($dokumentasi_path, ENT_QUOTES) . "' target='_blank'><i class='fa fa-file fa-lg'></i></a>";
                                    } else {
                                        echo "<span class='text-muted small'>-</span>";
                                    }
                                } else {
                                    echo "<span class='text-muted small'>-</span>";
                                }
                                ?>
                            </td>
                            <td>
                                <span class="badge rounded-pill <?= $badge_class; ?> px-3">
                                    <?= htmlspecialchars(ucfirst($status_cek)); ?>
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" style="border-color: #444;">
                                        Opsi
                                    </button>
                                    <ul class="dropdown-menu">
                                        
                                        <?php if ($status_cek != 'selesai' && $status_cek != 'ditolak'): ?>
                                            <li>
                                                <a class="dropdown-item" href="edit_laporan.php?id=<?= $id_pengaduan; ?>">
                                                    <i class="fa fa-edit me-2"></i> Edit
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php if ($status_cek == 'selesai' || $status_cek == 'ditolak'): ?>
                                            <li>
                                                <a class="dropdown-item" href="lihat_laporan_pengguna.php?id=<?= $id_pengaduan; ?>">
                                                    <i class="fa fa-reply me-2"></i> Lihat Tanggapan
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <li><hr class="dropdown-divider bg-secondary"></li>
                                        
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="konfirmasiHapus(event, <?= $id_pengaduan; ?>)">
                                                <i class="fa fa-trash me-2"></i> Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }

                    if ($no === 1) {
                        echo '<tr><td colspan="6" class="py-4 text-white-50">Belum ada pengaduan yang dibuat.</td></tr>';
                    }

                    mysqli_stmt_close($stmt);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function konfirmasiHapus(event, id) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data pengaduan ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#111',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'daftar_laporan_pengguna.php?hapus=' + id;
            }
        })
    }
</script>

</body>
</html>