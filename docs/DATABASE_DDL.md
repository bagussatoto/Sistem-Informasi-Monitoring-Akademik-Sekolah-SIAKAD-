# SPESIFIKASI DATA DEFINITION LANGUAGE (DDL) & KAMUS DATA
## Sistem Informasi Monitoring Akademik Sekolah (SIAKAD / SISMO)
**Basis Data: `sismo_smkit` | Engine: InnoDB | RDBMS: MySQL 5.7+ / MariaDB 10.3+**

---

### Informasi Dokumen
| Parameter | Keterangan |
|---|---|
| **Nama Basis Data** | `sismo_smkit` |
| **Penyusun / Author** | **Bagus Budi Satoto** |
| **Role** | **Web Developer / Database Engineer** |
| **Karakter Set Default** | `latin1` / `utf8mb4` |
| **Format Berkas Asli** | `database/sismo_smkit.sql` |
| **Tanggal Pembaruan** | 24 November 2026 |

---

## 1. Panduan Eksekusi DDL

### 1.1 Melalui Command Line Interface (CLI)
Pastikan layanan MySQL / MariaDB telah berjalan (misal via XAMPP atau Laragon):

```bash
# 1. Masuk ke direktori proyek
cd D:/Project/sistem/siakad/

# 2. Buat database baru jika belum ada
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS sismo_smkit;"

# 3. Impor seluruh struktur DDL dan data awal
mysql -u root -p sismo_smkit < database/sismo_smkit.sql
```

### 1.2 Melalui phpMyAdmin
1. Buka browser dan arahkan ke `http://localhost/phpmyadmin`.
2. Klik menu **New**, ketikkan nama database: `sismo_smkit`, lalu klik **Create**.
3. Pilih database `sismo_smkit`, buka tab **Import**.
4. Klik **Choose File**, arahkan ke `siakad/database/sismo_smkit.sql`.
5. Klik tombol **Go / Kirim** di bagian bawah.

---

## 2. Kamus Data Lengkap (Data Dictionary)

Sistem `sismo_smkit` memiliki 16 entitas tabel yang terorganisir secara modular:

### 2.1 `tb_sekolah` (Master Lembaga & Tenant)
Menyimpan identitas legal institusi sekolah dan menjadi induk relasi isolasi data multi-tenant.
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `npsn` | `INT(10)` | **PK** | No | Nomor Pokok Sekolah Nasional (Kode Legal) |
| `nss` | `VARCHAR(20)` | - | Yes | Nomor Statistik Sekolah |
| `nama_sekolah` | `VARCHAR(50)` | - | Yes | Nama resmi instansi sekolah |
| `jalan` | `VARCHAR(40)` | - | Yes | Alamat jalan lokasi sekolah |
| `desa` | `VARCHAR(10)` | - | Yes | Desa / Kelurahan |
| `kecamatan` | `VARCHAR(20)` | - | Yes | Kecamatan |
| `kota` | `VARCHAR(20)` | - | Yes | Kota / Kabupaten |
| `provinsi` | `VARCHAR(20)` | - | Yes | Provinsi |
| `telefon` | `VARCHAR(12)` | - | Yes | Nomor telepon sekolah |
| `email` | `VARCHAR(25)` | - | Yes | Email resmi sekolah |
| `kode_pos` | `VARCHAR(6)` | - | Yes | Kode pos |
| `tahun_berdiri`| `VARCHAR(4)` | - | Yes | Tahun pendirian institusi |
| `foto` | `LONGTEXT` | - | Yes | Logo / Foto gedung sekolah |
| `password` | `VARCHAR(50)` | - | Yes | Kata sandi login admin sekolah (MD5) |

---

