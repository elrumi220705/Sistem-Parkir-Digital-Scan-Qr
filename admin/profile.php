<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}

include "../config/config.php";

$id = $_SESSION['id_user'];
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id = '$id'");
$user = mysqli_fetch_assoc($query);

// Tampilkan feedback SweetAlert
if (isset($_SESSION['success'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '" . $_SESSION['success'] . "',
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '" . $_SESSION['error'] . "'
            });
        });
    </script>";
    unset($_SESSION['error']);
}

if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $passwordBaru = $_POST['password'];
    $updatePassword = "";

    // Upload foto profil
    $fotoProfil = $user['foto_profil'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $extValid = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['foto']['size'];

        if (!in_array($ext, $extValid)) {
            $_SESSION['error'] = 'Format foto harus JPG, JPEG, atau PNG';
            header('Location: profile.php');
            exit;
        }

        if ($size > 2 * 1024 * 1024) {
            $_SESSION['error'] = 'Ukuran foto maksimal 2MB';
            header('Location: profile.php');
            exit;
        }

        $namaBaru = 'foto_' . time() . '.' . $ext;
        $uploadDir = '../uploads/foto_profil/';
        $uploadPath = $uploadDir . $namaBaru;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
            if (!empty($user['foto_profil']) && file_exists($uploadDir . $user['foto_profil'])) {
                unlink($uploadDir . $user['foto_profil']);
            }
            $fotoProfil = $namaBaru;
        } else {
            $_SESSION['error'] = 'Gagal mengunggah foto profil';
            header('Location: profile.php');
            exit;
        }
    }

    // Cek apakah password diubah
    if (!empty($passwordBaru)) {
        if (strlen($passwordBaru) < 8) {
            $_SESSION['error'] = 'Password harus minimal 8 karakter';
            header('Location: profile.php');
            exit;
        }

        // Cek apakah password baru sama dengan yang lama
        if (password_verify($passwordBaru, $user['password'])) {
            $_SESSION['error'] = 'Password baru tidak boleh sama dengan password lama';
            header('Location: profile.php');
            exit;
        }

        $hashed = password_hash($passwordBaru, PASSWORD_DEFAULT);
        $updatePassword = ", password = '$hashed'";
    }

    // Cek apakah ada perubahan
    $tidakAdaPerubahan = true;

    if ($username !== $user['username']) {
        $tidakAdaPerubahan = false;
    }

    if ($fotoProfil !== $user['foto_profil']) {
        $tidakAdaPerubahan = false;
    }

    if (!empty($updatePassword)) {
        $tidakAdaPerubahan = false;
    }

    if ($tidakAdaPerubahan) {
        $_SESSION['error'] = 'Tidak ada perubahan yang dilakukan';
        header('Location: profile.php');
        exit;
    }

    // Jalankan query update
    $query = "UPDATE user SET username='$username', foto_profil='$fotoProfil' $updatePassword WHERE id = '$id'";
    $update = mysqli_query($koneksi, $query);

    if ($update) {
        $_SESSION['username'] = $username;
        $_SESSION['foto_profil'] = $fotoProfil;
        $_SESSION['success'] = 'Profil berhasil diperbarui';
        header('Location: profile.php');
        exit;
    } else {
        $_SESSION['error'] = 'Gagal memperbarui profil: ' . mysqli_error($koneksi);
        header('Location: profile.php');
        exit;
    }
}
?>




