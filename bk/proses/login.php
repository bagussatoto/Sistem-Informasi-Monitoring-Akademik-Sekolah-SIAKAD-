<?php 
include_once("koneksi.php");
session_start();
session_unset();
session_destroy();
session_start();

$kode         = isset($_POST['no_id']) ? trim($_POST['no_id']) : '';
$raw_password = isset($_POST['password']) ? (string)$_POST['password'] : '';

if (empty($kode) || empty($raw_password)) {
    echo "
    <script>
      alert('Mohon isi ID Pengguna dan Kata Sandi!');
      window.location = '../../';
    </script>
    ";
    exit;
}

/**
 * Verifikasi kata sandi dengan dukungan multi-layer:
 * 1. BCRYPT (Standar keamanan modern)
 * 2. MD5 32-karakter (Backward-compatibility sistem lama)
 * 3. MD5 20-karakter (Truncated hash dari skema lama tb_siswa)
 * 4. Plain-text (Kompatibilitas data demo basis data awal)
 * 
 * Jika autentikasi berhasil melalui layer lama, otomatis re-hash ke BCRYPT.
 * Penulis: Bagus Budi Satoto (Web Developer) | Email: bagusbudi1308@gmail.com
 */
function verify_and_rehash($raw_password, $stored_hash, $update_callback = null) {
    if (empty($stored_hash)) {
        return false;
    }
    // 1. Cek BCRYPT native
    if (password_verify($raw_password, $stored_hash)) {
        if (password_needs_rehash($stored_hash, PASSWORD_BCRYPT) && is_callable($update_callback)) {
            $new_hash = password_hash($raw_password, PASSWORD_BCRYPT);
            call_user_func($update_callback, $new_hash);
        }
        return true;
    }
    // 2. Cek backward-compatibility MD5 32 karakter
    if (md5($raw_password) === $stored_hash) {
        if (is_callable($update_callback)) {
            $new_hash = password_hash($raw_password, PASSWORD_BCRYPT);
            call_user_func($update_callback, $new_hash);
        }
        return true;
    }
    // 3. Cek backward-compatibility MD5 terpotong 20 karakter
    if (substr(md5($raw_password), 0, 20) === $stored_hash) {
        if (is_callable($update_callback)) {
            $new_hash = password_hash($raw_password, PASSWORD_BCRYPT);
            call_user_func($update_callback, $new_hash);
        }
        return true;
    }
    // 4. Cek backward-compatibility plain-text
    if ($raw_password === $stored_hash) {
        if (is_callable($update_callback)) {
            $new_hash = password_hash($raw_password, PASSWORD_BCRYPT);
            call_user_func($update_callback, $new_hash);
        }
        return true;
    }
    return false;
}

$kode_safe = mysqli_real_escape_string($conn, $kode);

// 1. Verifikasi Admin Sekolah (tb_sekolah)
$sql_sekolah = mysqli_query($conn, "SELECT * FROM tb_sekolah WHERE npsn = '$kode_safe'");
if ($sql_sekolah && mysqli_num_rows($sql_sekolah) > 0) {
    $data_sekolah = mysqli_fetch_array($sql_sekolah);
    if (verify_and_rehash($raw_password, $data_sekolah['password'], function($new_hash) use ($conn, $data_sekolah) {
        $clean_hash = mysqli_real_escape_string($conn, $new_hash);
        mysqli_query($conn, "UPDATE tb_sekolah SET password = '$clean_hash' WHERE npsn = '".$data_sekolah['npsn']."'");
    })) {
        $_SESSION['npsn'] = $kode;
        $_SESSION['nama_login'] = $data_sekolah['nama_sekolah'];
        echo "
        <script>
          alert('Login Berhasil');
          window.location = '../';
        </script>
        ";
        exit;
    }
}