### 2.2 `tb_admin` (Administrator Sistem Yayasan/Pusat)
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id_admin` | `VARCHAR(50)` | - | Yes | ID unik administrator |
| `nama_admin` | `VARCHAR(50)` | - | Yes | Nama lengkap admin utama |
| `password` | `VARCHAR(50)` | - | Yes | Sandi login administrator (MD5) |
| `foto` | `VARCHAR(50)` | - | Yes | Berkas foto profil admin |

---

### 2.3 `tb_karyawan` (Tenaga Pendidik & Kependidikan)
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `kode_guru` | `VARCHAR(4)` | **PK** | No | Kode identitas guru / pegawai |
| `nip` | `VARCHAR(20)` | - | No | Nomor Induk Pegawai |
| `npsn` | `INT(10)` | **FK** | No | Referensi ke `tb_sekolah.npsn` |
| `nama_karyawan`| `VARCHAR(50)` | - | Yes | Nama lengkap guru/staf |
| `jk` | `CHAR(1)` | - | Yes | Jenis Kelamin (`L`/`P`) |
| `tmp_lahir` | `VARCHAR(10)` | - | Yes | Tempat lahir |
| `tgl_lahir` | `VARCHAR(10)` | - | Yes | Tanggal lahir |
| `alamat` | `LONGTEXT` | - | Yes | Alamat tempat tinggal |
| `telefon` | `VARCHAR(12)` | - | Yes | Nomor telepon |
| `agama` | `VARCHAR(10)` | - | Yes | Agama |
| `status_kepegawaian`| `VARCHAR(20)` | - | Yes | Role (`Kepala Sekolah`, `Wali Kelas`, `BK`, `Tata Usaha`) |
| `pendidikan` | `VARCHAR(20)` | - | Yes | Tingkat pendidikan terakhir |
| `foto` | `LONGTEXT` | - | Yes | Berkas foto profil |
| `password` | `VARCHAR(50)` | - | Yes | Sandi login modul (MD5) |

---

### 2.4 `tb_siswa` (Buku Induk Kesiswaan)
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `nipd` | `VARCHAR(5)` | **PK** | No | Nomor Induk Peserta Didik (ID Siswa) |
| `nisn` | `VARCHAR(20)` | - | Yes | Nomor Induk Siswa Nasional |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |
| `nama_siswa` | `VARCHAR(50)` | - | Yes | Nama lengkap siswa |
| `jk` | `CHAR(1)` | - | Yes | Jenis Kelamin (`L`/`P`) |
| `tmp_lahir` | `VARCHAR(10)` | - | Yes | Tempat lahir |
| `tgl_lahir` | `VARCHAR(10)` | - | Yes | Tanggal lahir |
| `alamat` | `LONGTEXT` | - | Yes | Alamat lengkap domisili |
| `telefon` | `VARCHAR(12)` | - | Yes | Nomor kontak |
| `agama` | `VARCHAR(10)` | - | Yes | Agama |
| `negara` | `VARCHAR(20)` | - | Yes | Kewarganegaraan (WNI/WNA) |
| `nama_ayah` | `VARCHAR(20)` | - | Yes | Nama ayah kandung |
| `pekerjaan_ayah`| `VARCHAR(20)` | - | Yes | Pekerjaan ayah |
| `nama_ibu` | `VARCHAR(20)` | - | Yes | Nama ibu kandung |
| `pekerjaan_ibu`| `VARCHAR(20)` | - | Yes | Pekerjaan ibu |
| `nama_wali` | `VARCHAR(20)` | - | Yes | Nama wali murid (jika ada) |
| `pekerjaan_wali`| `VARCHAR(20)` | - | Yes | Pekerjaan wali |
| `anak_ke` | `CHAR(1)` | - | Yes | Urutan anak dalam keluarga |
| `id_kelas` | `VARCHAR(50)` | **FK** | Yes | Referensi ke `tb_kelas.id_kelas` |
| `id_jurusan` | `VARCHAR(20)` | **FK** | Yes | Referensi ke `tb_jurusan.id_jurusan` |
| `asal_sekolah` | `VARCHAR(20)` | - | Yes | Asal sekolah sebelumnya |
| `status_sekolah`| `VARCHAR(10)` | - | Yes | Status peserta didik (`Aktif`) |
| `foto` | `VARCHAR(100)`| - | Yes | Pasfoto siswa |
| `password_siswa`| `VARCHAR(20)`| - | Yes | Sandi login mandiri siswa (MD5) |
| `password_wali` | `VARCHAR(20)`| - | Yes | Sandi login pemantauan wali murid (MD5) |

---

### 2.5 `tb_jurusan` & `tb_kelas` (Master Rombel)
#### `tb_jurusan`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id_jurusan` | `VARCHAR(20)` | **PK** | No | Kode unik jurusan (misal: `20533660MIPA1`) |
| `nama_jurusan` | `VARCHAR(50)` | - | Yes | Nama program keahlian |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

