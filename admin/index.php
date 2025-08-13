<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("location:../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Dashboard Parkir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <?php require_once('template/css.php'); ?>
    <style>
    /* Dashboard enhancements */
    .page-hero{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;border-radius:16px;padding:24px 28px;box-shadow:0 10px 25px rgba(0,0,0,.08);position:relative;overflow:hidden}
    .page-hero .title{font-weight:700;letter-spacing:.3px}
    .page-hero .breadcrumb{--bs-breadcrumb-divider: '•';}
    .page-hero .breadcrumb .breadcrumb-item,
    .page-hero .breadcrumb .breadcrumb-item a{color:rgba(255,255,255,.9)}
    .dashboard-card{border-radius:14px;border:0;box-shadow:0 1px 2px rgba(16,24,40,.04),0 4px 12px rgba(16,24,40,.06);transition:.25s ease;background:#fff}
    .dashboard-card:hover{transform:translateY(-4px);box-shadow:0 10px 25px rgba(67,97,238,.15)}
    .progress{background:#eef2f7;border-radius:999px;overflow:hidden}
    .progress-bar{background:linear-gradient(90deg,#667eea,#764ba2)}
    .progress-bar.occupancy-low{background:linear-gradient(90deg,#10b981,#34d399)}
    .progress-bar.occupancy-medium{background:linear-gradient(90deg,#fbbf24,#f59e0b)}
    .progress-bar.occupancy-high{background:linear-gradient(90deg,#ef4444,#dc2626)}
    .badge-chip{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;background:#f3f4f6;border:1px solid #e5e7eb;font-weight:500}
    .badge-dot{width:8px;height:8px;border-radius:999px;background:#22c55e;display:inline-block}
    .stat-mini .icon{width:46px;height:46px;display:inline-flex;align-items:center;justify-content:center;border-radius:12px;background:rgba(102,126,234,.1);color:#667eea}
    .stat-mini .value{font-size:1.25rem;font-weight:700}
    @media (max-width: 767.98px){.page-hero{padding:18px 20px}.page-hero .title{font-size:1.25rem}}
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php require_once('template/sidebar.php'); ?>

    <div class="main-content">
        <?php require_once('template/nav.php'); ?>

        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="page-hero">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                <div>
                                    <h4 class="title mb-1"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-0">
                                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                        </ol>
                                    </nav>
                                </div>
                                <div class="d-none d-md-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-car-front-fill"></i>
                                        <span>Sistem Parkir Aktif</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                include "../config/config.php";

                if ($_SESSION['role'] === 'admin') {
                    $sql = "SELECT username FROM user WHERE is_active = 1 AND role = 'petugas'";
                    $result = mysqli_query($koneksi, $sql);

                    echo "<div class='card dashboard-card mb-4'><div class='card-body'>";
                    echo "<h6 class='mb-3 fw-semibold'><i class='bi bi-people-fill text-primary me-2'></i>Petugas Aktif</h6>";
                    echo "<div class='d-flex flex-wrap gap-2'>";
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<span class='badge-chip'><span class=\"badge-dot\"></span>{$row['username']}</span>";
                        }
                    } else {
                        echo "<span class='text-muted'>Tidak ada petugas aktif</span>";
                    }
                    echo "</div></div></div>";
                }
                ?>

                <div class="card dashboard-card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="fw-semibold">Selamat datang, <span class="text-primary"><?= $_SESSION['username']; ?></span>!</h5>
                                <p class="text-muted mb-0">Sistem parkir sudah aktif. Gunakan menu sidebar untuk navigasi fitur.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-primary-light rounded p-4 d-inline-block">
                                    <i class="bi bi-car-front-fill fs-1 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $totalKapasitas = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(kapasitas_slot),0) as sum FROM jenisKendaraan"))['sum'];
                $totalTerparkirAll = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kendaraan_masuk"))['total'];
                $sisaAll = max(0, $totalKapasitas - $totalTerparkirAll);
                $okupansiAll = $totalKapasitas > 0 ? ($totalTerparkirAll / $totalKapasitas) * 100 : 0;
                ?>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card dashboard-card stat-mini h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon"><i class="bi bi-hdd-stack-fill"></i></div>
                                <div>
                                    <div class="text-muted small">Total Kapasitas</div>
                                    <div class="value"><?= number_format($totalKapasitas) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card stat-mini h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon"><i class="bi bi-car-front-fill"></i></div>
                                <div>
                                    <div class="text-muted small">Terparkir</div>
                                    <div class="value"><?= number_format($totalTerparkirAll) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card stat-mini h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon"><i class="bi bi-check2-circle"></i></div>
                                <div>
                                    <div class="text-muted small">Sisa Slot</div>
                                    <div class="value"><?= number_format($sisaAll) ?></div>
                                    <div class="progress mt-2" style="height:6px;">
                                        <div class="progress-bar <?= $okupansiAll >= 80 ? 'occupancy-high' : ($okupansiAll >= 50 ? 'occupancy-medium' : 'occupancy-low') ?>" role="progressbar" style="width: <?= $okupansiAll ?>%" aria-valuenow="<?= $okupansiAll ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
// Data grafik harian (30 hari terakhir) untuk Masuk dan Keluar
$days = 30;
$dailyMasukMap = [];
$dailyKeluarMap = [];
$today = new DateTime('today');
for ($i = $days - 1; $i >= 0; $i--) {
    $d = clone $today;
    $d->modify("-{$i} days");
    $key = $d->format('Y-m-d');
    $dailyMasukMap[$key] = 0;
    $dailyKeluarMap[$key] = 0;
}
// Masuk per hari
$qDailyMasuk = mysqli_query($koneksi, "SELECT DATE(waktu_masuk) AS d, COUNT(*) AS total FROM kendaraan_masuk WHERE DATE(waktu_masuk) >= DATE_SUB(CURDATE(), INTERVAL " . ($days - 1) . " DAY) GROUP BY DATE(waktu_masuk) ORDER BY d ASC");
while ($row = mysqli_fetch_assoc($qDailyMasuk)) {
    $dailyMasukMap[$row['d']] = (int)$row['total'];
}
// Keluar per hari
$qDailyKeluar = mysqli_query($koneksi, "SELECT DATE(waktu_keluar) AS d, COUNT(*) AS total FROM riwayat_keluar WHERE DATE(waktu_keluar) >= DATE_SUB(CURDATE(), INTERVAL " . ($days - 1) . " DAY) GROUP BY DATE(waktu_keluar) ORDER BY d ASC");
while ($row = mysqli_fetch_assoc($qDailyKeluar)) {
    $dailyKeluarMap[$row['d']] = (int)$row['total'];
}
$chartDailyLabels = [];
$chartDailyMasukValues = [];
$chartDailyKeluarValues = [];
foreach ($dailyMasukMap as $dateKey => $val) {
    $chartDailyLabels[] = date('d M', strtotime($dateKey));
    $chartDailyMasukValues[] = $val;
    $chartDailyKeluarValues[] = $dailyKeluarMap[$dateKey] ?? 0;
}

// Data grafik bulanan (tahun berjalan) untuk Masuk dan Keluar
$monthsMasuk = [];
$monthsKeluar = [];
$year = date('Y');
for ($m = 1; $m <= 12; $m++) {
    $key = $year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
    $monthsMasuk[$key] = 0;
    $monthsKeluar[$key] = 0;
}
$qMonthlyMasuk = mysqli_query($koneksi, "SELECT DATE_FORMAT(waktu_masuk, '%Y-%m') AS ym, COUNT(*) AS total FROM kendaraan_masuk WHERE YEAR(waktu_masuk) = YEAR(CURDATE()) GROUP BY ym ORDER BY ym ASC");
while ($row = mysqli_fetch_assoc($qMonthlyMasuk)) {
    $monthsMasuk[$row['ym']] = (int)$row['total'];
}
$qMonthlyKeluar = mysqli_query($koneksi, "SELECT DATE_FORMAT(waktu_keluar, '%Y-%m') AS ym, COUNT(*) AS total FROM riwayat_keluar WHERE YEAR(waktu_keluar) = YEAR(CURDATE()) GROUP BY ym ORDER BY ym ASC");
while ($row = mysqli_fetch_assoc($qMonthlyKeluar)) {
    $monthsKeluar[$row['ym']] = (int)$row['total'];
}
$chartMonthlyLabels = [];
$chartMonthlyMasukValues = [];
$chartMonthlyKeluarValues = [];
foreach ($monthsMasuk as $ym => $val) {
    $chartMonthlyLabels[] = date('M', strtotime($ym.'-01'));
    $chartMonthlyMasukValues[] = $val;
    $chartMonthlyKeluarValues[] = $monthsKeluar[$ym] ?? 0;
}
?>

<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="card dashboard-card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Grafik Kedatangan vs Keluar Harian (30 hari)</h6>
        </div>
        <div style="height:300px"><canvas id="chartDaily"></canvas></div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card dashboard-card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Grafik Kedatangan vs Keluar Bulanan (<?= $year ?>)</h6>
        </div>
        <div style="height:300px"><canvas id="chartMonthly"></canvas></div>
      </div>
    </div>
  </div>
</div>

                <div class="row g-4">
                    <?php
                    $qJenis = mysqli_query($koneksi, "SELECT * FROM jenisKendaraan");
                    while ($j = mysqli_fetch_assoc($qJenis)) {
                        $id = $j['id_jenisKendaraan'];
                        $kapasitas = $j['kapasitas_slot'];
                        $terparkir = mysqli_fetch_assoc(
                            mysqli_query($koneksi, "SELECT COUNT(*) as total 
                                                    FROM kendaraan_masuk 
                                                    WHERE id_jenisKendaraan = '$id'")
                        )['total'];
                        $persentase = $kapasitas > 0 ? ($terparkir / $kapasitas) * 100 : 0;
                        $kelas = 'low';
                        if ($persentase >= 80) { $kelas = 'high'; }
                        elseif ($persentase >= 50) { $kelas = 'medium'; }

                        echo '<div class="col-md-4">
                                <div class="card dashboard-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 fw-semibold">'.$j['jenis_kendaraan'].'</h6>
                                            <span class="badge bg-primary-light text-primary rounded-pill">'
                                                .$terparkir.'/'.$kapasitas.
                                            '</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 8px;">
                                            <div class="progress-bar occupancy-' . $kelas . '" role="progressbar" style="width: ' 
                                                . $persentase . '%" aria-valuenow="' . $persentase . '" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between small">
                                            <span class="text-muted">Terisi</span>
                                            <span class="text-primary fw-semibold">'.($kapasitas - $terparkir).' Slot Tersedia</span>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php require_once('template/footer.php'); ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2"></script>
<?php require_once('template/js.php'); ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const dailyLabels = <?= json_encode($chartDailyLabels) ?>;
  const dailyMasukData = <?= json_encode($chartDailyMasukValues) ?>;
  const dailyKeluarData = <?= json_encode($chartDailyKeluarValues) ?>;
  const monthlyLabels = <?= json_encode($chartMonthlyLabels) ?>;
  const monthlyMasukData = <?= json_encode($chartMonthlyMasukValues) ?>;
  const monthlyKeluarData = <?= json_encode($chartMonthlyKeluarValues) ?>;

  function makeGradient(ctx, colorStart, colorEnd) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, colorStart);
    gradient.addColorStop(1, colorEnd);
    return gradient;
  }

  // Daily line chart
  const dailyCanvas = document.getElementById('chartDaily');
  if (dailyCanvas) {
    const ctxDaily = dailyCanvas.getContext('2d');
    const gradMasuk = makeGradient(ctxDaily, 'rgba(102,126,234,0.4)', 'rgba(118,75,162,0.05)');
    const gradKeluar = makeGradient(ctxDaily, 'rgba(239,68,68,0.35)', 'rgba(244,63,94,0.05)');
    new Chart(ctxDaily, {
      type: 'line',
      data: {
        labels: dailyLabels,
        datasets: [
          {
            label: 'Masuk',
            data: dailyMasukData,
            borderColor: '#667eea',
            backgroundColor: gradMasuk,
            fill: true,
            tension: 0.35,
            pointRadius: 2.5,
            pointHoverRadius: 4,
            pointBackgroundColor: '#667eea',
            pointBorderWidth: 0
          },
          {
            label: 'Keluar',
            data: dailyKeluarData,
            borderColor: '#ef4444',
            backgroundColor: gradKeluar,
            fill: true,
            tension: 0.35,
            pointRadius: 2.5,
            pointHoverRadius: 4,
            pointBackgroundColor: '#ef4444',
            pointBorderWidth: 0
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        scales: {
          x: { ticks: { maxTicksLimit: 10, color: '#6b7280' }, grid: { display: false } },
          y: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.05)' } }
        },
        plugins: {
          legend: { display: true, position: 'top' },
          tooltip: { enabled: true }
        }
      }
    });
  }

  // Monthly bar chart
  const monthlyCanvas = document.getElementById('chartMonthly');
  if (monthlyCanvas) {
    const ctxMonthly = monthlyCanvas.getContext('2d');
    const gradBarMasuk = makeGradient(ctxMonthly, 'rgba(102,126,234,0.8)', 'rgba(118,75,162,0.4)');
    const gradBarKeluar = makeGradient(ctxMonthly, 'rgba(239,68,68,0.8)', 'rgba(244,63,94,0.4)');
    new Chart(ctxMonthly, {
      type: 'bar',
      data: {
        labels: monthlyLabels,
        datasets: [
          {
            label: 'Masuk',
            data: monthlyMasukData,
            backgroundColor: gradBarMasuk,
            borderRadius: 8,
            borderSkipped: false
          },
          {
            label: 'Keluar',
            data: monthlyKeluarData,
            backgroundColor: gradBarKeluar,
            borderRadius: 8,
            borderSkipped: false
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { ticks: { color: '#6b7280' }, grid: { display: false } },
          y: { beginAtZero: true, ticks: { precision: 0, color: '#6b7280' }, grid: { color: 'rgba(0,0,0,0.05)' } }
        },
        plugins: {
          legend: { display: true, position: 'top' },
          tooltip: { enabled: true }
        }
      }
    });
  }
});
</script>
</body>
</html>
