<?php
$koneksi = mysqli_connect("localhost", "root", "", "parkir");

if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return number_format($angka, 0, ',', '.');
    }
}
?>
