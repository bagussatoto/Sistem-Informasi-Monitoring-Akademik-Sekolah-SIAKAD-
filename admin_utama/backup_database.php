<?php
/**
 * ============================================================
 * Halaman Backup Database 1-Klik — SIAKAD (Portal Admin Utama)
 * Sistem Informasi Monitoring Akademik Sekolah
 * ------------------------------------------------------------
 * Penulis   : Bagus Budi Satoto (Web Developer)
 * Email     : bagusbudi1308@gmail.com
 * ============================================================
 */

include_once("proses/koneksi.php");
session_start();

// ── Proteksi sesi ─────────────────────────────────────────
$nama_login = isset($_SESSION['nama_login']) ? $_SESSION['nama_login'] : '';
if (empty($nama_login)) {
    header("location:../");
    exit;
}

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'sismo_smkit';

// ── Download handler ───────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'download') {

    $timestamp = date('Ymd_His');
    $filename  = 'siakad_backup_' . $timestamp . '.sql';

    // Metode 1: mysqldump via exec()
    $exec_available = function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));
    if ($exec_available) {
        $dump_cmd = "mysqldump --user=$db_user --host=$db_host --databases $db_name";
        if (!empty($db_pass)) {
            $dump_cmd = "mysqldump --user=$db_user --password=$db_pass --host=$db_host --databases $db_name";
        }
        $output = []; $rc = 0;
        exec($dump_cmd . ' 2>&1', $output, $rc);
        if ($rc === 0 && !empty($output)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache'); header('Expires: 0');
            echo implode("\n", $output);
            exit;
        }
    }

    // Metode 2: Pure-PHP fallback
    ob_start();
    echo "-- ============================================================\n";
    echo "-- SIAKAD Database Backup (Portal Admin Utama)\n";
    echo "-- Database : $db_name\n";
    echo "-- Host     : $db_host\n";
    echo "-- Tanggal  : " . date('Y-m-d H:i:s') . "\n";
    echo "-- Penulis  : Bagus Budi Satoto | bagusbudi1308@gmail.com\n";
    echo "-- ============================================================\n\n";
    echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\nSET time_zone = \"+07:00\";\n\n";

    $tables_result = mysqli_query($conn, "SHOW TABLES");
    $tables = [];
    while ($row = mysqli_fetch_row($tables_result)) { $tables[] = $row[0]; }

    foreach ($tables as $table) {
        echo "\n-- --------------------------------------------------------\n";
        echo "-- Tabel: `$table`\n-- --------------------------------------------------------\n\n";
        echo "DROP TABLE IF EXISTS `$table`;\n";
        $cr = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
        $cr_row = mysqli_fetch_row($cr);
        echo $cr_row[1] . ";\n\n";

        $data_result = mysqli_query($conn, "SELECT * FROM `$table`");
        $num_rows = mysqli_num_rows($data_result);
        $num_fields = mysqli_num_fields($data_result);
        if ($num_rows > 0) {
            echo "-- Data tabel `$table`\n\n";
            $batch = [];
            while ($dr = mysqli_fetch_row($data_result)) {
                $vals = [];
                for ($i = 0; $i < $num_fields; $i++) {
                    $vals[] = $dr[$i] === null ? 'NULL' : "'" . mysqli_real_escape_string($conn, $dr[$i]) . "'";
                }
                $batch[] = '(' . implode(', ', $vals) . ')';
                if (count($batch) >= 100) {
                    echo "INSERT INTO `$table` VALUES\n" . implode(",\n", $batch) . ";\n";
                    $batch = [];
                }
            }
            if (!empty($batch)) { echo "INSERT INTO `$table` VALUES\n" . implode(",\n", $batch) . ";\n"; }
            echo "\n";
        }
    }
    echo "\n-- Backup selesai: " . date('Y-m-d H:i:s') . "\n";
    $sql_content = ob_get_clean();

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($sql_content));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache'); header('Expires: 0');
    echo $sql_content;
    exit;
}

