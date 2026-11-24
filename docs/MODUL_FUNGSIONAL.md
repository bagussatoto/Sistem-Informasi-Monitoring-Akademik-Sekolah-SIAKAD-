# DOKUMEN SPESIFIKASI MODUL FUNGSIONAL SISTEM (FSD)
## Sistem Informasi Monitoring Akademik Sekolah (SIAKAD / SISMO)

---

### Informasi Dokumen
| Atribut | Keterangan |
|---|---|
| **Nama Sistem** | Sistem Informasi Monitoring Akademik Sekolah (SIAKAD) |
| **Versi Dokumen** | 1.0 (Final Draft) |
| **Penyusun / Author** | **Bagus Budi Satoto** |
| **Role** | **Web Developer** |
| **Kategori Dokumen** | Functional Specification Document (FSD) |
| **Klasifikasi** | Internal & Stakeholder Documentation |

---

## 1. Pendahuluan

### 1.1 Latar Belakang
Sistem Informasi Monitoring Akademik Sekolah (SIAKAD / SISMO) dirancang untuk memodernisasi tata kelola administrasi akademik, pemantauan kehadiran, pembinaan kedisiplinan, transaksi keuangan, serta menjembatani sinergi komunikasi antara dewan guru, manajemen sekolah, siswa, dan orang tua/wali murid secara terpadu dan real-time.

### 1.2 Tujuan Dokumen
Dokumen ini disusun sebagai panduan teknis dan operasional yang merinci seluruh batasan modul fungsional sistem, alur proses bisnis, entitas data input, logika pemrosesan, hingga output yang dihasilkan oleh masing-masing modul.

---

## 2. Matriks Hak Akses Modul (Role-Based Access Control)

| Kode Modul | Nama Modul Fungsional | Admin Utama | Admin Sekolah | Kepala Sekolah | Wali Kelas | Guru BK | Tata Usaha | Siswa | Wali Murid |
|:---:|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **MOD-01** | Modul Master Data & Kelembagaan | ✔ | ✔ | 👁 | - | - | 👁 | - | - |
| **MOD-02** | Modul Buku Induk Kesiswaan | ✔ | ✔ | 👁 | 👁 | 👁 | ✔ | - | - |
| **MOD-03** | Modul Kurikulum & Penjadwalan | ✔ | ✔ | 👁 | 👁 | - | 👁 | 👁 | 👁 |
| **MOD-04** | Modul Presensi & Rekap Kehadiran | - | ✔ | 👁 | ✔ | 👁 | 👁 | 👁 | 👁 |
| **MOD-05** | Modul Penilaian Akademik (E-Rapor) | - | ✔ | 👁 | ✔ | - | - | 👁 | 👁 |
| **MOD-06** | Modul Billing & Keuangan SPP | - | ✔ | 👁 | - | - | ✔ | 👁 | 👁 |
| **MOD-07** | Modul Konseling & Poin Pelanggaran | - | ✔ | 👁 | 👁 | ✔ | - | 👁 | 👁 |
| **MOD-08** | Modul Portal Siswa Mandiri | - | - | - | - | - | - | ✔ | - |
| **MOD-09** | Modul Monitoring Wali Murid | - | - | - | - | - | - | - | ✔ |
| **MOD-10** | Modul Perpesanan Internal | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ |
| **MOD-11** | Modul Eksekutif Kepala Sekolah | - | - | ✔ | - | - | - | - | - |

*Keterangan: (✔) Hak Akses Penuh / Kelola (CRUD), (👁) Akses Lihat / Read-Only, (-) Tidak Ada Akses.*

---

## 3. Rincian Modul Fungsional Utama

```
                      ┌─────────────────────────────────────────┐
                      │    GERBANG LOGIN TERPADU (Index.php)     │
                      └────────────────────┬────────────────────┘
                                           │
         ┌─────────────────────────────────┴─────────────────────────────────┐
         ▼                                 ▼                                 ▼
┌──────────────────┐             ┌──────────────────┐             ┌──────────────────┐
│ MANAJEMEN DATA   │             │ OPERASIONAL KBM  │             │ MONITORING &     │
│ & KELEMBAGAAN    │             │ & KEUANGAN       │             │ KONSELING        │
├──────────────────┤             ├──────────────────┤             ├──────────────────┤
│ • Master Sekolah │             │ • Jadwal Mapel   │             │ • Rekap Absensi  │
│ • Guru & Pegawai │             │ • Nilai Siswa    │             │ • Poin Pelanggaran│
│ • Data Induk PD  │             │ • Tagihan & SPP  │             │ • Rekam BK       │
│ • Rombel & Kelas │             │ • Olah Pesan     │             │ • Dashboard Wali │
└──────────────────┘             └──────────────────┘             └──────────────────┘
```

