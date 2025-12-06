<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['nama_pengguna']) || empty($_SESSION['nama_pengguna'])) {
    header('Location: login.php');
    exit();
}

// Ambil biodata pengguna dari tabel "pengguna"
$nama_pengguna = $tlp = $alamat = "";

if (isset($_SESSION['nama_pengguna'])) {
    $sqlpengguna = "SELECT nama_pengguna, tlp, alamat FROM pengguna WHERE nama_pengguna = '$_SESSION[nama_pengguna]' LIMIT 1";
    $result = mysqli_query($conn, $sqlpengguna);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $nama_pengguna = $row['nama_pengguna'];
        $tlp = $row['tlp'];
        $alamat = $row['alamat'];
    }
}

// Proses simpan laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $dokumentasi = '';
    $tlp = htmlspecialchars($_POST['tlp']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $isi_laporan = htmlspecialchars($_POST['isi_laporan']);

    // Upload dokumentasi
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $dokumentasi = basename($_FILES['file']['name']);
        $target_dir = "image/";
        $target_file = $target_dir . $dokumentasi;

        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg','jpeg','png','gif','mp4','pdf','doc','docx','ppt','pptx','txt'];

        if (!in_array($file_ext, $allowed_ext)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Tidak Diizinkan',
                        text: 'Format file tidak didukung!',
                        background: '#111',
                        color: '#e0e0e0',
                        confirmButtonColor: '#ff0000'
                    });
                });
            </script>";
            $dokumentasi = '';
        } else {
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Upload',
                            text: 'Gagal mengunggah file ke server!',
                            background: '#111',
                            color: '#e0e0e0',
                            confirmButtonColor: '#ff0000'
                        });
                    });
                </script>";
                $dokumentasi = '';
            }
        }
    }

    // Validasi
    if (empty($dokumentasi)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Dokumentasi wajib diunggah!',
                    background: '#111',
                    color: '#e0e0e0',
                    confirmButtonColor: '#ff0000'
                });
            });
        </script>";
    } elseif (empty($isi_laporan)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Isi laporan wajib diisi!',
                    background: '#111',
                    color: '#e0e0e0',
                    confirmButtonColor: '#ff0000'
                });
            });
        </script>";
    } else {
        $query = "INSERT INTO pengaduan (nama_pengguna, tlp, alamat, dokumentasi, isi_laporan, waktu_pembuatan)
                  VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $query)) {
            $waktu_pembuatan = date('Y-m-d H:i:s');
            mysqli_stmt_bind_param($stmt, "ssssss", $nama_pengguna, $tlp, $alamat, $dokumentasi, $isi_laporan, $waktu_pembuatan);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil disimpan.',
                            background: '#111',
                            color: '#e0e0e0',
                            confirmButtonColor: '#ff0000'
                        }).then(() => {
                            window.location.href = 'daftar_laporan_pengguna.php';
                        });
                    });
                </script>";
            } else {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menyimpan data.',
                            background: '#111',
                            color: '#e0e0e0',
                            confirmButtonColor: '#ff0000'
                        });
                    });
                </script>";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPAN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <link rel="stylesheet" href="css/pengaduan.css">
</head>
<body>

<div class="container">
    <div class="glass-container mx-auto">
        <h4 class="text-center title-glow">
            <i class="fas fa-clipboard-check"></i> Form Pengaduan
        </h4>
        
        <form method="post" enctype="multipart/form-data">
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Waktu Pembuatan</label>
                    <input type="text" class="form-control" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" value="<?php echo $nama_pengguna; ?>" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" name="tlp" class="form-control" value="<?php echo $tlp; ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="<?php echo $alamat; ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Isi Laporan</label>
                <textarea name="isi_laporan" class="form-control" rows="5" required></textarea>
            </div>

            <div class="mb-5">
                <label class="form-label">Unggah Dokumentasi</label>
                <input type="file" name="file" class="form-control" required>
                <small style="color:#666;">Format: JPG, PNG, PDF, DOCX, PPTX, TXT</small>
            </div>

            <div class="d-flex justify-content-between border-top pt-3">
                <button type="reset" class="btn btn-danger">
                    <i class="fas fa-rotate-left me-2"></i> Reset
                </button>
                <button type="submit" name="simpan" class="btn btn-primary px-5">
                    <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
                </button>
            </div>

        </form>
    </div>
</div>

</body>
</html>
