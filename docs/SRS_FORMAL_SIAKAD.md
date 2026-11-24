# SOFTWARE REQUIREMENTS SPECIFICATION (SRS)
## Sistem Informasi Monitoring Akademik Sekolah (SIAKAD / SISMO)
**Berdasarkan Standar IEEE Std 830-1998 / ISO/IEC/IEEE 29148**

---

### Informasi Dokumen
| Parameter | Rincian Dokumen |
|---|---|
| **Judul Dokumen** | Software Requirements Specification (SRS) - SIAKAD |
| **Nomor Dokumen** | SRS-SIAKAD-2026-V1.0 |
| **Versi Dokumen** | 1.0 (Release Resmi) |
| **Penyusun Utama / Author** | **Bagus Budi Satoto** |
| **Role / Jabatan** | **Web Developer / Lead Software Engineer** |
| **Target Pengguna** | Instansi Pendidikan Sekolah Menengah (SMA/SMK/MA/SMP) |
| **Tanggal Publikasi** | 24 November 2026 |

---

## DAFTAR ISI
1. [Pendahuluan (Introduction)](#1-pendahuluan)
   - 1.1 Tujuan Dokumen (Purpose)
   - 1.2 Cakupan Produk (Scope)
   - 1.3 Definisi, Akronim, dan Singkatan
   - 1.4 Referensi (References)
   - 1.5 Gambaran Umum Dokumen
2. [Deskripsi Keseluruhan (Overall Description)](#2-deskripsi-keseluruhan)
   - 2.1 Perspektif Produk (Product Perspective)
   - 2.2 Fungsi Produk Utama (Product Functions)
   - 2.3 Karakteristik Pengguna (User Classes & Characteristics)
   - 2.4 Batasan Perangkat Lunak (Design & Implementation Constraints)
   - 2.5 Asumsi dan Ketergantungan (Assumptions & Dependencies)
3. [Kebutuhan Antarmuka Eksternal (External Interface Requirements)](#3-kebutuhan-antarmuka-eksternal)
   - 3.1 Antarmuka Pengguna (User Interfaces)
   - 3.2 Antarmuka Perangkat Keras (Hardware Interfaces)
   - 3.3 Antarmuka Perangkat Lunak (Software Interfaces)
   - 3.4 Antarmuka Komunikasi (Communications Interfaces)
4. [Kebutuhan Fungsional (Functional Requirements - FR)](#4-kebutuhan-fungsional)
   - 4.1 Modul Autentikasi & Kontrol Akses (FR-AUTH)
   - 4.2 Modul Master Data & Kelembagaan (FR-MST)
   - 4.3 Modul Kesiswaan & Rombel (FR-SIS)
   - 4.4 Modul Kurikulum & Penjadwalan KBM (FR-JDW)
   - 4.5 Modul Presensi & Rekap Kehadiran (FR-ABS)
   - 4.6 Modul Penilaian Akademik (FR-NIL)
   - 4.7 Modul Keuangan & Billing SPP (FR-KEU)
   - 4.8 Modul Konseling & Kedisiplinan Siswa (FR-BK)
   - 4.9 Modul Portal Siswa & Wali Murid (FR-PRT)
   - 4.10 Modul Perpesanan & Komunikasi (FR-MSG)
5. [Kebutuhan Non-Fungsional (Non-Functional Requirements - NFR)](#5-kebutuhan-non-fungsional)
   - 5.1 Kinerja (Performance Requirements)
   - 5.2 Keamanan Sistem (Security Requirements)
   - 5.3 Keandalan & Ketersediaan (Reliability & Availability)
   - 5.4 Kemudahan Penggunaan (Usability Requirements)
   - 5.5 Pemeliharaan & Portabilitas (Maintainability & Portability)
6. [Matriks Ketertelusuran Kebutuhan (Traceability Matrix)](#6-matriks-ketertelusuran-kebutuhan)
7. [Lembar Pengesahan (Sign-Off)](#7-lembar-pengesahan)

---

## 1. Pendahuluan

### 1.1 Tujuan Dokumen (Purpose)
Tujuan penyusunan dokumen *Software Requirements Specification* (SRS) ini adalah untuk mendefinisikan secara spesifik, formal, dan terukur seluruh spesifikasi kebutuhan fungsional dan non-fungsional dari **Sistem Informasi Monitoring Akademik Sekolah (SIAKAD / SISMO)**. Dokumen ini menjadi kontrak teknis antara pengembang (**Bagus Budi Satoto**) dengan pihak pemangku kepentingan (*stakeholders*) sekolah.

### 1.2 Cakupan Produk (Scope)
Sistem SIAKAD adalah aplikasi perangkat lunak berbasis web terpadu yang melingkupi:
- Pengelolaan data induk institusi, guru, staf, siswa, jurusan, dan kelas.
- Penjadwalan kegiatan belajar mengajar (KBM) terintegrasi.
- Pencatatan dan monitoring kehadiran/absensi siswa real-time.
- Manajemen evaluasi nilai hasil belajar siswa (Tugas, UTS, UAS).
- Penatausahaan billing tagihan dan pencatatan pembayaran SPP/keuangan.
- Bimbingan konseling dan buku rekam jejak poin pelanggaran tata tertib.
- Portal pemantauan mandiri bagi orang tua/wali murid dan siswa.

### 1.3 Definisi, Akronim, dan Singkatan
- **SIAKAD**: Sistem Informasi Akademik Sekolah.
- **SISMO**: Sistem Informasi Monitoring Sekolah.
- **NPSN**: Nomor Pokok Sekolah Nasional (Kode identitas legal sekolah).
- **NIPD**: Nomor Induk Peserta Didik (ID unik siswa dalam sistem).
- **KBM**: Kegiatan Belajar Mengajar.
- **SPP**: Sumbangan Pembinaan Pendidikan.
- **BK**: Bimbingan Konseling.
- **RBAC**: *Role-Based Access Control* (Pengendalian akses berbasis peran).
- **CRUD**: *Create, Read, Update, Delete*.

### 1.4 Referensi (References)
1. IEEE Std 830-1998, *IEEE Recommended Practice for Software Requirements Specifications*.
2. ISO/IEC/IEEE 29148:2018, *Systems and software engineering — Life cycle processes — Requirements engineering*.
3. Pressman, R. S., *Software Engineering: A Practitioner's Approach*, 8th Edition.

---

## 2. Deskripsi Keseluruhan (Overall Description)

### 2.1 Perspektif Produk (Product Perspective)
SIAKAD beroperasi sebagai sistem independen (*standalone web-based system*) dengan arsitektur *Client-Server* multi-tier yang berorientasi pada kemudahan akses melalui peramban web (*web browser*) modern. Sistem dapat dipasang pada infrastruktur intranet lokal (*on-premise*) sekolah maupun server cloud terpusat.

### 2.2 Fungsi Produk Utama (Product Functions)
1. **Otorisasi Terpusat**: Login satu pintu untuk 8 peran pengguna dengan verifikasi sesi berjenjang.
2. **Master Data Repository**: Penyimpanan dan relasi data entitas sekolah terisolasi berbasis NPSN.
3. **Automated Scheduling**: Penyusunan jadwal mengajar bebas bentrok ruang dan guru.
4. **Attendance Tracking**: Pemantauan presensi harian per jam atau per pertemuan.
5. **Academic Grade Reporting**: Perhitungan nilai akhir otomatis untuk pembuatan rapor.
6. **Financial Ledger**: Buku kas penerimaan SPP dan rekapitulasi status tunggakan siswa.
7. **Discipline & Counseling Journal**: Pengarsipan kasus siswa dan pembobotan poin pelanggaran.
8. **Parental Supervision Portal**: Dasbor transparan pemantauan proses belajar anak bagi wali murid.

### 2.3 Karakteristik Pengguna (User Classes & Characteristics)
| Kelas Pengguna | Tingkat Pendidikan / Keahlian | Frekuensi Penggunaan | Hak Akses Utama |
|---|---|---|---|
| **Admin Utama** | Tenaga Ahli IT / Administrator Sistem | Harian / Berkala | Kontrol akses global & konfigurasi sistem. |
| **Admin Sekolah** | Operator Sekolah / Staf Administrasi | Sangat Sering (Harian) | Kelola seluruh master data sekolah. |
| **Kepala Sekolah** | Pimpinan Lembaga / Manajerial | Mingguan / Bulanan | Monitoring eksekutif & persetujuan laporan. |
| **Wali Kelas** | Dewan Guru Pendidik | Setiap Hari Kerja | Presensi kelas, penginputan nilai, kontak wali. |
| **Guru BK** | Guru Konselor Sekolah | Berkala / Insidental | Rekap poin pelanggaran & sesi konseling. |
| **Tata Usaha (TU)** | Staf Keuangan & Kearsipan | Setiap Hari Kerja | Billing SPP, verifikasi kas, data induk siswa. |
| **Siswa** | Peserta Didik Sekolah | Setiap Hari KBM | Akses jadwal, nilai diri, absensi pribadi. |
| **Wali Murid** | Orang Tua / Wali Siswa | Berkala / Setiap Waktu | Memantau absensi, tagihan, dan disiplin anak. |

### 2.4 Batasan Perangkat Lunak (Constraints)
1. Sistem harus berjalan optimal pada PHP 7.x dan MySQL 5.7+ / MariaDB 10.3+.
2. Akses sistem memerlukan web browser yang mendukung standar HTML5, CSS3, dan JavaScript aktif.
3. Keamanan password menggunakan algoritma hashing MD5 standar integrasi database relasional.
4. Ukuran maksimum berkas unggahan foto dibatasi 5 Megabyte per berkas.

---

## 3. Kebutuhan Antarmuka Eksternal (External Interfaces)

### 3.1 Antarmuka Pengguna (User Interfaces)
- Tampilan antarmuka berbasis web responsif menggunakan Bootstrap 3 dan template Miminium.
- Skema warna formal institusional (*corporate blue, slate grey, white*).
- Validasi input formulir di sisi klien (*client-side validation*) menggunakan jQuery dan plugin iCheck.

### 3.2 Antarmuka Perangkat Keras (Hardware Interfaces)
- **Komputer Klien**: PC, Laptop, Tablet, atau Smartphone dengan layar minimum 360px lebar.
- **Server Komputer**: Processor minimum Dual Core 2.0 GHz, RAM minimum 2 GB, Harddisk/SSD minimum 20 GB.

### 3.3 Antarmuka Perangkat Lunak (Software Interfaces)
- **Sistem Operasi Server**: Windows Server (XAMPP/Laragon) atau Linux (Ubuntu/Debian Apache LAMP).
- **Database Engine**: Driver `MySQLi` bawaan PHP dengan koneksi TCP/IP `localhost:3306`.
- **Web Server**: Apache HTTP Server 2.4+ dengan modul `mod_rewrite`.

### 3.4 Antarmuka Komunikasi (Communications Interfaces)
- Protokol transfer web standar **HTTP/1.1** dan **HTTPS** (Port 80 / 443).
- Format pertukaran data internal menggunakan struktur relasional SQL dan string JSON untuk data tertentu.

---

## 4. Kebutuhan Fungsional (Functional Requirements - FR)

Setiap kebutuhan fungsional diberi kode unik dengan format: `FR-[MODUL]-[NOMOR]`.

### 4.1 Modul Autentikasi & Kontrol Akses (FR-AUTH)
* **FR-AUTH-001**: Sistem harus menyediakan formulir login terpadu satu pintu (`Index.php`) yang meminta `Id User` dan `Password`.
* **FR-AUTH-002**: Sistem harus melakukan verifikasi kredensial secara berjenjang ke tabel `tb_admin`, `tb_sekolah`, `tb_karyawan`, dan `tb_siswa`.
* **FR-AUTH-003**: Sistem harus mengarahkan pengguna ke dasbor yang sesuai dengan perannya secara otomatis setelah login valid.
* **FR-AUTH-004**: Sistem harus menolak login tidak valid dengan pesan peringatan (*alert*) dan mengembalikan pengguna ke halaman awal.
* **FR-AUTH-005**: Sistem harus menghapus seluruh data sesi (`session_destroy`) ketika pengguna memilih aksi *Logout*.

### 4.2 Modul Master Data & Kelembagaan (FR-MST)
* **FR-MST-001**: Sistem harus dapat menyimpan, menampilkan, dan memperbarui identitas profil sekolah (`tb_sekolah`) berdasarkan NPSN.
* **FR-MST-002**: Sistem harus dapat melakukan manajemen data kepegawaian (`tb_karyawan`) mencakup guru, kepala sekolah, BK, dan tata usaha.
* **FR-MST-003**: Sistem harus dapat mengelola master kompetensi jurusan (`tb_jurusan`).
* **FR-MST-004**: Sistem harus dapat mengelola master ruangan dan tingkatan kelas (`tb_kelas`).

### 4.3 Modul Kesiswaan & Rombel (FR-SIS)
* **FR-SIS-001**: Sistem harus dapat mencatat buku induk siswa baru (`tb_siswa`) meliputi NIPD, NISN, Nama, Tempat Tanggal Lahir, Alamat, Kontak, dan Nama Wali.
* **FR-SIS-002**: Sistem harus dapat memetakan siswa ke dalam rombongan belajar (rombel) kelas aktif.
* **FR-SIS-003**: Sistem harus secara otomatis membangkitkan kredensial akses mandiri bagi siswa dan kredensial bagi orang tua/wali murid saat registrasi siswa.
* **FR-SIS-004**: Sistem harus dapat mencetak kartu identitas dan biodata profil siswa.

### 4.4 Modul Kurikulum & Penjadwalan KBM (FR-JDW)
* **FR-JDW-001**: Sistem harus dapat mengelola daftar mata pelajaran kurikuler (`tb_pelajaran`).
* **FR-JDW-002**: Sistem harus dapat menetapkan guru pengampu untuk mata pelajaran tertentu.
* **FR-JDW-003**: Sistem harus dapat menyusun jadwal pelajaran (`tb_jadwal`) per hari, jam mulai, jam selesai, kelas, dan ruang.
* **FR-JDW-004**: Sistem harus memvalidasi agar tidak terjadi bentrok jadwal pada guru pengampu yang sama di jam yang sama.

### 4.5 Modul Presensi & Rekap Kehadiran (FR-ABS)
* **FR-ABS-001**: Sistem harus menyediakan formulir presensi harian siswa dengan pilihan status: Hadir (H), Sakit (S), Izin (I), dan Alpa (A).
* **FR-ABS-002**: Sistem harus menyimpan rekapitulasi data presensi ke tabel `tb_absensi` beserta tanggal dan pencatat.
* **FR-ABS-003**: Sistem harus menghitung akumulasi persentase kehadiran per siswa dan per kelas secara otomatis.
* **FR-ABS-004**: Sistem harus menyediakan filter rekap absensi berdasarkan rentang tanggal, bulan, dan kelas.

### 4.6 Modul Penilaian Akademik (FR-NIL)
* **FR-NIL-001**: Sistem harus memfasilitasi wali kelas/guru menginput nilai tugas harian, nilai UTS, dan nilai UAS siswa ke `tb_nilai_akademik`.
* **FR-NIL-002**: Sistem harus menghitung nilai akhir kumulatif mata pelajaran berdasarkan rumus bobot penilaian.
* **FR-NIL-003**: Sistem harus menyediakan laporan capaian hasil belajar (transkrip nilai rapor) per semester.

### 4.7 Modul Keuangan & Billing SPP (FR-KEU)
* **FR-KEU-001**: Sistem harus dapat membuat pos tagihan biaya sekolah (`tb_tagihan`) per siswa atau per rombel.
* **FR-KEU-002**: Sistem harus mencatat transaksi penerimaan setoran kas pembayaran ke tabel `tb_pembayaran`.
* **FR-KEU-003**: Sistem harus memperbarui status tagihan secara otomatis (*Lunas* atau *Menunggak*).
* **FR-KEU-004**: Sistem harus dapat mencetak bukti kuitansi pembayaran resmi sebagai bukti pelunasan.

### 4.8 Modul Konseling & Kedisiplinan Siswa (FR-BK)
* **FR-BK-001**: Sistem harus dapat mencatat kasus pelanggaran tata tertib siswa ke `tb_pelanggaran` beserta bobot poin sanksi.
* **FR-BK-002**: Sistem harus mengagregasi total poin pelanggaran siswa secara kumulatif.
* **FR-BK-003**: Sistem harus menyediakan formulir pencatatan sesi bimbingan konseling individual ke `tb_konseling`.
* **FR-BK-004**: Sistem harus memberikan indikator peringatan jika poin pelanggaran siswa melampaui batas ambang evaluasi.

### 4.9 Modul Portal Siswa & Wali Murid (FR-PRT)
* **FR-PRT-001**: Siswa harus dapat melihat jadwal KBM aktif, rekap absensi diri, nilai akademik, dan status tagihan sekolah melalui dasbor siswa.
* **FR-PRT-002**: Orang tua/wali murid harus dapat memantau kehadiran harian anak di sekolah secara real-time.
* **FR-PRT-003**: Orang tua/wali murid harus dapat melihat riwayat nilai ujian dan capaian hasil belajar anak.
* **FR-PRT-004**: Orang tua/wali murid harus dapat melihat rekam jejak kedisiplinan dan histori pembayaran SPP anak.

### 4.10 Modul Perpesanan & Komunikasi (FR-MSG)
* **FR-MSG-001**: Sistem harus menyediakan fasilitas pengiriman pesan internal (`tb_pesan`) antar-pengguna.
* **FR-MSG-002**: Sistem harus menampilkan kotak masuk (*inbox*) dan penanda status pesan (*Sudah Dibaca / Belum Dibaca*).
* **FR-MSG-003**: Sistem harus menyediakan fitur percakapan langsung (*chat*) untuk koordinasi pihak sekolah dan wali murid.

---

## 5. Kebutuhan Non-Fungsional (Non-Functional Requirements - NFR)

### 5.1 Kinerja (Performance Requirements)
* **NFR-PERF-001**: Waktu respon rata-rata pembacaan halaman (*page load time*) tidak boleh melebihi **2,0 detik** pada koneksi jaringan LAN standar.
* **NFR-PERF-002**: Sistem harus mampu melayani sedikitnya **100 transaksi bersamaan (*concurrent users*)** tanpa terjadi kegagalan koneksi basis data (*database connection timeout*).
* **NFR-PERF-003**: Eksekusi kueri agregasi nilai dan rekapitulasi absensi kelas tidak boleh melebihi **3,0 detik**.

### 5.2 Keamanan Sistem (Security Requirements)
* **NFR-SEC-001**: Seluruh kata sandi (*passwords*) pengguna harus disimpan dalam bentuk terenkripsi satu arah menggunakan hashing *MD5*.
* **NFR-SEC-002**: Setiap endpoint halaman sistem wajib memverifikasi variabel sesi (`$_SESSION`) aktif untuk mencegah akses tidak sah melalui pengetikan URL langsung (*URL Traversal / Direct Object Reference*).
* **NFR-SEC-003**: Sistem harus mengisolasi seluruh data operasional sekolah berdasarkan parameter `npsn` (*Multi-Tenant Isolation*).
* **NFR-SEC-004**: Input formulir wajib melalui sanitasi untuk meminimalisasi risiko serangan *SQL Injection* dan *Cross-Site Scripting (XSS)*.

### 5.3 Keandalan & Ketersediaan (Reliability & Availability)
* **NFR-REL-001**: Tingkat ketersediaan operasional sistem (*System Availability*) ditargetkan minimal **99.0%** pada jam operasional sekolah (07.00 - 17.00 WIB).
* **NFR-REL-002**: Basis data harus mendukung operasi pencadangan berkala (*backup and restore*) tanpa menyebabkan inkonsistensi relasi data.

### 5.4 Kemudahan Penggunaan (Usability Requirements)
* **NFR-USE-001**: Antarmuka pengguna harus menggunakan navigasi menu samping (*sidebar*) yang terstruktur konsisten di seluruh modul.
* **NFR-USE-002**: Setiap aksi penghapusan data krusial (*delete*) wajib memunculkan dialog konfirmasi (*confirmation modal/alert*) sebelum eksekusi.
* **NFR-USE-003**: Format tanggal dan penulisan mata uang (Rupiah / Rp) disesuaikan dengan konvensi standar Indonesia.

### 5.5 Pemeliharaan & Portabilitas (Maintainability & Portability)
* **NFR-MNT-001**: Kode sumber disusun secara terstruktur berdasarkan direktori peran pengguna (*separation of concerns*) untuk mempermudah pemeliharaan (*code maintenance*).
* **NFR-MNT-002**: Sistem dapat dipindahkan (*portable*) antar server web berbasis Windows (XAMPP/Laragon) maupun Linux Server tanpa perubahan struktur kode inti.

---

## 6. Matriks Ketertelusuran Kebutuhan (Traceability Matrix)

| Kode Kebutuhan Fungsional | Deskripsi Singkat | Tabel Basis Data Terkait | Peran Pengguna Utama |
|---|---|---|---|
| `FR-AUTH-001 s/d 005` | Autentikasi Login & Manajemen Sesi | `tb_admin`, `tb_sekolah`, `tb_karyawan`, `tb_siswa` | Seluruh Pengguna |
| `FR-MST-001 s/d 004` | Master Lembaga, Guru, Jurusan, Kelas | `tb_sekolah`, `tb_karyawan`, `tb_jurusan`, `tb_kelas` | Admin Utama, Admin Sekolah |
| `FR-SIS-001 s/d 004` | Buku Induk Siswa & Pembagian Rombel | `tb_siswa`, `tb_kelas` | Tata Usaha, Admin Sekolah |
| `FR-JDW-001 s/d 004` | Katalog Mapel & Penjadwalan KBM | `tb_pelajaran`, `tb_jadwal`, `tb_karyawan` | Admin Sekolah, Wali Kelas |
| `FR-ABS-001 s/d 004` | Input & Rekapitulasi Presensi Siswa | `tb_absensi`, `tb_siswa`, `tb_kelas` | Wali Kelas, Siswa, Wali Murid |
| `FR-NIL-001 s/d 003` | Evaluasi Nilai Tugas, UTS, UAS (E-Rapor) | `tb_nilai_akademik`, `tb_pelajaran`, `tb_siswa` | Wali Kelas, Siswa, Wali Murid |
| `FR-KEU-001 s/d 004` | Pos Tagihan & Transaksi Pembayaran SPP | `tb_tagihan`, `tb_pembayaran`, `tb_siswa` | Tata Usaha, Wali Murid |
| `FR-BK-001 s/d 004` | Poin Pelanggaran & Rekam Bimbingan BK | `tb_konseling`, `tb_pelanggaran`, `tb_siswa` | Guru BK, Wali Kelas, Wali Murid |
| `FR-PRT-001 s/d 004` | Portal Monitoring Siswa & Wali Murid | `tb_siswa`, `tb_absensi`, `tb_jadwal`, `tb_tagihan` | Siswa, Orang Tua / Wali |
| `FR-MSG-001 s/d 003` | Modul Olah Pesan & Chat Internal | `tb_pesan` | Seluruh Pengguna |

---

## 7. Lembar Pengesahan (Sign-Off)

Dokumen Spesifikasi Kebutuhan Perangkat Lunak (*Software Requirements Specification*) ini telah diverifikasi dan disetujui sebagai acuan teknis resmi pengembangan sistem.

| Disusun Oleh: | Menyetujui: |
|:---:|:---:|
| <br/><br/>**Bagus Budi Satoto**<br/>*Lead Web Developer / Software Engineer* | <br/><br/>**Tim Pengarah Proyek Sistem Informasi**<br/>*Representative Stakeholder SIAKAD* |
| Tanggal: 24 November 2026 | Tanggal: 24 November 2026 |
