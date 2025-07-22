<?php
// Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../config/config.php';
include_once 'notifikasi_slot.php';
?>

<nav class="topbar navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">
        <button class="sidebar-toggler navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand d-none d-lg-flex align-items-center ms-3" href="index.php">
            <i class="bi bi-p-square-fill text-primary me-2 fs-5"></i>
            <span class="fw-bold">EasyParkir</span>
        </a>

        <div class="ms-auto d-flex align-items-center">
            <!-- Notifikasi -->
            <div class="dropdown me-3">
                <a href="#" class="position-relative text-decoration-none" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5 text-muted"></i>
                    <?php if (!empty($jumlahNotifikasi)): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $jumlahNotifikasi ?>
                        </span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 300px;">
                    <li><h6 class="dropdown-header">Notifikasi Terbaru</h6></li>
                    <?php if (!empty($notifikasiSlot)): ?>
                        <?php foreach ($notifikasiSlot as $notif): ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-start gap-2" href="#">
                                    <i class="bi bi-exclamation-circle text-warning fs-5"></i>
                                    <div>
                                        <div class="fw-semibold"><?= $notif['jenis'] ?> hampir penuh</div>
                                        <div class="small text-muted">Tersisa <?= $notif['tersisa'] ?> slot</div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><span class="dropdown-item text-muted">Tidak ada notifikasi</span></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Foto Profil dan Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="me-2 position-relative">
                        <?php if (!empty($_SESSION['foto_profil']) && file_exists('../uploads/foto_profil/' . $_SESSION['foto_profil'])): ?>
                            <img src="../uploads/foto_profil/<?= $_SESSION['foto_profil'] ?>" class="rounded-circle" alt="Foto Profil" width="35" height="35" style="object-fit: cover;">
                        <?php else: ?>
                            <img src="../assets/default-avatar.png" class="rounded-circle" alt="Foto Default" width="35" height="35" style="object-fit: cover;">
                        <?php endif; ?>
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success rounded-circle border border-2 border-white"></span>
                    </div>

                    <div class="d-none d-lg-block text-start">
                        <span class="fw-semibold d-block"><?= $_SESSION['username'] ?? 'User' ?></span>
                        <small class="text-muted"><?= ucfirst($_SESSION['role'] ?? '-') ?></small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="../admin/profile.php"><i class="bi bi-person me-2"></i> Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../config/do_logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
