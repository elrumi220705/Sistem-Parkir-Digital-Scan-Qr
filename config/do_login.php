<?php
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    echo "<script>
        alert('Form login belum lengkap!');
        window.location.href = '../login.php';
    </script>";
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT id, username, password, role, foto_profil FROM user WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row['password'])) {
        // Simpan data session
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['foto_profil'] = $row['foto_profil']; 

        // Tandai user sebagai aktif
        $update_stmt = mysqli_prepare($koneksi, "UPDATE user SET is_active = 1 WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "i", $row['id']);
        mysqli_stmt_execute($update_stmt);

        // Redirect sesuai role
        if ($row['role'] === 'admin') {
            echo "<script>
                alert('Login Berhasil');
                window.location.href = '../admin/index.php';
            </script>";
        } else if ($row['role'] === 'petugas') {
            echo "<script>
                alert('Login Berhasil');
                window.location.href = '../admin/index.php';
            </script>";
        } else {
            // Jika role tidak dikenal
            echo "<script>
                alert('Login gagal! Role tidak valid.');
                window.location.href = '../login.php';
            </script>";
        }

    } else {
        echo "<script>
            alert('Login gagal! Password salah.');
            window.location.href = '../login.php';
        </script>";
    }

} else {
    echo "<script>
        alert('Login gagal! Username tidak ditemukan.');
        window.location.href = '../login.php';
    </script>";
}
?>
