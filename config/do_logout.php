<?php
session_start();
include "config.php";

if (isset($_SESSION['id_user'])) {
    $id = $_SESSION['id_user'];

    // Tandai user sebagai tidak aktif
    $stmt = mysqli_prepare($koneksi, "UPDATE user SET is_active = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

$_SESSION = [];
session_unset();
session_destroy();

header("location:../login.php");
exit;
?>
