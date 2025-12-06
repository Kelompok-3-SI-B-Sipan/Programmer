<?php
session_start();

// Jika id tidak ada, kembali ke daftar laporan
if (!isset($_GET['id'])) {
    header("location: daftar_laporan_pengguna.php");
    exit();
}

require "koneksi.php";

// Ambil data laporan
$id = intval($_GET['id']);
$sql_pengaduan = "SELECT * FROM pengaduan WHERE id_pengaduan = $id";
$result_pengaduan = mysqli_query($conn, $sql_pengaduan);

$data_pengaduan = null;
if ($result_pengaduan && mysqli_num_rows($result_pengaduan) > 0) {
    $data_pengaduan = mysqli_fetch_assoc($result_pengaduan);
}

// Ambil data tanggapan
$sql_tanggapan = "SELECT * FROM tanggapan WHERE id_pengaduan = $id";
$result_tanggapan = mysqli_query($conn, $sql_tanggapan);

$data_tanggapan = null;
if ($result_tanggapan && mysqli_num_rows($result_tanggapan) > 0) {
    $data_tanggapan = mysqli_fetch_assoc($result_tanggapan);
}

// Jika data laporan benar-benar tidak ditemukan
if (!$data_pengaduan) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Laporan Tidak Ditemukan',
            text: 'Data laporan tidak tersedia atau telah dihapus.',
        }).then(() => {
            window.location.href = 'daftar_laporan_pengguna.php';
        });
    </script>";
    exit();
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>SIPAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap, Font Awesome, Google Fonts, SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <link rel="stylesheet" href="css/lihat_pengguna.css">
</head>

<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="response-card">
                <h3 class="card-title"><i class="fas fa-comments"></i> Tanggapan Petugas</h3>

                <form method="post">

                    <?php if ($data_tanggapan): ?>
                        <div class="text-center">
                            <span class="date-badge">
                                <i class="far fa-clock me-1"></i> 
                                Ditanggapi pada: <?= date('d M Y, H:i', strtotime($data_tanggapan['waktu_tanggapan'])) ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <label class="text-secondary mb-2 small text-uppercase fw-bold">Isi Pesan:</label>
                            <textarea class="response-box" readonly><?= htmlspecialchars($data_tanggapan['keterangan']) ?></textarea>
                        </div>

                        <?php if (!empty($data_tanggapan['file_upload'])): ?>
                            <?php 
                                $file_path = $data_tanggapan['file_upload'];
                                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                            ?>
                            <div class="file-attachment">
                                <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                    <a href="<?= $file_path ?>" target="_blank">
                                        <img src="<?= $file_path ?>" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                    </a>
                                <?php elseif (in_array($ext, ['mp4', 'mov', 'avi'])): ?>
                                    <a href="<?= $file_path ?>" target="_blank">
                                        <i class="fas fa-video file-icon"></i>
                                        <span class="file-link">Putar Video</span>
                                    </a>
                                <?php elseif (in_array($ext, ['pdf', 'docx', 'doc', 'xls', 'xlsx'])): ?>
                                    <a href="<?= $file_path ?>" target="_blank">
                                        <i class="fas fa-file-alt file-icon"></i>
                                        <span class="file-link">Unduh Dokumen (<?= strtoupper($ext) ?>)</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $file_path ?>" target="_blank">
                                        <i class="fas fa-file file-icon"></i>
                                        <span class="file-link">Buka File</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-hourglass-half fa-4x text-secondary mb-3"></i>
                            <h5 class="text-white">Menunggu Respon</h5>
                            <p class="text-secondary">Laporan Anda sedang ditinjau oleh petugas.</p>
                            <textarea class="response-box" readonly>(Belum ada tanggapan)</textarea>
                        </div>
                    <?php endif; ?>

                    <!-- Status -->
                    <?php if ($data_pengaduan['status'] == "Selesai"): ?>
                        <div class="status-box status-success">
                            <i class="fas fa-check-circle me-2"></i> Laporan Anda Telah Selesai.
                        </div>
                    <?php elseif ($data_pengaduan['status'] == "Ditolak"): ?>
                        <div class="status-box status-danger">
                            <i class="fas fa-times-circle me-2"></i> Mohon Maaf, Laporan Anda Ditolak.
                        </div>
                    <?php else: ?>
                        <div class="status-box status-waiting">
                            <i class="fas fa-sync-alt fa-spin me-2"></i> Status: <?= htmlspecialchars($data_pengaduan['status']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-25">
                        <a href="daftar_laporan_pengguna.php" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
