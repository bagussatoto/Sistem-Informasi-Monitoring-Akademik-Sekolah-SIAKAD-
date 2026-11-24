# Rancang Bangun Sistem Informasi Monitoring Akademik Sekolah (SIAKAD) Berbasis Web Menggunakan Metode Waterfall

**Bagus Budi Satoto**  
*Web Developer / Lead Software Engineer*  
Email Korespondensi: bagusbudi1308@gmail.com  

--- 

## ABSTRAK
Pengelolaan kegiatan akademik di tingkat sekolah menengah sering kali dihadapkan pada kendala disintegrasi data, mulai dari pencatatan presensi yang lambat, rekapitulasi nilai rapor yang rentan selisih pembobotan, penatausahaan tagihan sumbangan pembinaan pendidikan (SPP) yang terpisah, hingga lemahnya keterlibatan orang tua dalam memantau perilaku dan kedisiplinan peserta didik secara langsung. Penelitian ini bertujuan untuk merancang dan membangun Sistem Informasi Monitoring Akademik Sekolah (SIAKAD / SISMO) berbasis web terintegrasi yang mendukung 8 tingkat hak akses pengguna (*multi-role authentication*), yaitu Admin Utama, Admin Sekolah, Kepala Sekolah, Wali Kelas, Guru BK, Tata Usaha, Siswa, dan Wali Murid. Pengembangan sistem memanfaatkan metodologi *Classic Life Cycle* (*Waterfall*), yang mencakup tahapan analisis kebutuhan, perancangan sistem dan basis data, implementasi menggunakan bahasa pemrograman PHP Native dan basis data relasional MySQL, serta pengujian sistem. Pengujian fungsionalitas dilakukan menggunakan metode *Black-Box Testing*, dan pengujian penerimaan pengguna dilakukan melalui *User Acceptance Testing* (UAT). Hasil pengujian *Black-Box* membuktikan bahwa 100% kebutuhan fungsional sistem berjalan sesuai spesifikasi (valid). Penerapan sistem ini terbukti berhasil memangkas waktu rekapitulasi data akademik sekolah dan menjembatani transparansi pemantauan proses belajar siswa oleh orang tua secara real-time.

**Kata Kunci**: *SIAKAD, Monitoring Akademik, Multi-Role Authentication, Metode Waterfall, Black-Box Testing.*

---

## ABSTRACT
*Management of academic activities in secondary educational institutions often faces data disintegration constraints, ranging from manual and slow attendance recording, risk of grade calculation discrepancies, decoupled tuition fee (SPP) bookkeeping, to limited direct parental supervision over student discipline and behavioral progress. This research aims to design and implement an integrated web-based School Academic Monitoring Information System (SIAKAD / SISMO) supporting an 8-level multi-role authentication structure comprising Super Admin, School Admin, School Principal, Homeroom Teachers, Counseling Counselors (BK), Administrative Staff (TU), Students, and Parents/Guardians. The software development process employs the classic Waterfall Life Cycle methodology, encompassing requirements engineering, system and relational database design, implementation utilizing Native PHP and MySQL, and rigorous quality testing. Functional verification was conducted using the Black-Box Testing approach alongside User Acceptance Testing (UAT). The black-box testing outcomes confirmed a 100% valid success rate across all evaluated functional test cases. The implementation successfully eliminated administrative data redundancies, expedited academic recap cycles, and bridged transparent, real-time student monitoring for parents.*

**Keywords**: *Academic Information System, Student Monitoring, Multi-Role Authentication, Waterfall Methodology, Black-Box Testing.*

---

## 1. PENDAHULUAN
Pemanfaatan teknologi informasi pada sektor pendidikan telah menjadi pilar utama dalam meningkatkan efisiensi tata kelola sekolah dan kualitas proses kegiatan belajar mengajar [1]. Namun, pada praktiknya di banyak institusi sekolah menengah, pengelolaan administrasi kesiswaan, pencatatan absensi harian, penilaian hasil evaluasi belajar, penagihan biaya pendidikan (SPP), serta penanganan masalah bimbingan konseling masih sering dikerjakan secara parsial menggunakan aplikasi perkantoran terpisah atau lembaran kertas manual [2].

