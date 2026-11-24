-- =============================================================================
-- SISTEM INFORMASI MONITORING AKADEMIK SEKOLAH (SIAKAD / SISMO)
-- FILE: triggers_and_functions.sql
-- AUTHOR: Bagus Budi Satoto (Web Developer)
-- DATABASE: sismo_smkit
-- RDBMS: MySQL 5.7+ / MariaDB 10.3+
-- DESKRIPSI: Paket Stored Functions & Triggers untuk otomasi integritas data
-- =============================================================================

USE `sismo_smkit`;

-- Drop triggers jika sudah ada
DROP TRIGGER IF EXISTS `trg_hitung_nilai_akhir_insert`;
DROP TRIGGER IF EXISTS `trg_hitung_nilai_akhir_update`;
DROP TRIGGER IF EXISTS `trg_update_status_tagihan_insert`;
DROP TRIGGER IF EXISTS `trg_update_status_tagihan_delete`;

-- Drop functions jika sudah ada
DROP FUNCTION IF EXISTS `fn_konversi_predikat`;
DROP FUNCTION IF EXISTS `fn_status_kelulusan`;
DROP FUNCTION IF EXISTS `fn_total_poin_pelanggaran`;
DROP FUNCTION IF EXISTS `fn_hitung_kehadiran`;
DROP FUNCTION IF EXISTS `fn_persentase_kehadiran`;

DELIMITER $$

-- =============================================================================
-- 1. STORED FUNCTIONS
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Function 1: fn_konversi_predikat
-- Mengonversi nilai akhir angka ke predikat huruf (A, B, C, D, E)
-- Contoh: SELECT fn_konversi_predikat(88); -> 'A'
-- -----------------------------------------------------------------------------
CREATE FUNCTION `fn_konversi_predikat` (
    `p_nilai` VARCHAR(10)
) 
RETURNS VARCHAR(2)
DETERMINISTIC
BEGIN
    DECLARE v_angka DECIMAL(5,2);
    DECLARE v_predikat VARCHAR(2);

    SET v_angka = COALESCE(CAST(`p_nilai` AS DECIMAL(5,2)), 0);

    IF v_angka >= 85.00 THEN
        SET v_predikat = 'A';
    ELSEIF v_angka >= 75.00 THEN
        SET v_predikat = 'B';
    ELSEIF v_angka >= 60.00 THEN
        SET v_predikat = 'C';
    ELSEIF v_angka >= 50.00 THEN
        SET v_predikat = 'D';
    ELSE
        SET v_predikat = 'E';
    END IF;

    RETURN v_predikat;
END$$

-- -----------------------------------------------------------------------------
-- Function 2: fn_status_kelulusan
-- Mengecek status kelulusan mata pelajaran terhadap standar KKM (Kriteria Ketuntasan Minimal)
-- Contoh: SELECT fn_status_kelulusan(80, 75); -> 'Tuntas'
-- -----------------------------------------------------------------------------
CREATE FUNCTION `fn_status_kelulusan` (
    `p_nilai` VARCHAR(10),
    `p_kkm` INT
) 
RETURNS VARCHAR(15)
DETERMINISTIC
BEGIN
    DECLARE v_angka DECIMAL(5,2);
    DECLARE v_kkm INT;
    DECLARE v_status VARCHAR(15);

    SET v_angka = COALESCE(CAST(`p_nilai` AS DECIMAL(5,2)), 0);
    SET v_kkm = COALESCE(`p_kkm`, 75);

    IF v_angka >= v_kkm THEN
        SET v_status = 'Tuntas';
    ELSE
        SET v_status = 'Remedial';
    END IF;

    RETURN v_status;
END$$

-- -----------------------------------------------------------------------------
-- Function 3: fn_total_poin_pelanggaran
-- Menghitung akumulasi total poin sanksi pelanggaran seorang siswa
-- Contoh: SELECT fn_total_poin_pelanggaran('17494');
-- -----------------------------------------------------------------------------
CREATE FUNCTION `fn_total_poin_pelanggaran` (
    `p_nipd` VARCHAR(5)
) 
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_total INT;

    SELECT COALESCE(SUM(p.`point`), 0) INTO v_total
    FROM `tb_konseling` k
    INNER JOIN `tb_pelanggaran` p ON k.`kode_pelanggaran` = p.`kode_pelanggaran`
    WHERE k.`nipd` = `p_nipd`;

    RETURN v_total;
