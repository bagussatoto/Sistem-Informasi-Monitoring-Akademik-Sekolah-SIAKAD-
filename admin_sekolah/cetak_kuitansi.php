<?php 
session_start();
if(!isset($_SESSION['npsn']) || empty($_SESSION['npsn'])){
    header("location:../");
    exit();
}

include_once("proses/koneksi.php");

$id_pembayaran = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';
$npsn = $_SESSION['npsn'];

// Fungsi Terbilang Rupiah Otomatis
function penyebut($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " ". $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10). " Belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai/10)." Puluh". penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " Seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai/100) . " Ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " Seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai/1000) . " Ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai/1000000) . " Juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai/1000000000) . " Milyar" . penyebut(fmod($nilai,1000000000));
    }
    return $temp;
}

function terbilang($nilai) {
    if($nilai < 0) {
        $hasil = "Minus ". trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }     
    return $hasil . " Rupiah";
}

// Query Data Pembayaran lengkap
$sql = "SELECT p.*, s.nama_siswa, s.nipd, s.nisn, k.nama_kelas, j.nama_jurusan, 
               t.nama_tagihan, t.nominal_tagihan, sek.nama_sekolah, sek.jalan, sek.kota, sek.provinsi, sek.telefon, sek.email
        FROM tb_pembayaran p
        JOIN tb_siswa s ON p.nipd = s.nipd
        LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas
        LEFT JOIN tb_jurusan j ON s.id_jurusan = j.id_jurusan
        JOIN tb_tagihan t ON p.id_tagihan = t.id_tagihan
        JOIN tb_sekolah sek ON p.npsn = sek.npsn
        WHERE p.id_pembayaran = '$id_pembayaran' AND p.npsn = '$npsn'";

$res = $conn->query($sql);
if(!$res || $res->num_rows == 0){
    die("<div style='text-align:center; padding:50px; font-family:Arial;'><h3>Data Pembayaran Tidak Ditemukan!</h3><a href='data_administrasi.php'>Kembali</a></div>");
}

$row = $res->fetch_assoc();
$nominal_bayar = isset($row['nominal_pembayaran']) ? $row['nominal_pembayaran'] : (isset($row['jumlah_bayar']) ? $row['jumlah_bayar'] : 0);
$terbilang_str = terbilang($nominal_bayar);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Kuitansi_Pembayaran_<?php echo htmlspecialchars($row['nipd']); ?>_<?php echo htmlspecialchars($row['id_pembayaran']); ?></title>
  <style>
    @page {
      size: A5 landscape;
      margin: 10mm;
    }
    @media print {
      body {
        background-color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .no-print {
        display: none !important;
      }
      .kuitansi-box {
        border: 2px solid #222222 !important;
        box-shadow: none !important;
      }
    }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #e2e8f0;
      color: #1a202c;
      margin: 0;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .action-bar {
      width: 100%;
      max-width: 800px;
      margin-bottom: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .btn {
      padding: 8px 18px;
      border-radius: 4px;
      font-size: 10pt;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      border: none;
      transition: all 0.2s;
    }
    .btn-print {
      background-color: #2b6cb0;
      color: #ffffff;
    }
    .btn-print:hover {
      background-color: #2c5282;
    }
    .btn-back {
      background-color: #718096;
      color: #ffffff;
    }
    .btn-back:hover {
      background-color: #4a5568;
    }
    .kuitansi-box {
      width: 100%;
      max-width: 800px;
      background: #ffffff;
      border: 3px double #2d3748;
      border-radius: 6px;
      padding: 24px 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      box-sizing: border-box;
      position: relative;
    }
    .kuitansi-header {
      display: flex;
      align-items: center;
      border-bottom: 2px solid #1a365d;
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .logo-img {
      width: 65px;
      height: 65px;
      object-fit: contain;
      margin-right: 18px;
    }
    .header-text h2 {
      margin: 0;
      font-size: 14pt;
      color: #1a365d;
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    .header-text p {
      margin: 2px 0 0 0;
      font-size: 8.5pt;
      color: #4a5568;
    }
    .title-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-bottom: 16px;
    }
    .kuitansi-title {
      font-size: 13pt;
      font-weight: 700;
      text-decoration: underline;
      color: #2b6cb0;
      letter-spacing: 1px;
    }
    .nomor-kuitansi {
      font-size: 9pt;
      color: #4a5568;
      font-weight: 600;
    }
    table.content-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9.5pt;
      margin-bottom: 16px;
    }
    table.content-table td {
      padding: 5px 4px;
      vertical-align: top;
    }
    .col-label {
      width: 170px;
      font-weight: 600;
      color: #2d3748;
    }
    .col-colon {
      width: 15px;
      text-align: center;
      font-weight: bold;
    }
    .col-value {
      color: #1a202c;
    }
    .terbilang-badge {
      background-color: #f7fafc;
      border: 1px dashed #cbd5e0;
      padding: 6px 10px;
      border-radius: 4px;
      font-style: italic;
      font-weight: 600;
      color: #2b6cb0;
      display: block;
    }
    .status-badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 8.5pt;
      font-weight: bold;
      background-color: #c6f6d5;
      color: #22543d;
    }
    .footer-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-top: 15px;
      padding-top: 10px;
    }
    .nominal-display {
      border: 2px solid #2b6cb0;
      background-color: #ebf8ff;
      color: #1a365d;
      font-size: 13pt;
      font-weight: 800;
      padding: 8px 18px;
      border-radius: 6px;
      display: inline-block;
      letter-spacing: 0.5px;
    }
    .signature-container {
      display: flex;
      gap: 40px;
      text-align: center;
      font-size: 9pt;
    }
    .sig-box {
      width: 150px;
    }
    .sig-space {
      height: 50px;
    }
    .sig-name {
      font-weight: bold;
      border-bottom: 1px solid #718096;
      padding-bottom: 2px;
    }
  </style>