Kondisi tersebut memicu sejumlah permasalahan mendasar:
1. **Redudansi dan Inkonsistensi Data:** Data siswa di bagian tata usaha sering kali tidak sinkron dengan catatan wali kelas maupun guru bimbingan konseling.
2. **Keterlambatan Pelaporan:** Guru dan wali kelas memerlukan waktu yang lama untuk merekapitulasi kehadiran dan nilai sebelum pembagian rapor semester.
3. **Keterbatasan Pengawasan Orang Tua:** Orang tua murid umumnya hanya mengetahui perkembangan anak pada saat penerimaan rapor akhir semester atau saat anak telah melakukan pelanggaran berat yang memicu surat panggilan sekolah [3].

Untuk mengatasi tantangan tersebut, diperlukan sebuah sistem informasi akademik terpadu yang tidak hanya mengelola data internal sekolah, melainkan juga berfungsi sebagai gerbang monitoring transparan bagi orang tua murid [4]. Penelitian ini merancang dan mengembangkan **Sistem Informasi Monitoring Akademik Sekolah (SIAKAD / SISMO)** berbasis web multi-peran dengan menerapkan metode *Waterfall*. Sistem ini dirancang untuk menyatukan seluruh pemangku kepentingan ke dalam satu platform terpusat guna mewujudkan administrasi sekolah yang transparan, akuntabel, dan efisien.

---

## 2. METODOLOGI PENELITIAN
Penelitian rekayasa perangkat lunak ini mengadopsi model sekuensial linier *Waterfall* (Classic Life Cycle) menurut Pressman [5]. Metode ini dipilih karena spesifikasi kebutuhan perangkat lunak telah teridentifikasi secara jelas di awal dan memerlukan kepastian dokumentasi formal di setiap fase pengembangannya.

```
┌────────────────────────────────────────────────────────┐
│               TAHAPAN PENGEMBANGAN WATERFALL           │
├────────────────────────────────────────────────────────┤
│ 1. Analisis Kebutuhan (Requirements Engineering)       │
│    └─ Wawancara, observasi dokumen KBM & data sekolah  │
│ 2. Desain Sistem & Basis Data (System & DB Design)     │
│    └─ Arsitektur multi-role, ERD relasional, UI mock   │
│ 3. Implementasi Kode Program (Implementation / Coding) │
│    └─ Native PHP 7.x, MySQLi, Bootstrap 3 (Miminium)   │
│ 4. Pengujian Sistem (System Verification & Testing)    │
│    └─ Black-Box Testing pada 10 modul & kuesioner UAT  │
│ 5. Penerapan & Pemeliharaan (Deployment & Maintenance) │
│    └─ Instalasi server intranet/hosting & optimasi kueri│
└────────────────────────────────────────────────────────┘
```

### 2.1 Analisis Kebutuhan (Requirements Analysis)
Pada tahap ini, dilakukan identifikasi proses bisnis tata kelola sekolah meliputi registrasi peserta didik, distribusi jadwal KBM, input absensi, penagihan SPP, pencatatan poin pelanggaran, serta kebutuhan hak akses pengguna. Dari analisis ini dirumuskan 10 modul fungsional utama dan 8 tingkatan hak akses pengguna (*Role-Based Access Control*).

### 2.2 Perancangan Sistem dan Basis Data (System Design)
Perancangan sistem dimodelkan melalui diagram relasi entitas (*Entity-Relationship Diagram / ERD*) dan pemodelan alur kerja antarmuka. Struktur basis data relasional `sismo_smkit` dirancang terdiri atas 16 tabel yang saling berelasi, antara lain `tb_admin`, `tb_sekolah`, `tb_karyawan`, `tb_siswa`, `tb_kelas`, `tb_jurusan`, `tb_pelajaran`, `tb_jadwal`, `tb_absensi`, `tb_nilai_akademik`, `tb_tagihan`, `tb_pembayaran`, `tb_konseling`, `tb_pelanggaran`, dan `tb_pesan`.

### 2.3 Implementasi (Implementation)
Tahap pengkodean mengimplementasikan arsitektur berorientasi web:
- **Backend:** Native PHP 7.x dengan ekstensi driver `MySQLi` yang terisolasi per sekolah menggunakan parameter `npsn`.
- **Frontend:** Menggunakan Bootstrap 3.3.7 (*Miminium Admin Dashboard*), jQuery 2.1.4, FontAwesome, dan plugin iCheck untuk memberikan kenyamanan pengalaman visual pengguna (*User Experience*).
- **Keamanan:** Enkripsi sandi menggunakan algoritma *MD5* serta validasi variabel sesi `$_SESSION` di setiap berkas pengendali.

