-- ==============================================================================
-- SKRIP MIGRASI STRUKTUR BASIS DATA: UPGRADE HASH KATA SANDI KE BCRYPT
-- Sistem Informasi Monitoring Akademik Sekolah (SIAKAD)
-- Penulis: Bagus Budi Satoto (Web Developer) | Email: bagusbudi1308@gmail.com
-- ==============================================================================
-- Penjelasan:
-- Algoritma BCRYPT menghasilkan string hash sepanjang 60 karakter ($2y$10$...).
-- Skrip ini memperluas kapasitas kolom kata sandi menjadi VARCHAR(255) agar hash
-- tidak terpotong (truncated) dan mendukung standar keamanan modern.
-- ==============================================================================

-- 1. Tabel Administrator Utama
ALTER TABLE `tb_admin` 
  MODIFY `password` VARCHAR(255) NULL DEFAULT NULL 
  COMMENT 'Hash kata sandi administrator (BCRYPT / MD5 kompatibel)';

-- 2. Tabel Sekolah / Akun Instansi
ALTER TABLE `tb_sekolah` 
  MODIFY `password` VARCHAR(255) NULL DEFAULT NULL 
  COMMENT 'Hash kata sandi instansi sekolah (BCRYPT / MD5 kompatibel)';

-- 3. Tabel Karyawan & Guru (Kepala Sekolah, Guru BK, Tata Usaha, Wali Kelas)
ALTER TABLE `tb_karyawan` 
  MODIFY `password` VARCHAR(255) NULL DEFAULT NULL 
  COMMENT 'Hash kata sandi akun karyawan/guru (BCRYPT / MD5 kompatibel)';

-- 4. Tabel Siswa (Password Portal Mandiri Siswa & Password Portal Wali Murid)
ALTER TABLE `tb_siswa` 
  MODIFY `password_siswa` VARCHAR(255) NULL DEFAULT NULL 
  COMMENT 'Hash kata sandi login mandiri siswa (BCRYPT)',
  MODIFY `password_wali` VARCHAR(255) NULL DEFAULT NULL 
  COMMENT 'Hash kata sandi login pemantauan wali murid (BCRYPT)';

-- ==============================================================================
-- SELESAI. Silakan jalankan skrip ini melalui phpMyAdmin atau MySQL CLI.
-- ==============================================================================
