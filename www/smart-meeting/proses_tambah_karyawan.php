<?php
 
$koneksi = mysqli_connect("db", "root", "password", "db_smart_meeting");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik           = $_POST['nik'];
    $nama_karyawan = $_POST['nama_karyawan'];
    $devisi        = $_POST['devisi'];

    $cek = mysqli_query($koneksi, "SELECT * FROM karyawan WHERE nik = '$nik'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Error: Karyawan dengan NIK [$nik] sudah terdaftar dalam sistem!'); window.history.back();</script>";
        exit;
    }

    $query = "INSERT INTO karyawan (nik, nama_karyawan, devisi) VALUES ('$nik', '$nama_karyawan', '$devisi')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data Master Karyawan berhasil disimpan!'); window.location.href = 'index.php';</script>";
    }
}
?>