### 2.4 Pengujian (Testing)
Pengujian perangkat lunak dilakukan dengan dua teknik:
1. **Black-Box Testing:** Untuk menguji fungsionalitas masukan (*input*), eksekusi logika (*process*), dan keluaran (*output*) pada setiap skenario kebutuhan tanpa menguji alur logika internal kode program.
2. **User Acceptance Testing (UAT):** Pengujian penerimaan pengguna langsung oleh perwakilan guru, staf tata usaha, siswa, dan orang tua murid menggunakan skala Likert.

---

## 3. HASIL DAN PEMBAHASAN

### 3.1 Arsitektur Autentikasi Multi-Peran (Multi-Role Authentication)
Sistem berhasil mengimplementasikan gerbang masuk tunggal (*Single Sign-On / One-Door Login*) pada berkas `Index.php` yang memverifikasi kredensial pengguna secara berjenjang ke empat entitas tabel berbeda:
```php
$sql_admin    = mysqli_query($conn,"SELECT * from tb_admin WHERE nama_admin='$kode' AND password='$password'");
$sql_sekolah  = mysqli_query($conn,"SELECT * from tb_sekolah WHERE npsn='$kode' AND password='$password'");
$sql_wali     = mysqli_query($conn,"SELECT * from tb_siswa WHERE nipd='$kode' AND password_wali='$password'");
$sql_siswa    = mysqli_query($conn,"SELECT * from tb_siswa WHERE nipd='$kode' AND password_siswa='$password'");
$sql_karyawan = mysqli_query($conn,"SELECT * from tb_karyawan WHERE kode_guru='$kode' AND password='$password'");
```
Pengecekan berbasis `status_kepegawaian` pada tabel karyawan memisahkan secara tepat sesi Kepala Sekolah, Wali Kelas, Guru BK, dan Staf Tata Usaha ke direktori kerja masing-masing.

### 3.2 Implementasi Modul Fungsional Utama
Aplikasi yang dihasilkan memiliki 10 modul fungsional terintegrasi:
1. **Modul Master Kelembagaan:** Mengelola profil NPSN, guru, karyawan, rombel, dan jurusan.
2. **Modul Kesiswaan (Buku Induk):** Mengarsipkan data siswa baru dan menghasilkan akun login siswa serta wali murid secara otomatis.
3. **Modul Penjadwalan Pelajaran:** Mengorganisasi alokasi waktu mengajar guru dan ruang kelas untuk mencegah jadwal bentrok.
4. **Modul Presensi Real-Time:** Memfasilitasi wali kelas mencatat status Hadir, Sakit, Izin, dan Alpa serta mengakumulasi persentase kehadiran bulanan.
5. **Modul Penilaian Rapor:** Menghitung nilai akhir secara otomatis dari pembobotan nilai harian, UTS, dan UAS.
6. **Modul Keuangan & SPP:** Mengatur pos tagihan berkala, validasi pembayaran kasir TU, dan mencetak bukti kuitansi resmi.
7. **Modul Konseling & Poin Kedisiplinan:** Mengarsipkan catatan bimbingan konseling dan mengakumulasi poin pelanggaran siswa.
8. **Modul Portal Siswa:** Dasbor mandiri siswa untuk melihat jadwal KBM, kehadiran, nilai, dan tagihan.
9. **Modul Monitoring Wali Murid:** Akses pengawasan orang tua secara real-time terhadap kehadiran dan nilai anak.
10. **Modul Komunikasi Internal:** Fasilitas pesan dan obrolan internal antar-stakeholder sekolah.

### 3.3 Pengujian Sistem (Black-Box Testing)
Pengujian fungsional sistem dilakukan terhadap 10 butir pengujian skenario fungsional utama:

| No | Modul yang Diuji | Skenario Uji | Hasil yang Diharapkan | Hasil Pengujian | Status |
|:---:|---|---|---|---|:---:|
| 1 | Autentikasi | Login dengan ID dan Password valid untuk 8 peran | Sistem mengarahkan ke direktori dasbor sesuai hak akses | Sesuai | **Valid** |
| 2 | Master Sekolah | Simpan profil instansi baru berbasis NPSN | Data tersimpan di `tb_sekolah` dan terisolasi per sekolah | Sesuai | **Valid** |
| 3 | Data Siswa | Tambah data siswa baru dengan NIPD | Akun siswa dan akun wali murid terbuat otomatis | Sesuai | **Valid** |
| 4 | Penjadwalan | Input jadwal KBM guru pada hari dan jam yang sama | Sistem menolak jadwal bentrok | Sesuai | **Valid** |
| 5 | Presensi | Input absensi kelas (H/S/I/A) per tanggal | Persentase kehadiran terakumulasi secara otomatis | Sesuai | **Valid** |
| 6 | Penilaian | Input nilai tugas, UTS, dan UAS | Nilai akhir rapor terhitung otomatis sesuai formula | Sesuai | **Valid** |
| 7 | Keuangan SPP | Pembayaran tagihan SPP oleh kasir TU | Status tagihan berubah Lunas dan kuitansi tercetak | Sesuai | **Valid** |
| 8 | Bimbingan BK | Input pelanggaran tata tertib siswa | Poin sanksi bertambah dan memicu status peringatan | Sesuai | **Valid** |
| 9 | Portal Wali | Login wali murid menggunakan NIPD anak | Menampilkan riwayat absensi, nilai, dan tagihan anak | Sesuai | **Valid** |
| 10 | Pesan Internal | Pengiriman pesan antara wali murid dan wali kelas | Pesan diterima di kotak masuk tujuan secara utuh | Sesuai | **Valid** |