<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil - EasyParkir</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <?php require_once('template/css.php'); ?>
    <style>
        .profile-header {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 3px solid white;
            font-size: 3.5rem;
        }

        .profile-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
        }

        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
            padding: 0.5rem 1.5rem;
        }

        .btn-primary:hover {
            background-color: #3a0ca3;
            border-color: #3a0ca3;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }
    </style>
</head>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        <?php require_once('template/sidebar.php'); ?>

        <div class="main-content">
            <!-- Navbar -->
            <?php require_once('template/nav.php'); ?>

            <!-- Profile Header -->
            <div class="profile-header">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="profile-avatar rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 overflow-hidden">
    <?php if (!empty($user['foto_profil']) && file_exists('../uploads/foto_profil/' . $user['foto_profil'])): ?>
        <img src="../uploads/foto_profil/<?= $user['foto_profil'] ?>" alt="Foto Profil" class="w-100 h-100 object-fit-cover">
    <?php else: ?>
        <i class="bi bi-person-fill text-white fs-1"></i>
    <?php endif; ?>
</div>
                        </div>
                        <div class="col-md-10">
                            <h2 class="fw-bold mb-1"><?= $_SESSION['username'] ?></h2>
                            <p class="mb-0">
                                <span class="badge bg-light text-primary"><?= ucfirst($_SESSION['role']) ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container my-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="profile-card card">
                            <div class="card-body p-4">
                                <h4 class="card-title fw-bold mb-4 text-primary">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Profil
                                </h4>

                                <form method="POST" enctype="multipart/form-data" id="profilForm">
                                    <div class="row">
                                        <div class="col-md-3 text-center mb-3">
                                            <?php if (!empty($user['foto_profil']) && file_exists('../uploads/foto_profil/' . $user['foto_profil'])): ?>
                                                <img src="../uploads/foto_profil/<?= $user['foto_profil'] ?>"
                                                    class="rounded-circle mb-2" width="100" height="100" alt="Foto Profil">
                                            <?php else: ?>
                                                <div
                                                    class="profile-avatar rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                                    <i class="bi bi-person-fill text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" name="foto" accept="image/*" class="form-control mt-2">
                                            <small class="form-text text-muted">Format: JPG, PNG. Max 2MB</small>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Username</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bi bi-person-fill"></i>
                                                        </span>
                                                        <input type="text" name="username" id="username"
                                                            class="form-control" value="<?= $user['username'] ?>"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">Email</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bi bi-envelope-fill"></i>
                                                        </span>
                                                        <input type="email" class="form-control"
                                                            value="<?= $user['email'] ?? 'Belum diatur' ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Password Baru</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-lock-fill"></i>
                                                    </span>
                                                    <input type="password" name="password" id="password"
                                                        class="form-control" placeholder="Masukkan password baru">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="togglePassword()">
                                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Minimal 8 karakter</div>
                                            </div>

                                            <div class="mb-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="rememberUsername">
                                                    <label class="form-check-label" for="rememberUsername">
                                                        Ingat username saya
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="index.php" class="btn btn-outline-secondary">
                                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                                </a>
                                                <button type="submit" name="update" class="btn btn-primary">
                                                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php require_once('template/footer.php'); ?>
        </div>
    </div>

    <?php require_once('template/js.php'); ?>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("toggleIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }

        // Remember username feature
        document.addEventListener("DOMContentLoaded", function () {
            const checkbox = document.getElementById("rememberUsername");
            const usernameInput = document.getElementById("username");

            // Load saved username if exists
            if (localStorage.getItem("rememberedUsername")) {
                usernameInput.value = localStorage.getItem("rememberedUsername");
                checkbox.checked = true;
            }

            // Update storage when checkbox changes
            checkbox.addEventListener("change", function () {
                if (this.checked) {
                    localStorage.setItem("rememberedUsername", usernameInput.value);
                } else {
                    localStorage.removeItem("rememberedUsername");
                }
            });

            // Update storage when username changes
            usernameInput.addEventListener("input", function () {
                if (checkbox.checked) {
                    localStorage.setItem("rememberedUsername", usernameInput.value);
                }
            });

            // Form validation
            const form = document.getElementById('profilForm');
            form.addEventListener('submit', function (e) {
                const password = document.getElementById('password').value;

                if (password.length > 0 && password.length < 8) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Password terlalu pendek',
                        text: 'Password harus minimal 8 karakter',
                    });
                }
            });
        });
    </script>
</body>

</html>