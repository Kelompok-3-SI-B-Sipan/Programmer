<?php
session_start();
include 'koneksi.php';

// Cek ID
if (!isset($_GET['id'])) {
    header("Location: daftar_laporan_admin.php");
    exit;
}

$id_pengaduan = intval($_GET['id']);

// 1. Ambil data pengaduan
$stmt = $conn->prepare("SELECT id_pengaduan, nama_pengguna, isi_laporan, dokumentasi, status, waktu_pembuatan, tlp, alamat FROM pengaduan WHERE id_pengaduan = ?");
$stmt->bind_param("i", $id_pengaduan);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<script>alert('Data tidak ditemukan'); window.location='daftar_laporan_admin.php';</script>";
    exit;
}
$laporan = $res->fetch_assoc();
$stmt->close();

// 2. Ambil semua tanggapan terkait (Join dengan tabel petugas untuk nama)
$sql = "SELECT t.id_tanggapan, t.waktu_tanggapan, t.keterangan, t.file_upload, p.nama_petugas
        FROM tanggapan t
        LEFT JOIN petugas p ON t.id_petugas = p.id_petugas
        WHERE t.id_pengaduan = ?
        ORDER BY t.waktu_tanggapan DESC";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $id_pengaduan);
$stmt2->execute();
$res2 = $stmt2->get_result();
$tanggapan_list = [];
while ($row = $res2->fetch_assoc()) {
    $tanggapan_list[] = $row;
}
$stmt2->close();
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SIPAN</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <link rel="stylesheet" href="css/lihat_admin.css">

    
</head>
<body>

<div class="container">
    
    <div class="row">
        <!-- KOLOM KIRI: Detail Laporan -->
        <div class="col-lg-5 mb-4">
            <div class="detail-card h-100">
                <div class="card-header-custom">
                    <h5 class="m-0 text-white"><i class="fas fa-file-alt me-2 text-danger"></i> Detail Laporan</h5>
                    
                    <?php
                    // Badge Status
                    $status = $laporan['status'] ?? 'Proses';
                    $badgeClass = 'bg-secondary';
                    if ($status == 'Selesai') $badgeClass = 'bg-success';
                    elseif ($status == 'Ditolak') $badgeClass = 'bg-danger';
                    elseif ($status == 'Ditanggapi') $badgeClass = 'bg-primary';
                    elseif ($status == 'Proses') $badgeClass = 'bg-warning text-dark';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                </div>
                
                <div class="card-body-custom">
                    <!-- Metadata -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="info-label">Pelapor</div>
                            <div class="info-value"><?= htmlspecialchars($laporan['nama_pengguna']) ?></div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">Tanggal</div>
                            <div class="info-value"><?= date('d M Y, H:i', strtotime($laporan['waktu_pembuatan'])) ?></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Alamat / Kontak</div>
                            <div class="info-value"><?= htmlspecialchars($laporan['alamat']) ?> &bull; <?= htmlspecialchars($laporan['tlp']) ?></div>
                        </div>
                    </div>

                    <!-- Isi Laporan -->
                    <div class="info-label mb-2">Isi Pengaduan</div>
                    <div class="content-box mb-4">
                        <?= nl2br(htmlspecialchars($laporan['isi_laporan'])) ?>
                    </div>

                    <!-- Dokumentasi -->
                    <div class="info-label mb-2">Bukti Lampiran</div>
                    <?php if (!empty($laporan['dokumentasi']) && file_exists('image/' . $laporan['dokumentasi'])): ?>
                        <?php 
                            $path = 'image/' . $laporan['dokumentasi'];
                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        ?>
                        <div class="p-2 border border-secondary border-opacity-25 rounded bg-black">
                            <?php if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
                                <a href="<?= $path ?>" target="_blank">
                                    <img src="<?= $path ?>" class="img-fluid rounded border border-dark" alt="Bukti">
                                </a>
                            <?php elseif (in_array($ext, ['mp4'])): ?>
                                <video src="<?= $path ?>" controls class="w-100 rounded"></video>
                            <?php else: ?>
                                <a href="<?= $path ?>" target="_blank" class="btn btn-sm btn-outline-light w-100">
                                    <i class="fas fa-download me-2"></i> Unduh File (<?= strtoupper($ext) ?>)
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-secondary fst-italic small">Tidak ada lampiran.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Riwayat Tanggapan -->
        <div class="col-lg-7">
            <div class="detail-card h-100">
                <div class="card-header-custom">
                    <h5 class="m-0 text-white"><i class="fas fa-comments me-2 text-danger"></i> Riwayat Tanggapan</h5>
                    <span class="badge bg-dark border border-secondary"><?= count($tanggapan_list) ?> Balasan</span>
                </div>
                
                <div class="card-body-custom">
                    <?php if (count($tanggapan_list) === 0): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comment-slash fa-3x text-secondary mb-3"></i>
                            <p class="text-secondary">Belum ada tanggapan dari petugas.</p>
                            <a href="tanggapan_admin.php?id=<?= $id_pengaduan ?>" class="btn btn-sm btn-outline-danger mt-2">
                                <i class="fas fa-reply me-1"></i> Beri Tanggapan Sekarang
                            </a>
                        </div>
                    <?php else: ?>
                        
                        <!-- Loop Tanggapan -->
                        <?php foreach ($tanggapan_list as $t): ?>
                            <div class="response-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:35px; height:35px;">
                                            <i class="fas fa-user-shield small"></i>
                                        </div>
                                        <div>
                                            <div class="petugas-name"><?= htmlspecialchars($t['nama_petugas'] ?? 'Admin/Petugas') ?></div>
                                            <div class="response-date"><i class="far fa-clock me-1"></i> <?= date('d M Y, H:i', strtotime($t['waktu_tanggapan'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="response-text">
                                    <?= nl2br(htmlspecialchars($t['keterangan'])) ?>
                                </div>

                                <!-- Lampiran Tanggapan -->
                                <?php if (!empty($t['file_upload']) && file_exists($t['file_upload'])): ?>
                                    <?php 
                                        $tf = htmlspecialchars($t['file_upload']);
                                        $text = strtolower(pathinfo($tf, PATHINFO_EXTENSION));
                                    ?>
                                    <div class="attachment-box">
                                        <div class="small text-secondary mb-1">Lampiran Petugas:</div>
                                        <?php if (in_array($text, ['jpg','jpeg','png','gif'])): ?>
                                            <a href="<?= $tf ?>" target="_blank">
                                                <img src="<?= $tf ?>" class="img-thumbnail-custom" alt="Lampiran">
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= $tf ?>" target="_blank" class="attachment-link">
                                                <i class="fas fa-paperclip me-2"></i> Lihat File (<?= strtoupper($text) ?>)
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- Tombol Tambah Tanggapan di Bawah -->
                        <div class="text-end mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <a href="tanggapan_admin.php?id=<?= $id_pengaduan ?>" class="btn btn-primary btn-sm px-4" style="background: #ff0000; border:none;">
                                <i class="fas fa-plus me-1"></i> Tambah Tanggapan
                            </a>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>