---

### MOD-01: Modul Manajemen Master Data & Kelembagaan
* **Deskripsi**: Modul sentral untuk mengelola identitas resmi institusi sekolah, cabang kompetensi keahlian/jurusan, tingkatan kelas, dan struktur kepegawaian.
* **Aktor Utama**: Admin Utama, Admin Sekolah, Tata Usaha.
* **Fitur Fungsional**:
  1. Pengelolaan profil sekolah (NPSN, nama institusi, alamat, logo, akreditasi).
  2. Pendataan tenaga pendidik (guru) dan staf kependidikan (karyawan/TU).
  3. Master data keahlian/jurusan (`tb_jurusan`) dan pembagian tingkatan ruang kelas (`tb_kelas`).
* **Input Data**: NPSN, data diri pendidik, NIP/Kode Guru, nama jurusan, kode kelas.
* **Output Sistem**: Profil sekolah terverifikasi, direktori guru/staf, struktur rombel kelas siap pakai.
* **Tabel Basis Data**: `tb_sekolah`, `tb_karyawan`, `tb_jurusan`, `tb_kelas`.

---

### MOD-02: Modul Buku Induk Kesiswaan & Manajemen Rombel
* **Deskripsi**: Mengelola siklus hidup data peserta didik sejak terdaftar hingga penempatan rombongan belajar (rombel).
* **Aktor Utama**: Admin Sekolah, Bagian Tata Usaha (TU).
* **Fitur Fungsional**:
  1. Pencatatan identitas lengkap siswa (NIPD, NISN, Nama Lengkap, Tempat Tanggal Lahir, Alamat, Kontak).
  2. Registrasi data identitas orang tua/wali murid beserta pembuatan kredensial akses wali.
  3. Penempatan siswa ke kelas tujuan dan pembagian rombel per tahun ajaran.
* **Input Data**: Berkas identitas calon siswa, formulir data orang tua, nomor induk.
* **Output Sistem**: Buku induk siswa digital, kartu identitas siswa, daftar hadir rombel kelas.
* **Tabel Basis Data**: `tb_siswa`, `tb_kelas`.

---

### MOD-03: Modul Kurikulum, Mata Pelajaran & Penjadwalan KBM
* **Deskripsi**: Mengatur distribusi kurikulum pelajaran dan pemetaan jam mengajar guru secara terstruktur guna mencegah tumpang tindih waktu.
* **Aktor Utama**: Admin Sekolah, Kurikulum, Wali Kelas.
* **Fitur Fungsional**:
  1. Master katalog mata pelajaran (`tb_pelajaran`).
  2. Penugasan guru pengampu untuk setiap mata pelajaran per kelas.
  3. Pembuatan jadwal belajar mengajar mingguan (Hari, Jam Mulai, Jam Selesai, Ruang).
* **Input Data**: Kode mapel, nama pelajaran, kode guru pengampu, waktu KBM.
* **Output Sistem**: Jadwal pelajaran interaktif per kelas, jadwal mengajar pribadi guru.
* **Tabel Basis Data**: `tb_pelajaran`, `tb_jadwal`, `tb_karyawan`, `tb_kelas`.

---

### MOD-04: Modul Presensi & Rekapitulasi Kehadiran
* **Deskripsi**: Mencatat kehadiran harian siswa secara akurat dan menyajikan rekapitulasi tingkat kedisiplinan siswa di sekolah.
* **Aktor Utama**: Wali Kelas, Guru Piket, Tata Usaha.
* **Fitur Fungsional**:
  1. Form penginputan absensi kelas harian dengan status: **Hadir (H)**, **Sakit (S)**, **Izin (I)**, dan **Alpa (A)**.
  2. Rekapitulasi kehadiran berkala (mingguan/bulanan/semester).
  3. Kalkulasi persentase kehadiran siswa otomatis sebagai syarat kelayakan evaluasi belajar.
