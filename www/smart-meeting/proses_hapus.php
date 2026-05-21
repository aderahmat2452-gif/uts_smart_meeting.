<?php
 
$koneksi = mysqli_connect('db', 'root', 'password', 'db_office_smart');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

 
if (isset($_GET['id'])) {
    $id_booking = $_GET['id'];

    
    $query_info = "SELECT b.*, k.nama_karyawan FROM t_booking b 
                   JOIN m_karyawan k ON b.id_karyawan = k.id_karyawan 
                   WHERE b.id_booking = '$id_booking'";
    $res_info = mysqli_query($koneksi, $query_info);
    
    if (mysqli_num_rows($res_info) > 0) {
        $info = mysqli_fetch_assoc($res_info);
        
        $keterangan_log = "User " . $info['nama_karyawan'] . " membatalkan/menghapus booking pada tanggal " . $info['tanggal_ratat'] . " jam " . $info['jam_mulai'];
        
        
        mysqli_query($koneksi, "INSERT INTO t_log_aktivitas (keterangan) VALUES ('$keterangan_log')");
        
       
        $query_hapus = "DELETE FROM t_booking WHERE id_booking = '$id_booking'";
        mysqli_query($koneksi, $query_hapus);
    }
}

 
header("Location: index.php");
exit();
?>