END$$

-- -----------------------------------------------------------------------------
-- Function 4: fn_hitung_kehadiran
-- Menghitung frekuensi kehadiran siswa berdasarkan status (H/S/I/A)
-- Contoh: SELECT fn_hitung_kehadiran('17494', 'H');
-- -----------------------------------------------------------------------------
CREATE FUNCTION `fn_hitung_kehadiran` (
    `p_nipd` VARCHAR(5),
    `p_status` CHAR(1)
) 
RETURNS INT
READS SQL DATA
BEGIN
    DECLARE v_jumlah INT;

    SELECT COUNT(*) INTO v_jumlah
    FROM `tb_absensi`
    WHERE `nipd` = `p_nipd` AND `keterangan` = `p_status`;

    RETURN v_jumlah;
END$$

-- -----------------------------------------------------------------------------
-- Function 5: fn_persentase_kehadiran
-- Menghitung persentase tingkat kehadiran siswa (Hadir / Total Absensi * 100)
-- Contoh: SELECT fn_persentase_kehadiran('17494'); -> 95.00
-- -----------------------------------------------------------------------------
CREATE FUNCTION `fn_persentase_kehadiran` (
    `p_nipd` VARCHAR(5)
) 
RETURNS DECIMAL(5,2)
READS SQL DATA
BEGIN
    DECLARE v_hadir INT;
    DECLARE v_total INT;
    DECLARE v_persen DECIMAL(5,2);

    SELECT COUNT(*) INTO v_hadir
    FROM `tb_absensi`
    WHERE `nipd` = `p_nipd` AND (`keterangan` = 'H' OR `keterangan` = 'M');

    SELECT COUNT(*) INTO v_total
    FROM `tb_absensi`
    WHERE `nipd` = `p_nipd`;

    IF v_total > 0 THEN
        SET v_persen = ROUND((v_hadir / v_total) * 100, 2);
    ELSE
        SET v_persen = 0.00;
    END IF;

    RETURN v_persen;
END$$

-- =============================================================================
-- 2. TRIGGERS
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Trigger 1: trg_hitung_nilai_akhir_insert
-- Otomatis menghitung kolom nilai_akhir saat data nilai diinput (BEFORE INSERT)
-- Bobot: 30% Tugas + 30% UTS + 40% UAS
-- -----------------------------------------------------------------------------
CREATE TRIGGER `trg_hitung_nilai_akhir_insert`
BEFORE INSERT ON `tb_nilai_akademik`
FOR EACH ROW
BEGIN
    DECLARE v_tugas DECIMAL(5,2);
    DECLARE v_uts DECIMAL(5,2);
    DECLARE v_uas DECIMAL(5,2);

    SET v_tugas = COALESCE(CAST(NEW.`tugas` AS DECIMAL(5,2)), 0);
    SET v_uts   = COALESCE(CAST(NEW.`uts` AS DECIMAL(5,2)), 0);
    SET v_uas   = COALESCE(CAST(NEW.`uas` AS DECIMAL(5,2)), 0);

    -- Hitung nilai akhir kumulatif otomatis
    SET NEW.`nilai_akhir` = CAST(ROUND((v_tugas * 0.3) + (v_uts * 0.3) + (v_uas * 0.4), 0) AS CHAR);
END$$

-- -----------------------------------------------------------------------------
-- Trigger 2: trg_hitung_nilai_akhir_update
-- Otomatis memperbarui nilai_akhir saat data nilai diubah (BEFORE UPDATE)
-- -----------------------------------------------------------------------------
CREATE TRIGGER `trg_hitung_nilai_akhir_update`
BEFORE UPDATE ON `tb_nilai_akademik`
FOR EACH ROW
BEGIN
    DECLARE v_tugas DECIMAL(5,2);
    DECLARE v_uts DECIMAL(5,2);
    DECLARE v_uas DECIMAL(5,2);

    SET v_tugas = COALESCE(CAST(NEW.`tugas` AS DECIMAL(5,2)), 0);
    SET v_uts   = COALESCE(CAST(NEW.`uts` AS DECIMAL(5,2)), 0);
    SET v_uas   = COALESCE(CAST(NEW.`uas` AS DECIMAL(5,2)), 0);

    SET NEW.`nilai_akhir` = CAST(ROUND((v_tugas * 0.3) + (v_uts * 0.3) + (v_uas * 0.4), 0) AS CHAR);