// ── Statistik ──────────────────────────────────────────────
$db_size_result = mysqli_query($conn,
    "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
     FROM information_schema.tables WHERE table_schema = '$db_name'"
);
$db_size_mb = mysqli_fetch_assoc($db_size_result)['size_mb'] ?? '0.00';
$tables_result = mysqli_query($conn, "SHOW TABLES");
$total_tables = mysqli_num_rows($tables_result);
$total_rows = 0;
while ($tbl = mysqli_fetch_row($tables_result)) {
    $cnt = mysqli_query($conn, "SELECT COUNT(*) as c FROM `{$tbl[0]}`");
    $r = mysqli_fetch_assoc($cnt);
    $total_rows += (int)$r['c'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Backup Database — SIAKAD Admin Utama</title>
  <link rel="stylesheet" href="asset/css/bootstrap.min.css">
  <link rel="stylesheet" href="asset/css/plugins/font-awesome.min.css"/>
  <link rel="stylesheet" href="asset/css/plugins/animate.min.css"/>
  <link href="asset/css/style.css" rel="stylesheet">
  <link rel="shortcut icon" href="asset/img/logomi.png">
  <style>
    .backup-card { border-radius:12px; padding:32px; background:#fff; box-shadow:0 4px 24px rgba(0,0,0,0.08); margin-top:30px; }
    .stat-box { background:#f7f9fc; border-radius:8px; padding:20px; text-align:center; margin-bottom:15px; }
    .stat-box .stat-num { font-size:2.2em; font-weight:700; color:#2980b9; }
    .stat-box .stat-label { font-size:.95em; color:#888; margin-top:4px; }
    .btn-backup { background:linear-gradient(135deg,#27ae60,#2ecc71); color:#fff; border:none; border-radius:8px; padding:16px 36px; font-size:1.15em; font-weight:600; letter-spacing:.5px; transition:transform .15s,box-shadow .15s; }
    .btn-backup:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(39,174,96,.35); color:#fff; }
    .info-box { background:#eaf6fb; border-left:4px solid #2980b9; border-radius:6px; padding:14px 18px; margin-top:20px; font-size:.93em; color:#2c3e50; }
    .warn-box { background:#fef9e7; border-left:4px solid #f39c12; border-radius:6px; padding:14px 18px; margin-top:12px; font-size:.93em; color:#7d6608; }
  </style>
</head>
<body id="mimin" class="dashboard">
  <nav class="navbar navbar-default header navbar-fixed-top">
    <div class="col-md-12 nav-wrapper">
      <div class="navbar-header" style="width:100%;">
        <div class="opener-left-menu is-open">
          <span class="top"></span><span class="middle"></span><span class="bottom"></span>
        </div>
        <a href="index.php" class="navbar-brand"><b>SIAKAD — ADMINISTRATOR UTAMA</b></a>
        <ul class="nav navbar-nav navbar-right user-nav" style="margin-right:5%;">
          <li class="user-name"><span><?php echo htmlspecialchars($nama_login); ?></span></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid mimin-wrapper">
    <div id="left-menu">
      <div class="sub-left-menu scroll">
        <ul class="nav nav-list">
          <li><div class="left-bg"></div></li>
          <li class="time">
            <h1 class="animated fadeInLeft" id="jam">--:--</h1>
            <p class="animated fadeInRight" id="tgl"></p>
          </li>
          <li class="ripple"><a href="index.php"><span class="fa-home fa"></span> Dashboard</a></li>
          <li class="active ripple"><a href="backup_database.php"><span class="fa fa-database"></span> Backup Database</a></li>
          <li><a href="tentang.php">Tentang Aplikasi</a></li>
        </ul>
      </div>
    </div>

    <div id="content">
      <div class="col-md-12">
        <ol class="breadcrumb animated fadeInDown">
          <li><a href="index.php"><span class="fa fa-home"></span> Dashboard</a></li>
          <li class="active"><span class="fa fa-database"></span> Backup Database</li>
        </ol>

        <div class="backup-card animated fadeInUp">
          <h3 style="font-weight:700; color:#2c3e50; margin-bottom:6px;">
            <span class="fa fa-database" style="color:#27ae60;"></span>
            Backup Database SIAKAD (Semua Instansi)
          </h3>
          <p style="color:#888; margin-bottom:24px;">
            Ekspor seluruh data semua sekolah ke satu file <code>.sql</code> cadangan.
          </p>

          <div class="row">
            <div class="col-md-4"><div class="stat-box">
              <div class="stat-num"><?php echo $total_tables; ?></div>
              <div class="stat-label"><span class="fa fa-table"></span> Total Tabel</div>
            </div></div>
            <div class="col-md-4"><div class="stat-box">
              <div class="stat-num"><?php echo number_format($total_rows); ?></div>
              <div class="stat-label"><span class="fa fa-list"></span> Total Baris Data</div>
            </div></div>
            <div class="col-md-4"><div class="stat-box">
              <div class="stat-num"><?php echo $db_size_mb; ?> MB</div>
              <div class="stat-label"><span class="fa fa-hdd-o"></span> Ukuran Database</div>
            </div></div>
          </div>

          <div class="text-center" style="margin:30px 0;">
            <a href="backup_database.php?action=download" class="btn btn-backup">
              <span class="fa fa-download"></span>&nbsp;&nbsp;
              Unduh Backup Database Sekarang (.sql)
            </a>
            <p style="color:#aaa; font-size:.88em; margin-top:12px;">
              Nama file: <code>siakad_backup_<?php echo date('Ymd_His'); ?>.sql</code>
            </p>
          </div>

          <div class="info-box">
            <strong><span class="fa fa-info-circle"></span> Informasi:</strong>
            File berisi seluruh tabel dan data dari database <strong><?php echo $db_name; ?></strong>.
            Dapat diimpor ulang via phpMyAdmin atau MySQL CLI untuk pemulihan penuh.
          </div>
          <div class="warn-box">
            <strong><span class="fa fa-exclamation-triangle"></span> Rekomendasi:</strong>
            Lakukan backup minimal <strong>1× seminggu</strong> atau setiap hari di akhir operasional.
          </div>

          <div style="margin-top:24px; color:#bbb; font-size:.85em; text-align:right;">
            <span class="fa fa-clock-o"></span>
            Waktu server: <strong><?php echo date('d/m/Y H:i:s'); ?></strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="asset/js/jquery.min.js"></script>
  <script src="asset/js/bootstrap.min.js"></script>
  <script src="asset/js/main.js"></script>
  <script>
    function updateJam() {
      var now = new Date();
      var hh = String(now.getHours()).padStart(2,'0');
      var mm = String(now.getMinutes()).padStart(2,'0');
      document.getElementById('jam').textContent = hh + ':' + mm;
      var days=['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
      var months=['January','February','March','April','May','June','July','August','September','October','November','December'];
      document.getElementById('tgl').textContent = days[now.getDay()]+', '+months[now.getMonth()]+' '+now.getDate()+'th '+now.getFullYear();
    }
    updateJam(); setInterval(updateJam, 1000);
  </script>
</body>
</html>