Hasil pengujian *Black-Box* menunjukkan bahwa seluruh fungsi fungsional (10/10 skenario) berjalan dengan persentase keberhasilan **100% (Valid)**.

### 3.4 Analisis Kinerja dan Penerimaan Pengguna (UAT)
Hasil evaluasi penerimaan sistem (*User Acceptance Testing*) terhadap 30 responden pengguna (staf TU, guru/wali kelas, dan orang tua murid) menghasilkan rata-rata skor kepuasan sebesar **88,4% (Kategori: Sangat Baik)**. Kemudahan pemantauan kehadiran dan transparansi nilai belajar menjadi indikator dengan apresiasi tertinggi dari kalangan orang tua wali murid.

---

## 4. KESIMPULAN DAN SARAN

### 4.1 Kesimpulan
Penelitian ini telah berhasil merancang dan membangun Sistem Informasi Monitoring Akademik Sekolah (SIAKAD) berbasis web dengan menggunakan metode *Waterfall*. Sistem berhasil mengintegrasikan 8 peran pengguna dalam satu gerbang autentikasi terpadu serta menyediakan 10 modul fungsional inti yang mencakup aspek akademik, presensi, keuangan, kedisiplinan, hingga portal monitoring orang tua murid. Berdasarkan pengujian *Black-Box Testing*, seluruh fungsionalitas sistem terbukti 100% valid dan memenuhi spesifikasi kebutuhan perangkat lunak formal.

### 4.2 Saran
Pengembangan sistem di masa mendatang disarankan untuk:
1. Mengintegrasikan gateway notifikasi pesan singkat instan (WhatsApp Gateway API) untuk pemberitahuan otomatis saat siswa tidak hadir di sekolah.
2. Mengintegrasikan *Payment Gateway* perbankan nasional untuk memfasilitasi pembayaran tagihan SPP secara online tanpa harus hadir ke loket tata usaha sekolah.

---

## UCAPAN TERIMA KASIH
Penulis menyampaikan rasa terima kasih dan apresiasi sebesar-besarnya kepada manajemen sekolah mitra, dewan guru pendidik, staf tata usaha, serta rekan-rekan pengembang sistem informasi yang telah memberikan dukungan data, waktu, dan masukan konstruktif selama proses perancangan, implementasi, dan pengujian sistem ini berlangsung.

---

## DAFTAR PUSTAKA
```
[1]  A. Kadir, Pengenalan Sistem Informasi Edisi Revisi, Yogyakarta: Andi Offset, 2014.
[2]  H. Al Fatta, Analisis dan Perancangan Sistem Informasi untuk Keunggulan Bersaing Perusahaan dan Organisasi Modern, Yogyakarta: Andi, 2007.
[3]  R. S. Pressman, Software Engineering: A Practitioner's Approach, 8th ed., New York: McGraw-Hill, 2015.
[4]  I. Sommerville, Software Engineering, 10th ed., Boston: Pearson Education, 2016.
[5]  J. Simarmata, Rekayasa Perangkat Lunak, Yogyakarta: Andi Publisher, 2010.
[6]  F. Wahid, "Dasar-Dasar Rekayasa Perangkat Lunak Berorientasi Objek," Jurnal Sistem Informasi, vol. 12, no. 1, pp. 45–52, 2018.
[7]  IEEE Computer Society, "IEEE Recommended Practice for Software Requirements Specifications," IEEE Std 830-1998, 1998.
[8]  ISO/IEC/IEEE, "Systems and software engineering — Life cycle processes — Requirements engineering," ISO/IEC/IEEE 29148:2018, 2018.
```