#### `tb_kelas`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id_kelas` | `VARCHAR(50)` | **PK** | No | Kode unik ruang/tingkat kelas (misal: `KL1`) |
| `nama_kelas` | `VARCHAR(50)` | - | Yes | Nama kelas (misal: `X MIPA 1`) |

---

### 2.6 `tb_pelajaran` & `tb_jadwal` (Kurikulum & KBM)
#### `tb_pelajaran`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `kode_mapel` | `VARCHAR(12)` | **PK** | No | Kode mata pelajaran |
| `nama_mapel` | `VARCHAR(50)` | - | Yes | Nama mata pelajaran |
| `kategori` | `VARCHAR(20)` | - | Yes | Kelompok kurikulum |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

#### `tb_jadwal`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `kode_jadwal` | `VARCHAR(25)` | - | Yes | Kode unik jadwal |
| `kode_mapel` | `VARCHAR(12)` | **FK** | Yes | Referensi ke `tb_pelajaran.kode_mapel` |
| `jam_ke` | `CHAR(2)` | - | Yes | Jam pertemuan ke- |
| `jam_mulai` | `VARCHAR(5)` | - | Yes | Jam mulai KBM (`07:00`) |
| `jam_akhir` | `VARCHAR(5)` | - | Yes | Jam berakhir KBM (`09:00`) |
| `hari` | `VARCHAR(5)` | - | Yes | Hari KBM (`Senin`, `Selasa`, dst.) |
| `id_kelas` | `VARCHAR(50)` | **FK** | Yes | Referensi ke `tb_kelas.id_kelas` |
| `id_jurusan` | `VARCHAR(20)` | **FK** | Yes | Referensi ke `tb_jurusan.id_jurusan` |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

---

### 2.7 `tb_absensi` & `tb_nilai_akademik` (Presensi & E-Rapor)
#### `tb_absensi`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `kode_absensi` | `VARCHAR(50)` | **PK** | No | Kode unik log presensi |
| `nipd` | `VARCHAR(5)` | **FK** | Yes | Referensi ke `tb_siswa.nipd` |
| `kode_mapel` | `VARCHAR(12)` | **FK** | Yes | Referensi ke `tb_pelajaran.kode_mapel` |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |
| `id_kelas` | `VARCHAR(50)` | **FK** | Yes | Referensi ke `tb_kelas.id_kelas` |
| `id_jurusan` | `VARCHAR(20)` | **FK** | Yes | Referensi ke `tb_jurusan.id_jurusan` |
| `jam_mapel` | `VARCHAR(12)` | - | Yes | Jam mata pelajaran |
| `keterangan` | `CHAR(1)` | - | Yes | Status presensi (`H`/`S`/`I`/`A`) |
| `tanggal` | `VARCHAR(10)` | - | Yes | Tanggal presensi (`MM/DD/YY`) |

#### `tb_nilai_akademik`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id_nilai` | `VARCHAR(50)` | **PK** | No | Kode unik entri nilai |
| `id_kelas` | `VARCHAR(50)` | **FK** | Yes | Referensi ke `tb_kelas.id_kelas` |
| `nipd` | `VARCHAR(5)` | **FK** | Yes | Referensi ke `tb_siswa.nipd` |
| `kode_mapel` | `VARCHAR(12)` | **FK** | Yes | Referensi ke `tb_pelajaran.kode_mapel` |
| `tugas` | `VARCHAR(4)` | - | Yes | Nilai tugas harian |
| `uts` | `VARCHAR(4)` | - | Yes | Nilai Ujian Tengah Semester |
| `uas` | `VARCHAR(4)` | - | Yes | Nilai Ujian Akhir Semester |
| `nilai_akhir` | `VARCHAR(4)` | - | Yes | Kalkulasi kumulatif nilai akhir |
| `id_jurusan` | `VARCHAR(20)` | **FK** | Yes | Referensi ke `tb_jurusan.id_jurusan` |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

---

