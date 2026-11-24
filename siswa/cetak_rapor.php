<?php 
session_start();
if(!isset($_SESSION['npsn']) || empty($_SESSION['npsn'])){
    header("location:../");
    exit();
}

include_once("proses/koneksi.php");

$npsn = $_SESSION['npsn'];
$nipd = isset($_GET['nipd']) ? mysqli_real_escape_string($conn, $_GET['nipd']) : '';
$semester_filter = isset($_GET['semester']) ? mysqli_real_escape_string($conn, $_GET['semester']) : '';

// 1. Ambil Data Sekolah
$sql_sek = "SELECT * FROM tb_sekolah WHERE npsn = '$npsn'";
$res_sek = $conn->query($sql_sek);
$data_sekolah = $res_sek->fetch_assoc();

// 2. Ambil Data Siswa Lengkap
$sql_siswa = "SELECT s.*, k.nama_kelas, j.nama_jurusan 
              FROM tb_siswa s 
              LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas 
              LEFT JOIN tb_jurusan j ON s.id_jurusan = j.id_jurusan 
              WHERE s.nipd = '$nipd' AND s.npsn = '$npsn'";
$res_siswa = $conn->query($sql_siswa);

if(!$res_siswa || $res_siswa->num_rows == 0){
    die("<div style='text-align:center; padding:50px; font-family:Arial;'><h3>Data Siswa dengan NIPD: " . htmlspecialchars($nipd) . " Tidak Ditemukan!</h3><a href='data_nilai_siswa.php'>Kembali ke Data Nilai</a></div>");
}
$siswa = $res_siswa->fetch_assoc();

// 3. Ambil Nilai Akademik Siswa
$sql_nilai = "SELECT n.*, p.nama_pelajaran, p.beban_balajar 
              FROM tb_nilai_akademik n 
              JOIN tb_pelajaran p ON n.kode_mapel = p.kode_mapel 
              WHERE n.nipd = '$nipd' AND n.npsn = '$npsn'";

if(!empty($semester_filter)){
    $sql_nilai .= " AND n.semester = '$semester_filter'";
}
$sql_nilai .= " ORDER BY p.nama_pelajaran ASC";
$res_nilai = $conn->query($sql_nilai);

// 4. Ambil Rekap Absensi Siswa
$sql_absensi = "SELECT keterangan, COUNT(*) as jumlah 
                FROM tb_absensi 
                WHERE nipd = '$nipd' AND npsn = '$npsn' 
                GROUP BY keterangan";
$res_absensi = $conn->query($sql_absensi);
$rekap_absensi = array('H' => 0, 'S' => 0, 'I' => 0, 'A' => 0);
if($res_absensi){
    while($row_abs = $res_absensi->fetch_assoc()){
        $ket = strtoupper(trim($row_abs['keterangan']));
        if($ket == 'M' || $ket == 'H') $rekap_absensi['H'] += $row_abs['jumlah'];
        else if($ket == 'S') $rekap_absensi['S'] += $row_abs['jumlah'];
        else if($ket == 'I') $rekap_absensi['I'] += $row_abs['jumlah'];
        else if($ket == 'A') $rekap_absensi['A'] += $row_abs['jumlah'];
    }
}

