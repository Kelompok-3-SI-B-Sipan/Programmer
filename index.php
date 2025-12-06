<?php
session_start();
include 'koneksi.php';

// Pastikan variabel session ada
$isLoggedIn = false;
if (isset($_SESSION['nama_pengguna']) && !empty($_SESSION['nama_pengguna'])) {
    $isLoggedIn = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPAN</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/index.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image" href="img/dengar_suaraku.png">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">SIPAN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#tentang">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#our-team">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="data_pengaduan.php">Data Pengaduan</a></li>
                </ul>

                <!-- Tombol Login & Daftar -->
                <div class="d-flex ms-3 gap-2">
                    <a href="login.php" class="btn btn-box-red" role="button">Login</a>
                    <a href="signup.php" class="btn btn-box-red" role="button">Daftar</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Section Tentang Kami -->
    <div id="tentang" class="section text-center mt-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-8" data-aos="fade-down" data-aos-duration="1000">Tentang Kami</h2>
                    <p class="lead mb-10" data-aos="fade-up" data-aos-duration="1000">
                        Selamat datang di SIPAN, solusi inovatif untuk pengelolaan pengaduan sederhana.
                        Kami adalah tim yang berdedikasi untuk memberikan sistem yang mudah digunakan dan efisien.
                        Dengan teknologi mutakhir, kami siap membantu institusi pemerintahan guna meningkatkan pengaduan yang akurat. 
                        Mari bersama-sama membangun masa depan negara yang lebih baik dan berkualitas.
                    </p>
                    
                    <!-- Tombol ADUKAN - Selalu tampilkan button dengan onclick showLoginAlert -->
                    <button type="button" class="btn btn-red ms-2" data-aos="fade-up" data-aos-duration="1000" onclick="handleAdukanClick()">
                        <strong>ADUKAN</strong>
                    </button>
                </div>
                <div class="col-lg-6">
                    <div class="image-container">
                        <img src="img/pengaduan.png" onerror="this.src='https://placehold.co/600x400/333/red?text=Tentang+Kami'" class="img-fluid about-image" alt="Tentang Kami Illustration" style="max-width: 82%; height: auto;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Tim -->
    <section id="our-team" class="section bg-dark text-white">
        <div class="container">
            <h2 class="text-center mb-5">Our Team</h2>
            <div class="row justify-content-center">
                
                <!-- Member 1 -->
                <div class="col-lg-4 col-md-6 team-member">
                    <div class="card text-center team-card">
                        <img src="img/dosen pembimbing.png" onerror="this.src='https://placehold.co/400x500/333/red?text=Dosen'" class="card-img-top" alt="Team Member 1">
                        <div class="card-body">
                            <a href="https://ung.ac.id/formasi/people/197903312012121001" class="card-title" target="_blank"><h5>Rahman Takdir</h5></a>
                            <p class="card-text text-white">Dosen pembimbing</p>
                            <ul class="list-inline follow-us">
                                <li class="list-inline-item"><a href="082133638646" class="btn btn-social" target="_blank"><i class="fab fa-whatsapp fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="mailto:prstosugi@gmail.com" class="btn btn-social" target="_blank"><i class="fas fa-envelope fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="sugi" class="btn btn-social" target="_blank"><i class="fab fa-instagram fa-lg"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Member 3 -->
                <div class="col-lg-4 col-md-6 team-member">
                    <div class="card text-center team-card">
                        <img src="img/fauzia.png" onerror="this.src='https://placehold.co/400x500/333/red?text=Frontend'" class="card-img-top" alt="Team Member 3">
                        <div class="card-body">
                            <a href="https://mahasiswa.ung.ac.id/531423026/about" class="card-title" target="_blank"><h5>Fauzia Nur'aini Kuku</h5></a>
                            <p class="card-text text-white">Project Manager</p>
                            <ul class="list-inline follow-us">
                                <li class="list-inline-item"><a href="https://wa.me/085266093736" class="btn btn-social" target="_blank"><i class="fab fa-whatsapp fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="mailto:prstosugi@gmail.com" class="btn btn-social" target="_blank"><i class="fas fa-envelope fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="https://www.instagram.com/jiaaku_?igsh=dHUyMTQ2YTR0M3Z6" class="btn btn-social" target="_blank"><i class="fab fa-instagram fa-lg"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Member 2 -->
                <div class="col-lg-4 col-md-6 team-member">
                    <div class="card text-center team-card">
                        <img src="img/sugi.png" onerror="this.src='https://placehold.co/400x500/333/red?text=Backend'" class="card-img-top" alt="Team Member 2">
                        <div class="card-body">
                            <a href="https://giyku-portofolio.web.app/" class="card-title" target="_blank"><h5>Sugiyanto Prasetio</h5></a>
                            <p class="card-text text-white">Fullstak Developer</p>
                            <ul class="list-inline follow-us">
                                <li class="list-inline-item"><a href="https://wa.me/085932931415" class="btn btn-social" target="_blank"><i class="fab fa-whatsapp fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="mailto:prstosugi@gmail.com" class="btn btn-social" target="_blank"><i class="fas fa-envelope fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="https://www.instagram.com/prstio_____" class="btn btn-social" target="_blank"><i class="fab fa-instagram fa-lg"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Member 5 -->
                <div class="col-lg-4 col-md-6 team-member">
                    <div class="card text-center team-card">
                        <img src="img/pami.png" onerror="this.src='https://placehold.co/400x500/333/red?text=UI/UX'" class="card-img-top" alt="Team Member 4">
                        <div class="card-body">
                            <a href="https://mahasiswa.ung.ac.id/531423028/about" class="card-title" target="_blank"><h5>Fahmi Hainunisa</h5></a>
                            <p class="card-text text-white">DevOps</p>
                            <ul class="list-inline follow-us">
                                <li class="list-inline-item"><a href="https://wa.me/085757538097" class="btn btn-social" target="_blank"><i class="fab fa-whatsapp fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="mailto:fahmihainunnisa777@gmail.com" class="btn btn-social" target="_blank"><i class="fas fa-envelope fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="https://www.instagram.com/fhmiiiihnsa?igsh=Z3B4b2M2MDRrcTJv" class="btn btn-social" target="_blank"><i class="fab fa-instagram fa-lg"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Member 4 -->
                <div class="col-lg-4 col-md-6 team-member">
                    <div class="card text-center team-card">
                        <img src="img/anti.png" onerror="this.src='https://placehold.co/400x500/333/red?text=Frontend+2'" class="card-img-top" alt="Team Member 3">
                        <div class="card-body">
                            <a href="" class="card-title" target="_blank"><h5>Surianti Makas</h5></a>
                            <p class="card-text text-white">UI/UX Designer</p>
                            <ul class="list-inline follow-us">
                                <li class="list-inline-item"><a href="https://wa.me/085796861190" class="btn btn-social" target="_blank"><i class="fab fa-whatsapp fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="makassurianti@gmail.com" class="btn btn-social" target="_blank"><i class="fas fa-envelope fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="https://www.instagram.com/antym__?igsh=dGlvYjd0Zjh6cGt5" class="btn btn-social" target="_blank"><i class="fab fa-instagram fa-lg"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Member 6 -->
                <div class="col-lg-4 col-md-6 team-member">
                    <div class="card text-center team-card">
                        <img src="img/ajii.png" onerror="this.src='https://placehold.co/400x500/333/red?text=UI/UX+2'" class="card-img-top" alt="Team Member 4">
                        <div class="card-body">
                            <a href="" class="card-title" target="_blank"><h5>Aji Muhammad Landung</h5></a>
                            <p class="card-text text-white">Quality Assurance</p>
                            <ul class="list-inline follow-us">
                                <li class="list-inline-item"><a href="https://wa.me/085924462146" class="btn btn-social" target="_blank"><i class="fab fa-whatsapp fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="mailto:ajimuhamadlandung@gmail.com" class="btn btn-social" target="_blank"><i class="fas fa-envelope fa-lg"></i></a></li>
                                <li class="list-inline-item"><a href="https://www.instagram.com/ajilandung_?igsh=MWFha3Z3N3Fka3p0aA%3D%3D&utm_source=qr" class="btn btn-social" target="_blank"><i class="fab fa-instagram fa-lg"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <script>
        function handleAdukanClick() {
            Swal.fire({
                title: 'Harap Login Terlebih Dahulu',
                text: 'Anda harus login untuk membuat pengaduan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Login Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ff0000',
                cancelButtonColor: '#333333',
                background: '#0b0b0b',
                color: '#ffffff',
                confirmButtonText: '<i class="fas fa-sign-in-alt"></i> Login',
                cancelButtonText: '<i class="fas fa-times"></i> Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>