* **Input Data**: Tanggal absensi, NIPD siswa, status kehadiran, keterangan izin/sakit.
* **Output Sistem**: Laporan absensi kelas, grafik kehadiran individu siswa, notifikasi alpa berlebih.
* **Tabel Basis Data**: `tb_absensi`, `tb_siswa`, `tb_kelas`.

---

### MOD-05: Modul Penilaian Akademik & Evaluasi Belajar (E-Rapor)
* **Deskripsi**: Wadah terintegrasi untuk pendokumentasian nilai capaian kompetensi belajar siswa secara transparan.
* **Aktor Utama**: Wali Kelas, Guru Mata Pelajaran.
* **Fitur Fungsional**:
  1. Penginputan nilai tugas harian, nilai Ujian Tengah Semester (UTS), dan Ujian Akhir Semester (UAS).
  2. Perhitungan nilai akhir kumulatif otomatis sesuai bobot persentase evaluasi.
  3. Publikasi nilai yang dapat diakses langsung oleh siswa dan orang tua.
  4. Pencetakan Lembar Rapor Digital (E-Rapor) resmi format A4 siap cetak/PDF (`cetak_rapor.php`) lengkap dengan kop sekolah, biodata siswa, tabel nilai & bobot, rekapitulasi kehadiran (Sakit, Izin, Alpa), catatan wali kelas, dan 3 kolom pengesahan (Wali Murid, Wali Kelas, Kepala Sekolah).
* **Input Data**: Nilai angka harian, nilai UTS, nilai UAS, catatan capaian wali kelas.
* **Output Sistem**: Lembar nilai akademik siswa, transkrip nilai semester, lembar cetak rapor (`cetak_rapor.php`), laporan rangking kelas.
* **Tabel Basis Data**: `tb_nilai_akademik`, `tb_pelajaran`, `tb_siswa`.

---

### MOD-06: Modul Administrasi Keuangan, Tagihan & Pembayaran SPP
* **Deskripsi**: Mengotomatisasi penagihan uang sekolah (SPP) dan transaksi keuangan agar tertib administrasi serta bebas dari selisih pembukuan.
* **Aktor Utama**: Petugas Tata Usaha (Bendahara Sekolah), Siswa, Wali Murid.
* **Fitur Fungsional**:
  1. Pembuatan pos tagihan rutin bulanan (SPP) maupun biaya insidental (ujian, praktikum, seragam).
  2. Input penerimaan kas pembayaran dari siswa/wali murid.
  3. Validasi status pembayaran (*Lunas* / *Menunggak*).
  4. Pencetakan bukti kuitansi pembayaran resmi format A5 landscape siap cetak/PDF (`cetak_kuitansi.php`) dengan nomor kuitansi otomatis, terbilang rupiah dinamis, rincian pembayaran, dan tanda tangan kasir Tata Usaha.
* **Input Data**: Jenis tagihan, nominal tagihan, tanggal jatuh tempo, nominal setor, tanggal bayar.
* **Output Sistem**: Kuitansi pembayaran resmi (`cetak_kuitansi.php`), laporan buku besar tagihan, rekap tunggakan siswa.
* **Tabel Basis Data**: `tb_tagihan`, `tb_pembayaran`, `tb_siswa`.

---

### MOD-07: Modul Bimbingan Konseling (BK) & Kedisiplinan Siswa
* **Deskripsi**: Instrumen pemantauan perilaku dan pembinaan moral siswa berbasis bobot poin sanksi dan catatan konseling berkala.
* **Aktor Utama**: Guru Bimbingan Konseling (BK), Wali Kelas, Kepala Sekolah.
* **Fitur Fungsional**:
  1. Pencatatan riwayat pelanggaran tata tertib siswa beserta bobot poin sanksi pelanggaran.
  2. Buku catatan konseling individual siswa (masalah, tanggal konsultasi, dan hasil solusi pembinaan).
  3. Akumulasi poin pelanggaran untuk dasar penerbitan surat peringatan (SP) atau panggilan orang tua.
* **Input Data**: NIPD siswa, kategori pelanggaran, tanggal kejadian, poin sanksi, isi sesi bimbingan konseling.
* **Output Sistem**: Riwayat rekap konseling, buku sanksi kedisiplinan siswa, status perilaku siswa.
* **Tabel Basis Data**: `tb_konseling`, `tb_pelanggaran`, `tb_siswa`.

---

