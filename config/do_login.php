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

    // Cek password hash
    if (password_verify($password, $row['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['foto_profil'] = $row['foto_profil']; 

        echo "<script>
            alert('Login Berhasil');
            window.location.href = '../admin/index.php';
        </script>";
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
