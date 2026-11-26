<?php
/**
 * ============================================================
 * Halaman Backup Database 1-Klik — SIAKAD
 * Sistem Informasi Monitoring Akademik Sekolah
 * ------------------------------------------------------------
 * Penulis   : Bagus Budi Satoto (Web Developer)
 * Email     : bagusbudi1308@gmail.com
 * ------------------------------------------------------------
 * Fitur:
 *   - Backup seluruh tabel database ke file .sql otomatis
 *   - Nama file: siakad_backup_YYYYMMDD_HHMMSS.sql
 *   - Metode 1: mysqldump via exec() (jika tersedia)
 *   - Metode 2: Pure-PHP fallback (tanpa ekstensi eksternal)
 *   - File diunduh langsung ke browser (tidak disimpan di server)
 *   - Proteksi sesi: hanya Admin Sekolah yang login
 * ============================================================
 */

include_once("proses/koneksi.php");
session_start();

// ── Proteksi sesi ─────────────────────────────────────────
$npsn = isset($_SESSION['npsn']) ? $_SESSION['npsn'] : '';
if (empty($npsn)) {
    header("location:../");
    exit;
}

// ── Konfigurasi koneksi (ambil dari koneksi.php) ──────────
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'sismo_smkit';

// ── Jika tombol download diklik ───────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'download') {

    $timestamp  = date('Ymd_His');
    $filename   = 'siakad_backup_' . $timestamp . '.sql';

    // Coba metode 1: mysqldump via exec()
    $mysqldump_path = 'mysqldump'; // ubah ke path absolut jika perlu, misal: 'C:/xampp/mysql/bin/mysqldump'
    $dump_command   = "$mysqldump_path --user=$db_user --host=$db_host --databases $db_name";
    if (!empty($db_pass)) {
        $dump_command = "$mysqldump_path --user=$db_user --password=$db_pass --host=$db_host --databases $db_name";
    }

    $exec_available = function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))));

    if ($exec_available) {
        $output = [];
        $return_code = 0;
        exec($dump_command . ' 2>&1', $output, $return_code);

        if ($return_code === 0 && !empty($output)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo implode("\n", $output);
            exit;
        }
    }

    // Metode 2: Pure-PHP Backup (Fallback — tidak butuh exec / mysqldump)
    ob_start();

    echo "-- ============================================================\n";
    echo "-- SIAKAD Database Backup\n";
    echo "-- Database : $db_name\n";
    echo "-- Host     : $db_host\n";
    echo "-- Tanggal  : " . date('Y-m-d H:i:s') . "\n";
    echo "-- Generator: SIAKAD Pure-PHP Backup Engine\n";
    echo "-- Penulis  : Bagus Budi Satoto | bagusbudi1308@gmail.com\n";
    echo "-- ============================================================\n\n";
    echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    echo "SET time_zone = \"+07:00\";\n\n";

    // Dapatkan semua tabel
    $tables_result = mysqli_query($conn, "SHOW TABLES");
    if (!$tables_result) {
        ob_end_clean();
        die("Gagal membaca tabel database: " . mysqli_error($conn));
    }

    $tables = [];
    while ($row = mysqli_fetch_row($tables_result)) {
        $tables[] = $row[0];
    }

    foreach ($tables as $table) {
        // ── CREATE TABLE ──────────────────────────────────────
        echo "\n-- --------------------------------------------------------\n";
        echo "-- Struktur tabel `$table`\n";
        echo "-- --------------------------------------------------------\n\n";
        echo "DROP TABLE IF EXISTS `$table`;\n";

        $create_result = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
        $create_row    = mysqli_fetch_row($create_result);
        echo $create_row[1] . ";\n\n";

        // ── INSERT DATA ───────────────────────────────────────
        $data_result = mysqli_query($conn, "SELECT * FROM `$table`");
        $num_rows    = mysqli_num_rows($data_result);
        $num_fields  = mysqli_num_fields($data_result);

        if ($num_rows > 0) {
            echo "-- Data untuk tabel `$table`\n\n";

            $batch_size  = 100; // insert per batch
            $batch_rows  = [];

            while ($data_row = mysqli_fetch_row($data_result)) {
                $values = [];
                for ($i = 0; $i < $num_fields; $i++) {
                    if ($data_row[$i] === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . mysqli_real_escape_string($conn, $data_row[$i]) . "'";
                    }
                }
                $batch_rows[] = '(' . implode(', ', $values) . ')';

                if (count($batch_rows) >= $batch_size) {
                    echo "INSERT INTO `$table` VALUES\n" . implode(",\n", $batch_rows) . ";\n";
                    $batch_rows = [];
                }
            }

            if (!empty($batch_rows)) {
                echo "INSERT INTO `$table` VALUES\n" . implode(",\n", $batch_rows) . ";\n";
            }
            echo "\n";
        }
    }

    echo "\n-- ============================================================\n";
    echo "-- Backup selesai pada: " . date('Y-m-d H:i:s') . "\n";
    echo "-- ============================================================\n";

    $sql_content = ob_get_clean();

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($sql_content));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $sql_content;
    exit;
}

