<?php
 
$koneksi = mysqli_connect("db", "root", "password", "db_smart_meeting");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_ruangan = $_POST['nama_ruangan'];
    $kapasitas    = $_POST['kapasitas'];
    $fasilitas    = $_POST['fasilitas'];

    $cek = mysqli_query($koneksi, "SELECT * FROM ruangan WHERE nama_ruangan = '$nama_ruangan'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Error: Ruangan $nama_ruangan sudah terdaftar sebelumnya!'); window.history.back();</script>";
        exit;
    }

    $query = "INSERT INTO ruangan (nama_ruangan, kapasitas, fasilitas) VALUES ('$nama_ruangan', '$kapasitas', '$fasilitas')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data Master Ruangan berhasil disimpan!'); window.location.href = 'index.php';</script>";
    }
}
?>
