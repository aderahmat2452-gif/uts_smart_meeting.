<?php
// proses_hapus.php
// PERBAIKAN: Host wajib 'db', password 'password', dan database 'db_office_smart'
$koneksi = mysqli_connect('db', 'root', 'password', 'db_office_smart');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek apakah ada parameter ID yang dikirim untuk dihapus
if (isset($_GET['id'])) {
    $id_booking = $_GET['id'];

    // 1. Ambil informasi booking terlebih dahulu untuk dicatat ke t_log_aktivitas (Kriteria Soal UTS)
    $query_info = "SELECT b.*, k.nama_karyawan FROM t_booking b 
                   JOIN m_karyawan k ON b.id_karyawan = k.id_karyawan 
                   WHERE b.id_booking = '$id_booking'";
    $res_info = mysqli_query($koneksi, $query_info);
    
    if (mysqli_num_rows($res_info) > 0) {
        $info = mysqli_fetch_assoc($res_info);
        // Format log sesuai yang diminta di lembar soal
        $keterangan_log = "User " . $info['nama_karyawan'] . " membatalkan/menghapus booking pada tanggal " . $info['tanggal_ratat'] . " jam " . $info['jam_mulai'];
        
        // 2. Masukkan catatan ke tabel t_log_aktivitas
        mysqli_query($koneksi, "INSERT INTO t_log_aktivitas (keterangan) VALUES ('$keterangan_log')");
        
        // 3. Jalankan perintah hapus data di tabel utama t_booking
        $query_hapus = "DELETE FROM t_booking WHERE id_booking = '$id_booking'";
        mysqli_query($koneksi, $query_hapus);
    }
}

// Setelah selesai menghapus, lempar kembali halaman ke dashboard utama
header("Location: index.php");
exit();
?>