// 5. Ambil Data Kepala Sekolah
$sql_kepsek = "SELECT * FROM tb_karyawan WHERE npsn = '$npsn' AND status_kepegawaian = 'Kepala Sekolah' LIMIT 1";
$res_kepsek = $conn->query($sql_kepsek);
$kepsek = ($res_kepsek && $res_kepsek->num_rows > 0) ? $res_kepsek->fetch_assoc() : array('nama_karyawan' => 'Kepala Sekolah', 'nip' => '-');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Rapor_Akademik_<?php echo htmlspecialchars($siswa['nipd']); ?>_<?php echo htmlspecialchars($siswa['nama_siswa']); ?></title>
  <style>
    @page {
      size: A4 portrait;
      margin: 15mm 15mm 15mm 15mm;
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
      .rapor-wrapper {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
      }
    }
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Times New Roman', Times, serif;
      font-size: 10.5pt;
      line-height: 1.4;
      color: #1a202c;
      background-color: #e2e8f0;
      margin: 0;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .action-bar {
      width: 100%;
      max-width: 820px;
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-family: 'Segoe UI', Arial, sans-serif;
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
    .rapor-wrapper {
      width: 100%;
      max-width: 820px;
      background: #ffffff;
      padding: 30px 40px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      box-sizing: border-box;
    }
    .kop-surat {
      display: flex;
      align-items: center;
      border-bottom: 3px double #000000;
      padding-bottom: 12px;
      margin-bottom: 18px;
    }
    .kop-logo {
      width: 75px;
      height: 75px;
      object-fit: contain;
      margin-right: 20px;
    }
    .kop-text {
      text-align: center;
      flex: 1;
    }
    .kop-text h4 {
      margin: 0;
      font-size: 11pt;
      font-weight: normal;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .kop-text h2 {
      margin: 3px 0;
      font-size: 15pt;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .kop-text p {
      margin: 2px 0 0 0;
      font-size: 9pt;
      font-style: italic;
    }
    .rapor-title {
      text-align: center;
      font-size: 13pt;
      font-weight: bold;
      text-transform: uppercase;
      text-decoration: underline;
      margin-bottom: 16px;
      letter-spacing: 0.5px;
    }
    .bio-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      column-gap: 24px;
      row-gap: 4px;
      margin-bottom: 16px;
      font-size: 10pt;
    }
    .bio-row {
      display: flex;
    }
    .bio-label {
      width: 130px;
      font-weight: 500;
    }
    .bio-colon {
      width: 15px;
      font-weight: bold;
    }
    .bio-val {
      flex: 1;
      font-weight: 600;
    }
    table.data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
      font-size: 9.5pt;
    }
    table.data-table th, table.data-table td {
      border: 1px solid #000000;
      padding: 6px 8px;
    }
    table.data-table th {
      background-color: #f2f2f2;
      text-align: center;
      font-weight: bold;
    }
    .text-center {
      text-align: center;
    }
    .text-right {
      text-align: right;
    }
    .rekap-grid {
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      column-gap: 20px;
      margin-bottom: 18px;
      page-break-inside: avoid;
    }
    table.sub-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9pt;
    }
    table.sub-table th, table.sub-table td {
      border: 1px solid #000000;
      padding: 5px 8px;
    }
    table.sub-table th {
      background-color: #f2f2f2;
      text-align: left;
    }
    .catatan-box {
      border: 1px solid #000000;
      padding: 8px 12px;
      min-height: 85px;
      font-size: 9.5pt;
      font-style: italic;
    }
    .catatan-box strong {
      font-style: normal;
      display: block;
      margin-bottom: 4px;
    }
    .sig-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      page-break-inside: avoid;
      font-size: 10pt;
    }
    .sig-table td {
      border: none;
      vertical-align: top;
      text-align: center;
      width: 33.33%;
    }
    .sig-space {
      height: 60px;
    }
    .sig-name {
      font-weight: bold;
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="action-bar no-print">
    <a href="data_nilai_siswa.php" class="btn btn-back">⬅ Kembali</a>
    <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Rapor / Simpan PDF</button>
  </div>

  <div class="rapor-wrapper">
    <div class="kop-surat">
      <img src="../asset/img/tutwuri.png" alt="Logo" class="kop-logo" />
      <div class="kop-text">
        <h4>PEMERINTAH DAERAH PROVINSI <?php echo strtoupper(htmlspecialchars($data_sekolah['provinsi'])); ?></h4>
        <h4>DINAS PENDIDIKAN DAN KEBUDAYAAN</h4>
        <h2><?php echo strtoupper(htmlspecialchars($data_sekolah['nama_sekolah'])); ?></h2>
        <p><?php echo htmlspecialchars($data_sekolah['jalan'] . ', ' . $data_sekolah['kota'] . ' - Kode Pos: ' . $data_sekolah['kode_pos']); ?></p>
        <p>Telepon: <?php echo htmlspecialchars($data_sekolah['telefon']); ?> | Email: <?php echo htmlspecialchars($data_sekolah['email']); ?> | NPSN: <?php echo htmlspecialchars($data_sekolah['npsn']); ?></p>
      </div>
    </div>

    <div class="rapor-title">LAPORAN CAPAIAN HASIL BELAJAR PESERTA DIDIK</div>

    <div class="bio-grid">
      <div>
        <div class="bio-row">
          <div class="bio-label">Nama Peserta Didik</div>
          <div class="bio-colon">:</div>
          <div class="bio-val"><?php echo strtoupper(htmlspecialchars($siswa['nama_siswa'])); ?></div>
        </div>
        <div class="bio-row">
          <div class="bio-label">Nomor Induk (NIPD)</div>
          <div class="bio-colon">:</div>
          <div class="bio-val"><?php echo htmlspecialchars($siswa['nipd']); ?></div>
        </div>
        <div class="bio-row">
          <div class="bio-label">NISN</div>
          <div class="bio-colon">:</div>
          <div class="bio-val"><?php echo htmlspecialchars($siswa['nisn']); ?></div>
        </div>
      </div>
      <div>
        <div class="bio-row">
          <div class="bio-label">Kelas / Rombel</div>
          <div class="bio-colon">:</div>
          <div class="bio-val"><?php echo htmlspecialchars($siswa['nama_kelas']); ?> - <?php echo htmlspecialchars($siswa['nama_jurusan']); ?></div>
        </div>
        <div class="bio-row">
          <div class="bio-label">Semester</div>
          <div class="bio-colon">:</div>
          <div class="bio-val"><?php echo !empty($semester_filter) ? htmlspecialchars($semester_filter) : '1 (Ganjil)'; ?></div>
        </div>
        <div class="bio-row">
          <div class="bio-label">Tahun Pelajaran</div>
          <div class="bio-colon">:</div>
          <div class="bio-val"><?php echo date('Y') . '/' . (date('Y') + 1); ?></div>
        </div>
      </div>
    </div>

    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 5%;">No</th>
          <th style="width: 35%;">Mata Pelajaran</th>
          <th style="width: 10%;">KKM</th>
          <th style="width: 12%;">Nilai Akhir</th>
          <th style="width: 10%;">Predikat</th>
          <th style="width: 28%;">Capaian Kompetensi / Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no = 1;
        $total_nilai = 0;
        $count = 0;
        if($res_nilai && $res_nilai->num_rows > 0){
            while($n = $res_nilai->fetch_assoc()){
                $nilai_angka = (int)$n['nilai'];
                $total_nilai += $nilai_angka;
                $count++;

                // Hitung predikat
                $predikat = !empty($n['bobot']) ? $n['bobot'] : 'B';
                if($nilai_angka >= 85) $predikat = 'A';
                else if($nilai_angka >= 75) $predikat = 'B';
                else if($nilai_angka >= 60) $predikat = 'C';
                else $predikat = 'D';

                $keterangan = ($nilai_angka >= 75) ? 'Tuntas mencapai kompetensi' : 'Perlu bimbingan remedial';
        ?>
        <tr>
          <td class="text-center"><?php echo $no++; ?></td>
          <td><?php echo htmlspecialchars($n['nama_pelajaran']); ?></td>
          <td class="text-center">75</td>
          <td class="text-center"><strong><?php echo $nilai_angka; ?></strong></td>
          <td class="text-center"><strong><?php echo $predikat; ?></strong></td>
          <td><?php echo htmlspecialchars($keterangan); ?></td>
        </tr>
        <?php 
            }
        } else {
        ?>
        <tr>
          <td colspan="6" class="text-center" style="padding: 20px;"><em>Belum ada data nilai akademik yang dicatat untuk semester ini.</em></td>
        </tr>
        <?php } ?>
      </tbody>
      <?php if($count > 0){ 
          $rata_rata = round($total_nilai / $count, 1);
      ?>
      <tfoot>
        <tr style="background-color: #fafafa; font-weight: bold;">
          <td colspan="3" class="text-center">RATA-RATA NILAI AKADEMIK</td>
          <td class="text-center" style="font-size: 11pt; color: #1a365d;"><?php echo $rata_rata; ?></td>
          <td class="text-center"><?php echo ($rata_rata >= 85 ? 'A' : ($rata_rata >= 75 ? 'B' : 'C')); ?></td>
          <td>Status Rata-Rata: <?php echo ($rata_rata >= 75 ? 'Tuntas Belajar' : 'Perlu Peningkatan'); ?></td>
        </tr>
      </tfoot>
      <?php } ?>
    </table>

    <div class="rekap-grid">
      <div>
        <table class="sub-table">
          <thead>
            <tr>
              <th colspan="2" class="text-center">REKAPITULASI PRESENSI / KEHADIRAN</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Sakit (S)</td>
              <td class="text-center" style="width: 35%;"><strong><?php echo $rekap_absensi['S']; ?></strong> hari</td>
            </tr>
            <tr>
              <td>Izin (I)</td>
              <td class="text-center"><strong><?php echo $rekap_absensi['I']; ?></strong> hari</td>
            </tr>
            <tr>
              <td>Tanpa Keterangan / Alpa (A)</td>
              <td class="text-center"><strong><?php echo $rekap_absensi['A']; ?></strong> hari</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="catatan-box">
        <strong>Catatan Perkembangan Wali Kelas:</strong>
        Peserta didik menunjukkan sikap disiplin dan partisipasi aktif selama proses pembelajaran. Tingkatkan terus motivasi belajar dan capaian prestasi akademik di semester berikutnya.
      </div>
    </div>

    <table class="sig-table">
      <tr>
        <td>
          Mengetahui,<br/>
          Orang Tua / Wali Murid,<br/>
          <div class="sig-space"></div>
          <div class="sig-name"><?php echo !empty($siswa['nama_ayah']) ? htmlspecialchars($siswa['nama_ayah']) : (!empty($siswa['nama_wali']) ? htmlspecialchars($siswa['nama_wali']) : '....................................'); ?></div>
        </td>
        <td>
          <div style="height: 18px;"></div>
          Wali Kelas,<br/>
          <div class="sig-space"></div>
          <div class="sig-name"><?php echo isset($_SESSION['nama_login']) ? htmlspecialchars($_SESSION['nama_login']) : 'Guru Pembina Kelas'; ?></div>
          <div style="font-size: 8.5pt;">NIP: ....................................</div>
        </td>
        <td>
          <?php echo htmlspecialchars($data_sekolah['kota']); ?>, <?php echo date('d F Y'); ?><br/>
          Kepala Sekolah,<br/>
          <div class="sig-space"></div>
          <div class="sig-name"><?php echo htmlspecialchars($kepsek['nama_karyawan']); ?></div>
          <div style="font-size: 8.5pt;">NIP: <?php echo htmlspecialchars($kepsek['nip']); ?></div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