### MOD-08: Modul Portal Mandiri Siswa
* **Deskripsi**: Antarmuka mandiri bagi siswa untuk memantau seluruh aktivitas pendidikannya di sekolah.
* **Aktor Utama**: Siswa.
* **Fitur Fungsional**:
  1. Menampilkan jadwal KBM aktif mingguan.
  2. Memeriksa riwayat kehadiran/absensi diri secara real-time.
  3. Melihat nilai akademik dan evaluasi hasil belajar.
  4. Memeriksa riwayat tagihan dan bukti status pembayaran SPP.
* **Input Data**: Kredensial login siswa (NIPD & Password Siswa).
* **Output Sistem**: Dashboard profil akademik pribadi siswa.
* **Tabel Basis Data**: `tb_siswa`, `tb_jadwal`, `tb_absensi`, `tb_nilai_akademik`, `tb_tagihan`.

---

### MOD-09: Modul Monitoring Wali Murid (Parental Supervision)
* **Deskripsi**: Sarana pengawasan orang tua terhadap perkembangan proses pendidikan anaknya di sekolah secara transparan tanpa harus menunggu bagi rapor.
* **Aktor Utama**: Orang Tua / Wali Murid.
* **Fitur Fungsional**:
  1. Pemantauan kehadiran anak di kelas (notifikasi keterlambatan/alpa).
  2. Pengecekan riwayat nilai tugas, UTS, dan UAS anak.
  3. Pemantauan kedisiplinan anak (catatan poin pelanggaran dan sesi konseling BK).
  4. Pengecekan status tagihan dan pembayaran sekolah anak.
* **Input Data**: Kredensial login wali murid (NIPD Siswa & Password Wali).
* **Output Sistem**: Dashboard monitoring anak komprehensif.
* **Tabel Basis Data**: `tb_siswa`, `tb_absensi`, `tb_nilai_akademik`, `tb_pembayaran`, `tb_pelanggaran`.

---

### MOD-10: Modul Komunikasi & Kotak Pesan Internal
* **Deskripsi**: Kanal pertukaran pesan langsung antar-pengguna untuk koordinasi yang cepat, terdokumentasi, dan terpusat di dalam sistem.
* **Aktor Utama**: Seluruh Peran Pengguna.
* **Fitur Fungsional**:
  1. Pengiriman pesan personal antara wali murid dan wali kelas / guru.
  2. Modul baca pesan dan kotak masuk (inbox) terorganisir.
  3. Fitur chat internal untuk diskusi seputar permasalahan akademik atau operasional sekolah.
* **Input Data**: ID Pengirim, ID Penerima, subjek, isi pesan, lampiran (jika ada).
* **Output Sistem**: Kotak masuk pesan, log histori percakapan.
* **Tabel Basis Data**: `tb_pesan`.

---

### MOD-11: Modul Eksekutif & Monitoring Kepala Sekolah
* **Deskripsi**: Dashboard analitik untuk pimpinan sekolah guna meninjau performa institusi secara menyeluruh.
* **Aktor Utama**: Kepala Sekolah.
* **Fitur Fungsional**:
  1. Monitoring ringkasan absensi seluruh tingkatan kelas.
  2. Pengawasan rekap capaian nilai siswa dan performa mengajar dewan guru.
  3. Peninjauan laporan rekapitulasi keuangan dan catatan pembinaan kesiswaan.
* **Input Data**: Periode tahun ajaran, filter tingkatan kelas.
* **Output Sistem**: Laporan eksekutif berkala, grafik statistik kehadiran dan akademik.
* **Tabel Basis Data**: Seluruh tabel operasional (`tb_absensi`, `tb_nilai_akademik`, `tb_karyawan`, `tb_siswa`).

---

## 4. Keamanan & Logika Pemrosesan Data

1. **Autentikasi Multi-Peran Terpusat**:
   - Sistem membaca input identitas (`no_id`) dan `password` (terenkripsi *MD5*).
   - Skrip `login.php` memeriksa kecocokan di tabel administrator, sekolah, karyawan (berdasarkan `status_kepegawaian`), serta siswa/wali murid secara bertingkat sebelum menetapkan hak akses sesi (`$_SESSION`).
2. **Isolasi Data Antar-Instansi (Multi-Tenant by NPSN)**:
   - Setiap transaksi data (guru, siswa, nilai, absensi, tagihan) terikat dengan identitas Nomor Pokok Sekolah Nasional (`npsn`), memastikan privasi dan integritas data sekolah tetap terisolasi.
