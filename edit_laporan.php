<?php
session_start();
include 'koneksi.php';

// Pastikan user login
if (!isset($_SESSION['nama_pengguna'])) {
    header('Location: login.php');
    exit();
}

// Ambil id pengaduan dari URL
$id_pengaduan = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_pengaduan <= 0) {
    header('Location: daftar_laporan_pengguna.php');
    exit();
}

// Ambil data pengaduan, pastikan milik user yang login
$stmt = $conn->prepare("SELECT id_pengaduan, nama_pengguna, tlp, alamat, dokumentasi, isi_laporan, waktu_pembuatan, status FROM pengaduan WHERE id_pengaduan = ? AND nama_pengguna = ?");
$stmt->bind_param("is", $id_pengaduan, $_SESSION['nama_pengguna']);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    // Tidak ditemukan atau bukan milik user
    header('Location: daftar_laporan_pengguna.php?error=not_found');
    exit();
}

$data = $res->fetch_assoc();
$stmt->close();

// Cek apakah laporan masih bisa diedit (hanya jika status bukan Selesai atau Ditolak)
$status_lower = strtolower($data['status']);
if ($status_lower === 'selesai' || $status_lower === 'ditolak') {
    header('Location: daftar_laporan_pengguna.php?error=cannot_edit');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $tlp_input = trim($_POST['tlp'] ?? '');
    $alamat_input = trim($_POST['alamat'] ?? '');
    $isi_laporan_input = trim($_POST['isi_laporan'] ?? '');

    // Validasi input
    if (empty($tlp_input)) $errors[] = 'Nomor telepon wajib diisi.';
    if (empty($alamat_input)) $errors[] = 'Alamat wajib diisi.';
    if (empty($isi_laporan_input)) $errors[] = 'Isi laporan wajib diisi.';
    
    // Validasi panjang
    if (strlen($tlp_input) < 10) $errors[] = 'Nomor telepon minimal 10 karakter.';
    if (strlen($alamat_input) < 10) $errors[] = 'Alamat minimal 10 karakter.';
    if (strlen($isi_laporan_input) < 20) $errors[] = 'Isi laporan minimal 20 karakter.';

    // Handle file upload (opsional)
    $upload_dir = __DIR__ . '/image/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $new_filename = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $original_name = basename($_FILES['file']['name']);
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','mp4','pdf','doc','docx','ppt','pptx','txt'];

            if (!in_array($ext, $allowed)) {
                $errors[] = 'Format file tidak diperbolehkan.';
            } elseif ($_FILES['file']['size'] > 5242880) { // 5MB
                $errors[] = 'Ukuran file terlalu besar (maksimal 5MB).';
            } else {
                $new_filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $target_path = $upload_dir . $new_filename;
                if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
                    $errors[] = 'Gagal mengunggah file.';
                    $new_filename = null;
                }
            }
        } else {
            $errors[] = 'Terjadi kesalahan saat upload file.';
        }
    }

    if (empty($errors)) {
        // Update database
        if ($new_filename !== null) {
            // Hapus file lama jika ada
            if (!empty($data['dokumentasi']) && file_exists($upload_dir . $data['dokumentasi'])) {
                @unlink($upload_dir . $data['dokumentasi']);
            }
            $sql = "UPDATE pengaduan SET tlp = ?, alamat = ?, isi_laporan = ?, dokumentasi = ? WHERE id_pengaduan = ? AND nama_pengguna = ?";
            $stmt_upd = $conn->prepare($sql);
            $stmt_upd->bind_param("ssssss", $tlp_input, $alamat_input, $isi_laporan_input, $new_filename, $id_pengaduan, $_SESSION['nama_pengguna']);
        } else {
            $sql = "UPDATE pengaduan SET tlp = ?, alamat = ?, isi_laporan = ? WHERE id_pengaduan = ? AND nama_pengguna = ?";
            $stmt_upd = $conn->prepare($sql);
            $stmt_upd->bind_param("sssss", $tlp_input, $alamat_input, $isi_laporan_input, $id_pengaduan, $_SESSION['nama_pengguna']);
        }

        if ($stmt_upd->execute()) {
            $stmt_upd->close();
            // Redirect ke daftar laporan dengan pesan success
            header('Location: daftar_laporan_pengguna.php?success=edit');
            exit();
        } else {
            $errors[] = 'Gagal menyimpan perubahan: ' . $stmt_upd->error;
            $stmt_upd->close();
            // Jika update gagal, hapus file yang baru diupload
            if ($new_filename !== null && file_exists($upload_dir . $new_filename)) {
                @unlink($upload_dir . $new_filename);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SUARAKU</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
        <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
<link rel="stylesheet" href="css/edit.css">
    
</head>
<body>

<div class="container">
    

    <div class="edit-card">
        <h3 class="card-title"><i class="fa fa-pen-to-square"></i> Edit Laporan</h3>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-custom alert-dismissible fade show mb-4" role="alert">
                <strong><i class="fa fa-exclamation-triangle me-2"></i> Perhatian:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label class="form-label"><i class="fa fa-user"></i> Nama Pengguna</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_pengguna']) ?>" disabled>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fa fa-phone"></i> Nomor Telepon</label>
                    <input type="text" name="tlp" class="form-control" value="<?= htmlspecialchars($_POST['tlp'] ?? $data['tlp']) ?>" placeholder="08..." required>
                    <small class="text-muted">Min. 10 karakter</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fa fa-map-marker-alt"></i> Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($_POST['alamat'] ?? $data['alamat']) ?>" placeholder="Alamat lengkap" required>
                    <small class="text-muted">Min. 10 karakter</small>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fa fa-file-alt"></i> Isi Laporan</label>
                <textarea name="isi_laporan" rows="6" class="form-control" placeholder="Jelaskan detail laporan..." required><?= htmlspecialchars($_POST['isi_laporan'] ?? $data['isi_laporan']) ?></textarea>
                <small class="text-muted">Min. 20 karakter</small>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="fa fa-image"></i> Dokumentasi / Bukti</label>
                
                <!-- Existing File Display -->
                <?php if (!empty($data['dokumentasi']) && file_exists(__DIR__ . '/image/' . $data['dokumentasi'])): ?>
                    <div class="existing-file">
                        <div class="d-flex align-items-center">
                            <?php 
                                $path = 'image/' . htmlspecialchars($data['dokumentasi']);
                                $ext = strtolower(pathinfo($data['dokumentasi'], PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg','jpeg','png','gif'])): 
                            ?>
                                <img src="<?= $path ?>" class="file-preview-img me-3" alt="Preview">
                            <?php else: ?>
                                <i class="fas fa-file-alt fa-2x me-3 text-secondary"></i>
                            <?php endif; ?>
                            
                            <div>
                                <div class="text-white small mb-1">File saat ini:</div>
                                <a href="<?= $path ?>" target="_blank" class="text-danger text-decoration-none small fw-bold">
                                    <i class="fa fa-external-link-alt"></i> Lihat / Unduh
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <input type="file" name="file" class="form-control">
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle me-1"></i> Upload file baru untuk mengganti (JPG, PNG, MP4, PDF, DOCX). Maks 5MB.
                </small>
            </div>

            <div class="text-end pt-3 border-top border-secondary border-opacity-25">
                <button type="submit" name="simpan" class="btn-save">
                    <i class="fa fa-save me-2"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>