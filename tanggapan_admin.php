<?php
session_start();

// Cek apakah ID ada di URL
if (!isset($_GET['id'])) {
    header("location: daftar_laporan_admin.php");
    exit;
}

require "koneksi.php";

// Variabel untuk SweetAlert
$swal_script = "";

// Mengambil ID pengaduan dari URL
$id_pengaduan = (int)$_GET['id'];

// Query untuk mengambil data pengaduan
$sql_pengaduan = "SELECT * FROM pengaduan WHERE id_pengaduan = ?";
$stmt_pengaduan = $conn->prepare($sql_pengaduan);
$stmt_pengaduan->bind_param("i", $id_pengaduan);
$stmt_pengaduan->execute();
$result_pengaduan = $stmt_pengaduan->get_result();

// Validasi jika ID pengaduan tidak ditemukan
if ($result_pengaduan->num_rows === 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='daftar_laporan_admin.php';</script>";
    exit;
}

$data_pengaduan = $result_pengaduan->fetch_assoc();

// Ambil id_petugas dari session atau database dengan validasi
$id_petugas = null;

if (isset($_SESSION['id_petugas']) && !empty($_SESSION['id_petugas'])) {
    $id_petugas_session = (int)$_SESSION['id_petugas'];
    $check_petugas = "SELECT id_petugas FROM petugas WHERE id_petugas = ?";
    $stmt_check = $conn->prepare($check_petugas);
    $stmt_check->bind_param("i", $id_petugas_session);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        $id_petugas = $id_petugas_session;
    }
    $stmt_check->close();
}

if ($id_petugas === null) {
    $sql_petugas = "SELECT id_petugas FROM petugas LIMIT 1";
    $result_petugas = $conn->query($sql_petugas);
    
    if ($result_petugas && $result_petugas->num_rows > 0) {
        $row_petugas = $result_petugas->fetch_assoc();
        $id_petugas = $row_petugas['id_petugas'];
    } else {
        die("Error: Tidak ada petugas yang terdaftar.");
    }
}

// PROSES FORM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $waktu_tanggapan = date('Y-m-d H:i:s');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $file_path = null;

    if (!empty($keterangan)) {
        // Proses unggah file jika ada
        if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "image/";
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }

            $file_name = basename($_FILES['file_upload']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi', 'pdf', 'docx', 'doc', 'xls', 'xlsx'];

            if (in_array($file_ext, $allowed_ext) && $_FILES['file_upload']['size'] <= 2048000) { 
                $unique_name = uniqid("resp_", true) . "." . $file_ext;
                $file_path = $upload_dir . $unique_name;
                
                if (!move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path)) {
                    $swal_script = "<script>Swal.fire({icon: 'error', title: 'Gagal', text: 'Gagal mengunggah file.', background: '#111', color: '#fff'});</script>";
                    $file_path = null;
                }
            } else {
                $swal_script = "<script>Swal.fire({icon: 'error', title: 'Invalid', text: 'Format file salah atau ukuran > 2MB.', background: '#111', color: '#fff'});</script>";
                $file_path = null;
            }
        }

        // Query Simpan Tanggapan
        if (empty($swal_script)) { // Lanjut jika tidak ada error upload
            $sql_tanggapan = "INSERT INTO tanggapan (id_pengaduan, waktu_tanggapan, keterangan, id_petugas, file_upload) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_tanggapan);

            if ($stmt) {
                $stmt->bind_param("issis", $id_pengaduan, $waktu_tanggapan, $keterangan, $id_petugas, $file_path);

                if ($stmt->execute()) {
                    // Update Status Pengaduan
                    $update_status = "UPDATE pengaduan SET status = 'Selesai' WHERE id_pengaduan = ?";
                    $stmt_update = $conn->prepare($update_status);
                    $stmt_update->bind_param("i", $id_pengaduan);
                    $stmt_update->execute();
                    $stmt_update->close();

                    $swal_script = "
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terkirim!',
                                text: 'Tanggapan berhasil disimpan dan status laporan Selesai.',
                                background: '#111',
                                color: '#fff',
                                confirmButtonColor: '#ff0000'
                            }).then(() => {
                                window.location.href='daftar_laporan_admin.php';
                            });
                        });
                    </script>";
                } else {
                    $swal_script = "<script>Swal.fire({icon: 'error', title: 'Database Error', text: 'Gagal menyimpan tanggapan.', background: '#111', color: '#fff'});</script>";
                }
                $stmt->close();
            }
        }
    } else {
        $swal_script = "<script>Swal.fire({icon: 'warning', title: 'Kosong', text: 'Isi tanggapan tidak boleh kosong!', background: '#111', color: '#fff'});</script>";
    }
}
?>

<!DOCTYPE html>
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
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <link rel="stylesheet" href="css/admin_tanggapan.css">
     
</head>
<body>

<div class="response-card">
    <h4 class="page-title"><i class="fas fa-edit"></i> Beri Tanggapan</h4>
    
    <!-- Informasi Pengaduan -->
    <div class="info-box">
        <h5 class="mb-3 text-white border-bottom border-secondary pb-2">Detail Laporan</h5>
        <p><strong>Pelapor</strong> : <?= htmlspecialchars($data_pengaduan['nama_pengguna'] ?? '-') ?></p>
        <p><strong>Tanggal</strong> : <?= date('d F Y, H:i', strtotime($data_pengaduan['waktu_pembuatan'])) ?></p>
        <p><strong>Isi Laporan</strong> : <span class="d-block mt-1 fst-italic text-white-50">"<?= nl2br(htmlspecialchars($data_pengaduan['isi_laporan'])) ?>"</span></p>
    </div>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_pengaduan" value="<?= htmlspecialchars($id_pengaduan) ?>">
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Tanggal Tanggapan</label>
                <input type="text" class="form-control" value="<?= date('d F Y') ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label>Petugas Penanggung Jawab</label>
                <!-- Menampilkan ID Petugas yang aktif -->
                <input type="text" class="form-control" value="ID Petugas: <?= htmlspecialchars($id_petugas) ?>" readonly>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="keterangan">Isi Tanggapan <span class="text-danger">*</span></label>
            <textarea name="keterangan" id="keterangan" rows="6" class="form-control" required placeholder="Tuliskan solusi atau tindak lanjut dari laporan ini..."></textarea>
        </div>
        
        <div class="mb-4">
            <label for="file_upload">Lampiran Bukti (Opsional)</label>
            <input type="file" name="file_upload" id="file_upload" class="form-control">
            <small class="text-muted"><i class="fas fa-info-circle"></i> Format: PDF, DOCX, JPG, MP4. Maksimal 2MB.</small>
        </div>
        
        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary border-opacity-25">
            <a href="daftar_laporan_admin.php" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane me-2"></i> Kirim Tanggapan
            </button>
        </div>
    </form>
</div>

<!-- Output Script SweetAlert -->
<?php echo $swal_script; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>