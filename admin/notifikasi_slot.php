<?php
// Cek jika koneksi belum tersedia
if (!isset($koneksi)) {
    include '../config/config.php'; // Sesuaikan path jika berbeda
}

$notifikasiSlot = [];

$query = mysqli_query($koneksi, "
    SELECT jk.jenis_kendaraan, jk.kapasitas_slot, COUNT(km.id) AS terparkir
    FROM jenisKendaraan jk
    LEFT JOIN kendaraan_masuk km ON km.id_jenisKendaraan = jk.id_jenisKendaraan
    GROUP BY jk.id_jenisKendaraan
");

while ($row = mysqli_fetch_assoc($query)) {
    $tersisa = $row['kapasitas_slot'] - $row['terparkir'];
    if ($tersisa <= 2) {
        $notifikasiSlot[] = [
            'jenis' => $row['jenis_kendaraan'],
            'tersisa' => $tersisa
        ];
    }
}

$jumlahNotifikasi = count($notifikasiSlot);