### 2.8 `tb_tagihan` & `tb_pembayaran` (Administrasi SPP)
#### `tb_tagihan`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id_tagihan` | `VARCHAR(20)` | **PK** | No | Kode pos tagihan |
| `nama_tagihan` | `VARCHAR(50)` | - | Yes | Nama tagihan (misal: `SPP Juli`) |
| `nominal_tagihan`| `INT(20)` | - | Yes | Besar tarif tagihan |
| `id_kelas` | `VARCHAR(50)` | **FK** | Yes | Alokasi tingkatan kelas |
| `keterangan` | `VARCHAR(20)` | - | Yes | Deskripsi tagihan |
| `tanggal_terbit`| `VARCHAR(10)` | - | Yes | Tanggal rilis tagihan |
| `tanggal_akhir` | `VARCHAR(10)` | - | Yes | Jatuh tempo pembayaran |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

#### `tb_pembayaran`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id_pembayaran`| `VARCHAR(50)` | **PK** | No | Kode unik transaksi kas |
| `id_tagihan` | `VARCHAR(20)` | **FK** | Yes | Referensi ke `tb_tagihan.id_tagihan` |
| `nipd` | `VARCHAR(5)` | **FK** | Yes | Referensi ke `tb_siswa.nipd` |
| `tgl_bayar` | `VARCHAR(10)` | - | Yes | Tanggal transaksi bayar |
| `jumlah_bayar` | `INT(20)` | - | Yes | Nominal uang yang disetor |
| `keterangan` | `VARCHAR(20)` | - | Yes | Status (`Lunas`/`Cicilan`) |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

---

### 2.9 `tb_konseling` & `tb_pelanggaran` (Bimbingan Konseling)
#### `tb_konseling`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id_konseling` | `VARCHAR(50)` | **PK** | No | Kode unik rekam konseling |
| `nipd` | `VARCHAR(5)` | **FK** | Yes | Referensi ke `tb_siswa.nipd` |
| `kode_guru` | `VARCHAR(4)` | **FK** | Yes | Referensi ke `tb_karyawan.kode_guru` (BK) |
| `kode_pelanggaran`| `VARCHAR(50)`| **FK** | Yes | Referensi ke `tb_pelanggaran` |
| `tanggal_konseling`| `VARCHAR(10)`| - | Yes | Tanggal pelaksanaan sesi BK |
| `masalah` | `LONGTEXT` | - | Yes | Uraian permasalahan siswa |
| `penyelesaian` | `LONGTEXT` | - | Yes | Tindak lanjut solusi pembinaan |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

#### `tb_pelanggaran`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `kode_pelanggaran`| `VARCHAR(50)`| **PK** | No | Kode jenis tata tertib |
| `nama_pelanggaran`| `VARCHAR(50)`| - | Yes | Deskripsi pelanggaran |
| `point` | `INT(10)` | - | Yes | Bobot poin sanksi |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

---

### 2.10 `tb_pesan` & `tb_info_sekolah` (Komunikasi & Informasi)
#### `tb_pesan`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id_pesan` | `VARCHAR(50)` | **PK** | No | Kode unik log pesan |
| `id_tujuan` | `VARCHAR(50)` | - | Yes | ID pengguna penerima |
| `isi_pesan` | `LONGTEXT` | - | Yes | Konten pesan / obrolan |
| `tanggal_pesan` | `VARCHAR(50)` | - | Yes | Waktu pengiriman pesan |
| `status_pesan` | `VARCHAR(20)` | - | Yes | Status baca (`Sudah Dibaca`/`Baru`) |
| `id_pengirim` | `VARCHAR(50)` | - | Yes | ID pengguna pengirim |
| `npsn` | `INT(10)` | **FK** | Yes | Referensi ke `tb_sekolah.npsn` |

#### `tb_info_sekolah`
| Kolom | Tipe Data | Kunci | Null | Keterangan |
|---|---|:---:|:---:|---|
| `id` | `VARCHAR(50)` | **PK** | No | Kode pengumuman |
| `npsn` | `INT(10)` | **FK** | No | Referensi ke `tb_sekolah.npsn` |
| `informasi` | `LONGTEXT` | - | Yes | Isi pengumuman resmi |
| `tanggal` | `VARCHAR(10)` | - | Yes | Tanggal publikasi info |
| `foto` | `LONGTEXT` | - | Yes | Lampiran gambar pengumuman |