END$$

-- -----------------------------------------------------------------------------
-- Trigger 3: trg_update_status_tagihan_insert
-- Otomatis memperbarui status pembayaran di tb_tagihan (AFTER INSERT pada tb_pembayaran)
-- -----------------------------------------------------------------------------
CREATE TRIGGER `trg_update_status_tagihan_insert`
AFTER INSERT ON `tb_pembayaran`
FOR EACH ROW
BEGIN
    DECLARE v_total_setor INT;
    DECLARE v_nominal_tagihan INT;

    -- Hitung total pembayaran untuk tagihan ini oleh siswa bersangkutan
    SELECT COALESCE(SUM(`jumlah_bayar`), 0) INTO v_total_setor
    FROM `tb_pembayaran`
    WHERE `id_tagihan` = NEW.`id_tagihan` AND `nipd` = NEW.`nipd`;

    -- Ambil besar tagihan
    SELECT COALESCE(`nominal_tagihan`, 0) INTO v_nominal_tagihan
    FROM `tb_tagihan`
    WHERE `id_tagihan` = NEW.`id_tagihan`;

    -- Update status keterangan di tb_tagihan
    IF v_total_setor >= v_nominal_tagihan AND v_nominal_tagihan > 0 THEN
        UPDATE `tb_tagihan` 
        SET `keterangan` = 'Lunas' 
        WHERE `id_tagihan` = NEW.`id_tagihan`;
    ELSE
        UPDATE `tb_tagihan` 
        SET `keterangan` = 'Menunggak' 
        WHERE `id_tagihan` = NEW.`id_tagihan`;
    END IF;
END$$

-- -----------------------------------------------------------------------------
-- Trigger 4: trg_update_status_tagihan_delete
-- Menghitung ulang status pembayaran jika ada data transaksi pembayaran yang dihapus
-- -----------------------------------------------------------------------------
CREATE TRIGGER `trg_update_status_tagihan_delete`
AFTER DELETE ON `tb_pembayaran`
FOR EACH ROW
BEGIN
    DECLARE v_total_setor INT;
    DECLARE v_nominal_tagihan INT;

    SELECT COALESCE(SUM(`jumlah_bayar`), 0) INTO v_total_setor
    FROM `tb_pembayaran`
    WHERE `id_tagihan` = OLD.`id_tagihan` AND `nipd` = OLD.`nipd`;

    SELECT COALESCE(`nominal_tagihan`, 0) INTO v_nominal_tagihan
    FROM `tb_tagihan`
    WHERE `id_tagihan` = OLD.`id_tagihan`;

    IF v_total_setor >= v_nominal_tagihan AND v_nominal_tagihan > 0 THEN
        UPDATE `tb_tagihan` 
        SET `keterangan` = 'Lunas' 
        WHERE `id_tagihan` = OLD.`id_tagihan`;
    ELSE
        UPDATE `tb_tagihan` 
        SET `keterangan` = 'Menunggak' 
        WHERE `id_tagihan` = OLD.`id_tagihan`;
    END IF;
END$$

DELIMITER ;

-- =============================================================================
-- 3. CONTOH QUERY VERIFIKASI & PENGUJIAN
-- =============================================================================
-- 1. Cek predikat nilai:
--    SELECT nipd, kode_mapel, nilai_akhir, fn_konversi_predikat(nilai_akhir) AS predikat,
--           fn_status_kelulusan(nilai_akhir, 75) AS status FROM tb_nilai_akademik LIMIT 5;
--
-- 2. Cek total poin pelanggaran siswa:
--    SELECT nipd, nama_siswa, fn_total_poin_pelanggaran(nipd) AS total_poin FROM tb_siswa LIMIT 5;
--
-- 3. Cek persentase absensi kehadiran:
--    SELECT nipd, nama_siswa, fn_persentase_kehadiran(nipd) AS persen_kehadiran FROM tb_siswa LIMIT 5;
-- =============================================================================