// ── Ambil informasi database untuk tampilan ───────────────
$db_size_result = mysqli_query($conn,
    "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
     FROM information_schema.tables
     WHERE table_schema = '$db_name'"
);
$db_size_row = mysqli_fetch_assoc($db_size_result);
$db_size_mb  = $db_size_row['size_mb'] ?? '0.00';

$tables_result  = mysqli_query($conn, "SHOW TABLES");
$total_tables   = mysqli_num_rows($tables_result);

$total_rows = 0;
while ($tbl = mysqli_fetch_row($tables_result)) {
    $cnt = mysqli_query($conn, "SELECT COUNT(*) as c FROM `{$tbl[0]}`");
    $r = mysqli_fetch_assoc($cnt);
    $total_rows += (int)$r['c'];
}

$profil_result = mysqli_query($conn, "SELECT nama_sekolah, foto FROM tb_sekolah WHERE npsn='$npsn'");
$profil_row    = mysqli_fetch_array($profil_result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Backup Database — SIAKAD</title>
  <link rel="stylesheet" href="asset/css/bootstrap.min.css">
  <link rel="stylesheet" href="asset/css/plugins/font-awesome.min.css"/>
  <link rel="stylesheet" href="asset/css/plugins/animate.min.css"/>
  <link href="asset/css/style.css" rel="stylesheet">
  <link rel="shortcut icon" href="asset/img/logomi.png">
  <style>
    .backup-card {
      border-radius: 12px;
      padding: 32px;
      background: #fff;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      margin-top: 30px;
    }
    .stat-box {
      background: #f7f9fc;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      margin-bottom: 15px;
    }
    .stat-box .stat-num {
      font-size: 2.2em;
      font-weight: 700;
      color: #2980b9;
    }
    .stat-box .stat-label {
      font-size: 0.95em;
      color: #888;
      margin-top: 4px;
    }
    .btn-backup {
      background: linear-gradient(135deg, #27ae60, #2ecc71);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 16px 36px;
      font-size: 1.15em;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: transform .15s, box-shadow .15s;
    }
    .btn-backup:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(39,174,96,0.35);
      color: #fff;
    }
    .info-box {
      background: #eaf6fb;
      border-left: 4px solid #2980b9;
      border-radius: 6px;
      padding: 14px 18px;
      margin-top: 20px;
      font-size: 0.93em;
      color: #2c3e50;
    }
    .warn-box {
      background: #fef9e7;
      border-left: 4px solid #f39c12;
      border-radius: 6px;
      padding: 14px 18px;
      margin-top: 12px;
      font-size: 0.93em;
      color: #7d6608;
    }
    .breadcrumb-wrap { margin-bottom: 0; padding: 10px 0 0; }
  </style>
</head>
<body id="mimin" class="dashboard">

  <!-- Header -->
  <nav class="navbar navbar-default header navbar-fixed-top">
    <div class="col-md-12 nav-wrapper">
      <div class="navbar-header" style="width:100%;">
        <div class="opener-left-menu is-open">
          <span class="top"></span><span class="middle"></span><span class="bottom"></span>
        </div>
        <a href="index.php" class="navbar-brand"><b>SISTEM INFORMASI MONITORING AKADEMIK SISWA</b></a>
        <ul class="nav navbar-nav navbar-right user-nav" style="margin-right:5%;">
          <li class="user-name"><span><?php echo htmlspecialchars($profil_row['nama_sekolah'] ?? ''); ?></span></li>
          <li class="dropdown avatar-dropdown">
            <img src="asset/foto/sekolah/<?php echo htmlspecialchars($profil_row['foto'] ?? ''); ?>" class="img-circle avatar" alt="foto" data-toggle="dropdown"/>
            <ul class="dropdown-menu user-dropdown">
              <li><a href="profile_sekolah.php"><span class="fa fa-user"></span> Profil</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="../proses/logout.php"><span class="fa fa-sign-out"></span> Keluar</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid mimin-wrapper">

    <!-- Sidebar -->
    <div id="left-menu">
      <div class="sub-left-menu scroll">
        <ul class="nav nav-list">
          <li><div class="left-bg"></div></li>
          <li class="time">
            <h1 class="animated fadeInLeft" id="jam">--:--</h1>
            <p class="animated fadeInRight" id="tgl"></p>
          </li>
          <li class="ripple"><a href="index.php"><span class="fa-home fa"></span> Dashboard</a></li>
          <li class="ripple"><a class="tree-toggle nav-header"><span class="fa fa-graduation-cap"></span> Data Siswa <span class="fa-angle-right fa right-arrow text-right"></span></a>
            <ul class="nav nav-list tree"><li><a href="data_siswa.php">Data Induk Siswa</a></li></ul>
          </li>
          <li class="ripple"><a class="tree-toggle nav-header"><span class="fa fa-users"></span> Data Karyawan <span class="fa-angle-right fa right-arrow text-right"></span></a>
            <ul class="nav nav-list tree">
              <li><a href="data_karyawan.php">Data Induk Karyawan</a></li>
              <li><a href="data_guru.php">Data Guru</a></li>
            </ul>
          </li>
          <li class="ripple"><a class="tree-toggle nav-header"><span class="fa fa-pencil"></span> Data Akademik <span class="fa-angle-right fa right-arrow text-right"></span></a>
            <ul class="nav nav-list tree">
              <li><a href="data_pelajaran.php">Data Mata Pelajaran</a></li>
              <li><a href="data_jadwal.php">Data Jadwal</a></li>
              <li><a href="data_kelas.php">Data Jurusan Kelas</a></li>
              <li><a href="data_absensi.php">Data Absensi Siswa</a></li>
              <li><a href="data_nilai_siswa.php">Data Nilai Siswa</a></li>
            </ul>
          </li>
          <li class="ripple"><a class="tree-toggle nav-header"><span class="fa fa-money"></span> Data Administrasi <span class="fa-angle-right fa right-arrow text-right"></span></a>
            <ul class="nav nav-list tree">
              <li><a href="data_tagihan.php">Data Tagihan</a></li>
              <li><a href="data_administrasi.php">Data Pembayaran Siswa</a></li>
            </ul>
          </li>
          <li class="ripple"><a class="tree-toggle nav-header"><span class="fa fa-envelope-o"></span> Pesan <span class="fa-angle-right fa right-arrow text-right"></span></a>
            <ul class="nav nav-list tree">
              <li><a href="data_pesan_masuk.php">Pesan Masuk</a></li>
              <li><a href="data_pesan.php">Data Pesan</a></li>
            </ul>
          </li>
          <li class="active ripple"><a href="backup_database.php"><span class="fa fa-database"></span> Backup Database</a></li>
          <li><a href="tentang.php">Tentang Aplikasi</a></li>
        </ul>
      </div>
    </div>
    <!-- end: Left Menu -->

    <!-- Content -->
    <div id="content">
      <div class="col-md-12">

        <!-- Breadcrumb -->
        <ol class="breadcrumb breadcrumb-wrap animated fadeInDown">
          <li><a href="index.php"><span class="fa fa-home"></span> Dashboard</a></li>
          <li class="active"><span class="fa fa-database"></span> Backup Database</li>
        </ol>

        <div class="backup-card animated fadeInUp">

          <h3 style="font-weight:700; color:#2c3e50; margin-bottom:6px;">
            <span class="fa fa-database" style="color:#27ae60;"></span>
            Backup Database SIAKAD
          </h3>
          <p style="color:#888; margin-bottom:24px;">
            Ekspor seluruh data sistem ke file <code>.sql</code> yang dapat disimpan sebagai cadangan data harian.
          </p>

          <!-- Statistik DB -->
          <div class="row">
            <div class="col-md-4 col-sm-4">
              <div class="stat-box">
                <div class="stat-num"><?php echo $total_tables; ?></div>
                <div class="stat-label"><span class="fa fa-table"></span> Total Tabel</div>
              </div>
            </div>
            <div class="col-md-4 col-sm-4">
              <div class="stat-box">
                <div class="stat-num"><?php echo number_format($total_rows); ?></div>
                <div class="stat-label"><span class="fa fa-list"></span> Total Baris Data</div>
              </div>
            </div>
            <div class="col-md-4 col-sm-4">
              <div class="stat-box">
                <div class="stat-num"><?php echo $db_size_mb; ?> MB</div>
                <div class="stat-label"><span class="fa fa-hdd-o"></span> Ukuran Database</div>
              </div>
            </div>
          </div>

          <!-- Tombol Backup -->
          <div class="text-center" style="margin: 30px 0;">
            <a href="backup_database.php?action=download" class="btn btn-backup">
              <span class="fa fa-download"></span>&nbsp;&nbsp;
              Unduh Backup Database Sekarang (.sql)
            </a>
            <p style="color:#aaa; font-size:0.88em; margin-top:12px;">
              File akan otomatis diunduh ke perangkat Anda.<br>
              Nama file: <code>siakad_backup_<?php echo date('Ymd_His'); ?>.sql</code>
            </p>
          </div>

          <!-- Info -->
          <div class="info-box">
            <strong><span class="fa fa-info-circle"></span> Informasi Backup:</strong><br>
            File <code>.sql</code> yang diunduh berisi seluruh struktur tabel (<code>CREATE TABLE</code>)
            dan data (<code>INSERT INTO</code>) dari database <strong><?php echo $db_name; ?></strong>.
            File ini dapat langsung diimpor kembali melalui <strong>phpMyAdmin</strong> atau
            perintah <code>mysql -u root -p sismo_smkit &lt; nama_file.sql</code> untuk pemulihan data.
          </div>

          <div class="warn-box">
            <strong><span class="fa fa-exclamation-triangle"></span> Rekomendasi Jadwal Backup:</strong><br>
            Lakukan backup secara rutin — minimal <strong>1× seminggu</strong> (atau setiap hari
            di akhir jam operasional sekolah) untuk memastikan data tidak hilang jika terjadi
            kerusakan server, virus, atau kesalahan operasional.
          </div>

          <!-- Riwayat info -->
          <div style="margin-top:24px; color:#bbb; font-size:0.85em; text-align:right;">
            <span class="fa fa-clock-o"></span>
            Waktu server saat ini: <strong><?php echo date('d/m/Y H:i:s'); ?></strong>
          </div>

        </div>
        <!-- end backup-card -->

      </div>
    </div>
    <!-- end: Content -->

  </div>

  <script src="asset/js/jquery.min.js"></script>
  <script src="asset/js/bootstrap.min.js"></script>
  <script src="asset/js/main.js"></script>
  <script>
    // Jam digital di sidebar
    function updateJam() {
      var now = new Date();
      var hh = String(now.getHours()).padStart(2,'0');
      var mm = String(now.getMinutes()).padStart(2,'0');
      document.getElementById('jam').textContent = hh + ':' + mm;
      var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
      var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
      document.getElementById('tgl').textContent =
        days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate() + 'th ' + now.getFullYear();
    }
    updateJam();
    setInterval(updateJam, 1000);
  </script>
</body>
</html>