---

## 3. Script SQL DDL Constraints (Integritas Relasional)

Kutipan DDL pendefinisian kunci utama (*Primary Key*) dan kunci tamu (*Foreign Key Constraints*):

```sql
-- Penambahan Primary Key
ALTER TABLE `tb_sekolah` ADD PRIMARY KEY (`npsn`);
ALTER TABLE `tb_karyawan` ADD PRIMARY KEY (`kode_guru`);
ALTER TABLE `tb_siswa` ADD PRIMARY KEY (`nipd`);
ALTER TABLE `tb_kelas` ADD PRIMARY KEY (`id_kelas`);
ALTER TABLE `tb_jurusan` ADD PRIMARY KEY (`id_jurusan`);
ALTER TABLE `tb_pelajaran` ADD PRIMARY KEY (`kode_mapel`);
ALTER TABLE `tb_absensi` ADD PRIMARY KEY (`kode_absensi`);
ALTER TABLE `tb_nilai_akademik` ADD PRIMARY KEY (`id_nilai`);
ALTER TABLE `tb_tagihan` ADD PRIMARY KEY (`id_tagihan`);
ALTER TABLE `tb_pembayaran` ADD PRIMARY KEY (`id_pembayaran`);
ALTER TABLE `tb_konseling` ADD PRIMARY KEY (`id_konseling`);
ALTER TABLE `tb_pelanggaran` ADD PRIMARY KEY (`kode_pelanggaran`);
ALTER TABLE `tb_pesan` ADD PRIMARY KEY (`id_pesan`);
ALTER TABLE `tb_info_sekolah` ADD PRIMARY KEY (`id`);

-- Penambahan Relasi Foreign Key Multi-Tenant & Kesiswaan
ALTER TABLE `tb_siswa`
  ADD CONSTRAINT `FK_tb_siswa_tb_sekolah` FOREIGN KEY (`npsn`) REFERENCES `tb_sekolah` (`npsn`),
  ADD CONSTRAINT `FK_tb_siswa_tb_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `tb_kelas` (`id_kelas`),
  ADD CONSTRAINT `FK_tb_siswa_tb_jurusan` FOREIGN KEY (`id_jurusan`) REFERENCES `tb_jurusan` (`id_jurusan`);

ALTER TABLE `tb_absensi`
  ADD CONSTRAINT `FK_tb_absensi_tb_siswa` FOREIGN KEY (`nipd`) REFERENCES `tb_siswa` (`nipd`),
  ADD CONSTRAINT `FK_tb_absensi_tb_pelajaran` FOREIGN KEY (`kode_mapel`) REFERENCES `tb_pelajaran` (`kode_mapel`),
  ADD CONSTRAINT `FK_tb_absensi_tb_sekolah` FOREIGN KEY (`npsn`) REFERENCES `tb_sekolah` (`npsn`);

ALTER TABLE `tb_pembayaran`
  ADD CONSTRAINT `FK_tb_pembayaran_tb_tagihan` FOREIGN KEY (`id_tagihan`) REFERENCES `tb_tagihan` (`id_tagihan`),
  ADD CONSTRAINT `FK_tb_pembayaran_tb_siswa` FOREIGN KEY (`nipd`) REFERENCES `tb_siswa` (`nipd`),
  ADD CONSTRAINT `FK_tb_pembayaran_tb_sekolah` FOREIGN KEY (`npsn`) REFERENCES `tb_sekolah` (`npsn`);

ALTER TABLE `tb_konseling`
  ADD CONSTRAINT `FK_tb_konseling_tb_siswa` FOREIGN KEY (`nipd`) REFERENCES `tb_siswa` (`nipd`),
  ADD CONSTRAINT `FK_tb_konseling_tb_karyawan` FOREIGN KEY (`kode_guru`) REFERENCES `tb_karyawan` (`kode_guru`),
  ADD CONSTRAINT `FK_tb_konseling_tb_pelanggaran` FOREIGN KEY (`kode_pelanggaran`) REFERENCES `tb_pelanggaran` (`kode_pelanggaran`),
  ADD CONSTRAINT `FK_tb_konseling_tb_sekolah` FOREIGN KEY (`npsn`) REFERENCES `tb_sekolah` (`npsn`);
```