</head>
<body>

  <div class="action-bar no-print">
    <a href="data_administrasi.php" class="btn btn-back">⬅ Kembali</a>
    <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Kuitansi / Simpan PDF</button>
  </div>

  <div class="kuitansi-box">
    <div class="kuitansi-header">
      <img src="../asset/img/tutwuri.png" alt="Logo Sekolah" class="logo-img" />
      <div class="header-text">
        <h2><?php echo htmlspecialchars($row['nama_sekolah']); ?></h2>
        <p><?php echo htmlspecialchars($row['jalan'] . ', ' . $row['kota'] . ', ' . $row['provinsi']); ?></p>
        <p>Telp: <?php echo htmlspecialchars($row['telefon']); ?> | Email: <?php echo htmlspecialchars($row['email']); ?> | NPSN: <?php echo htmlspecialchars($row['npsn']); ?></p>
      </div>
    </div>

    <div class="title-row">
      <div class="kuitansi-title">KUITANSI PEMBAYARAN</div>
      <div class="nomor-kuitansi">No: KWT/<?php echo htmlspecialchars($row['id_pembayaran']); ?>/<?php echo date('Y'); ?></div>
    </div>

    <table class="content-table">
      <tr>
        <td class="col-label">Telah Diterima Dari</td>
        <td class="col-colon">:</td>
        <td class="col-value"><strong><?php echo htmlspecialchars($row['nama_siswa']); ?></strong> (NIPD: <?php echo htmlspecialchars($row['nipd']); ?> / NISN: <?php echo htmlspecialchars($row['nisn']); ?>)</td>
      </tr>
      <tr>
        <td class="col-label">Tingkat / Rombel</td>
        <td class="col-colon">:</td>
        <td class="col-value"><?php echo htmlspecialchars($row['nama_kelas']); ?> - <?php echo htmlspecialchars($row['nama_jurusan']); ?></td>
      </tr>
      <tr>
        <td class="col-label">Uang Sejumlah</td>
        <td class="col-colon">:</td>
        <td class="col-value">
          <span class="terbilang-badge"># <?php echo htmlspecialchars($terbilang_str); ?> #</span>
        </td>
      </tr>
      <tr>
        <td class="col-label">Untuk Pembayaran</td>
        <td class="col-colon">:</td>
        <td class="col-value"><?php echo htmlspecialchars($row['nama_tagihan']); ?></td>
      </tr>
      <tr>
        <td class="col-label">Tanggal Transaksi</td>
        <td class="col-colon">:</td>
        <td class="col-value"><?php echo htmlspecialchars($row['tanggal_pembayaran']); ?></td>
      </tr>
      <tr>
        <td class="col-label">Status Pelunasan</td>
        <td class="col-colon">:</td>
        <td class="col-value">
          <span class="status-badge"><?php echo htmlspecialchars(isset($row['keterangan_bayar']) && !empty($row['keterangan_bayar']) ? $row['keterangan_bayar'] : 'Lunas'); ?></span>
        </td>
      </tr>
    </table>

    <div class="footer-row">
      <div>
        <div style="font-size: 8.5pt; color: #718096; margin-bottom: 4px;">Jumlah Terbayar:</div>
        <div class="nominal-display">Rp <?php echo number_format($nominal_bayar, 0, ',', '.'); ?>,-</div>
      </div>

      <div class="signature-container">
        <div class="sig-box">
          <div>Penyetor / Siswa,</div>
          <div class="sig-space"></div>
          <div class="sig-name"><?php echo htmlspecialchars($row['nama_siswa']); ?></div>
        </div>
        <div class="sig-box">
          <div><?php echo htmlspecialchars($row['kota']); ?>, <?php echo date('d/m/Y'); ?></div>
          <div style="margin-top:2px;">Bendahara / Petugas TU,</div>
          <div class="sig-space"></div>
          <div class="sig-name"><?php echo isset($_SESSION['nama_login']) ? htmlspecialchars($_SESSION['nama_login']) : 'Staff Tata Usaha'; ?></div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