3. **Session Validation & Access Control**:
   - Setiap sub-modul direktori memiliki proteksi sesi untuk mencegah akses tidak sah (*unauthorized URL traversal*).

---

## 5. Arsitektur Infrastruktur & Stack Teknis

### 5.1 Diagram Arsitektur Lapisan Sistem
```
┌────────────────────────────────────────────────────────────────────────┐
│                      ARSITEKTUR STACK TEKNIS SIAKAD                    │
├────────────────────────────────────────────────────────────────────────┤
│  Client Layer      : Web Browser (Chrome, Firefox, Edge, Safari)       │
│  Presentation/UI   : HTML5, CSS3, Bootstrap 3 (Miminium), jQuery      │
│  Application Layer : Native PHP (PHP 7.0 - 7.4 / 5.6+ with MySQLi)     │
│  Database Layer    : MySQL 5.7+ / MariaDB 10.3+ (Engine: InnoDB)       │
│  Web Server        : Apache HTTP Server 2.4+ (dengan mod_rewrite)      │
│  OS Target         : Windows Server / Linux (Ubuntu/Debian/CentOS)     │
└────────────────────────────────────────────────────────────────────────┘
```

### 5.2 Komponen Perangkat Lunak (Software Stack)
* **Backend Runtime**: Native PHP 7.0 - 7.4 (Kompatibel PHP 5.6+).
* **Ekstensi PHP Wajib**:
  - `mysqli` : Driver komunikasi dan eksekusi kueri basis data relasional.
  - `session` : Manajemen sesi autentikasi dan status hak akses pengguna.
  - `fileinfo` & `gd` : Validasi MIME type dan kompresi berkas foto profil siswa/guru.
  - `json` & `mbstring` : Penanganan data terenkripsi dan format pertukaran data.
* **Database Management System**: MySQL 5.7+ atau MariaDB 10.3+ (Engine `InnoDB` untuk integritas relasi tabel).
* **Web Server Engine**: Apache HTTP Server 2.4+ dengan modul aktif `mod_rewrite` dan pemrosesan berkas `.htaccess`.
* **Frontend Framework & Library**:
  - Bootstrap 3.3.7 Admin Dashboard (Miminium Theme).
  - FontAwesome 4.x & Simple Line Icons.
  - jQuery 2.1.4, jQuery UI, Moment.js, dan iCheck Flat-Aero plugin.

### 5.3 Spesifikasi Kebutuhan Perangkat Keras (Hardware Requirements)
| Komponen Server | Kebutuhan Minimum (≤ 500 Siswa) | Rekomendasi Skala Penuh (> 1.000 Siswa) |
|---|---|---|
| **Processor / CPU** | Dual-Core 2.0 GHz (atau 1 vCPU Cloud) | Quad-Core 2.5 GHz ke atas (atau 2-4 vCPU) |
| **RAM (Memory)** | 2 GB DDR3/DDR4 | 4 GB - 8 GB DDR4 |
| **Storage / Harddisk** | 20 GB HDD/SSD | 50 GB - 100 GB SSD NVMe (Arsip & Backup) |
| **Bandwidth / Jaringan** | LAN 100 Mbps (Lokal) / Internet 5 Mbps | LAN Gigabit 1000 Mbps / Internet 20-50 Mbps |

### 5.4 Model Penerapan Sistem (Deployment Strategy)
1. **Model On-Premise / Jaringan Intranet Sekolah**:
   - Server fisik dipasang di laboratorium komputer atau ruang server sekolah.
   - Menggunakan bundel XAMPP, Laragon, atau Linux Server (LAMP).
   - Seluruh dewan guru, operator TU, dan siswa dapat mengakses aplikasi melalui jaringan lokal LAN/WiFi sekolah tanpa menggunakan kuota internet.
2. **Model Cloud / Web Hosting Publik**:
   - Di-deploy pada layanan Cloud VPS atau Shared Hosting cPanel dengan domain sekolah resmi (misal: `siakad.sekolah.sch.id`).
   - Memungkinkan orang tua/wali murid memantau presensi dan nilai akademik anak secara online dari smartphone di mana saja dan kapan saja.

---

## 6. Lembar Pengesahan

| Disusun Oleh | Mengetahui / Disetujui |
|:---:|:---:|
| <br/><br/>**Bagus Budi Satoto**<br/>*Web Developer* | <br/><br/>**Tim Manajemen Sistem Informasi**<br/>*SIAKAD Project Lead* |