// 2. Verifikasi Karyawan (Kepala Sekolah, BK, Tata Usaha, Wali Kelas)
$sql_karyawan = mysqli_query($conn, "SELECT * FROM tb_karyawan WHERE kode_guru = '$kode_safe'");
if ($sql_karyawan && mysqli_num_rows($sql_karyawan) > 0) {
    $data_karyawan = mysqli_fetch_array($sql_karyawan);
    if (verify_and_rehash($raw_password, $data_karyawan['password'], function($new_hash) use ($conn, $data_karyawan) {
        $clean_hash = mysqli_real_escape_string($conn, $new_hash);
        mysqli_query($conn, "UPDATE tb_karyawan SET password = '$clean_hash' WHERE kode_guru = '".$data_karyawan['kode_guru']."'");
    })) {
        $_SESSION['kode_guru'] = $kode;
        $_SESSION['nama_login'] = $data_karyawan['nama_karyawan'];
        $_SESSION['npsn']  = $data_karyawan['npsn'];

        $role = $data_karyawan['status_kepegawaian'];
        if ($role == "Kepala Sekolah") {
            echo "
            <script>
              alert('Login Berhasil');
              window.location = '../../kepala_sekolah';
            </script>
            ";
        } elseif ($role == "BK") {
            echo "
            <script>
              alert('Login Berhasil');
              window.location = '../../bk/';
            </script>
            ";
        } elseif ($role == "Tata Usaha") {
            echo "
            <script>
              alert('Login Berhasil');
              window.location = '../../tata_usaha/';
            </script>
            ";
        } elseif ($role == "Wali Kelas") {
            echo "
            <script>
              alert('Login Berhasil');
              window.location = '../../wali_kelas/';
            </script>
            ";
        } else {
            echo "
            <script>
              alert('Login Berhasil');
              window.location = '../../';
            </script>
            ";
        }
        exit;
    }
}

// 3. Verifikasi Siswa / Wali Murid (tb_siswa)
$sql_siswa = mysqli_query($conn, "SELECT * FROM tb_siswa WHERE nipd = '$kode_safe'");
if ($sql_siswa && mysqli_num_rows($sql_siswa) > 0) {
    $data_siswa = mysqli_fetch_array($sql_siswa);

    // Cek password wali murid
    if (verify_and_rehash($raw_password, $data_siswa['password_wali'], function($new_hash) use ($conn, $data_siswa) {
        $clean_hash = mysqli_real_escape_string($conn, $new_hash);
        mysqli_query($conn, "UPDATE tb_siswa SET password_wali = '$clean_hash' WHERE nipd = '".$data_siswa['nipd']."'");
    })) {
        $_SESSION['nipd'] = $data_siswa['nipd'];
        $_SESSION['nama_login'] = $data_siswa['nama_siswa'];
        $_SESSION['npsn']  = $data_siswa['npsn'];
        header("location:../../wali_murid/");
        exit;
    }

    // Cek password siswa mandiri
    if (verify_and_rehash($raw_password, $data_siswa['password_siswa'], function($new_hash) use ($conn, $data_siswa) {
        $clean_hash = mysqli_real_escape_string($conn, $new_hash);
        mysqli_query($conn, "UPDATE tb_siswa SET password_siswa = '$clean_hash' WHERE nipd = '".$data_siswa['nipd']."'");
    })) {
        $_SESSION['nipd'] = $data_siswa['nipd'];
        $_SESSION['nama_login'] = $data_siswa['nama_siswa'];
        $_SESSION['npsn']  = $data_siswa['npsn'];
        header("location:../../siswa/");
        exit;
    }
}

// 4. Verifikasi Administrator Utama (tb_admin)
$sql_admin = mysqli_query($conn, "SELECT * FROM tb_admin WHERE nama_admin = '$kode_safe'");
if ($sql_admin && mysqli_num_rows($sql_admin) > 0) {
    $data_admin = mysqli_fetch_array($sql_admin);
    if (verify_and_rehash($raw_password, $data_admin['password'], function($new_hash) use ($conn, $data_admin) {
        $clean_hash = mysqli_real_escape_string($conn, $new_hash);
        mysqli_query($conn, "UPDATE tb_admin SET password = '$clean_hash' WHERE id_admin = '".$data_admin['id_admin']."'");
    })) {
        $_SESSION['nama_login'] = $data_admin['nama_admin'];
        $_SESSION['data_foto']  = $data_admin['foto'];
        header("location:../../admin_utama/");
        exit;
    }
}

// Jika autentikasi gagal
echo "
<script>
  alert('Login Gagal, ID Pengguna atau Password Tidak Sesuai!');
  window.location = '../../';
</script>
";
?>
