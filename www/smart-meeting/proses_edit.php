<?php
 
$koneksi = mysqli_connect("db_uts_app", "root", "password", "db_smart_meeting");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_peminjaman = $_POST['id_peminjaman'];
    $tanggal_rapat = $_POST['tanggal_rapat'];
    $jam_mulai     = $_POST['jam_mulai'];
    $jam_selesai   = $_POST['jam_selesai'];
    $id_ruangan    = $_POST['id_ruangan'];
    $id_karyawan   = $_POST['id_karyawan'];
    $agenda        = $_POST['agenda'];

    
    if ($jam_mulai >= $jam_selesai) {
        echo "<script>alert('Error: Jam mulai tidak boleh lebih besar atau sama dengan jam selesai!'); window.history.back();</script>";
        exit;
    }

    
    $query_cek = "SELECT p.*, k.devisi 
                  FROM peminjaman p
                  JOIN karyawan k ON p.id_karyawan = k.id_karyawan
                  WHERE p.id_ruangan = '$id_ruangan' 
                    AND p.tanggal = '$tanggal_rapat'
                    AND p.id_peminjaman != '$id_peminjaman'
                    AND '$jam_mulai' < p.jam_selesai 
                    AND '$jam_selesai' > p.jam_mulai";

    $result_cek = mysqli_query($koneksi, $query_cek);

    if (mysqli_num_rows($result_cek) > 0) {
        $data_bentrok = mysqli_fetch_assoc($result_cek);
        $divisi_bentrok = $data_bentrok['devisi'];
        $agenda_bentrok = $data_bentrok['agenda'];

        echo "<script>
                alert('Maaf, ruangan sudah digunakan oleh Divisi [$divisi_bentrok] untuk agenda [$agenda_bentrok]!');
                window.history.back();
              </script>";
        exit;
    }

     
    $query_update = "UPDATE peminjaman SET 
                        tanggal = '$tanggal_rapat', 
                        jam_mulai = '$jam_mulai', 
                        jam_selesai = '$jam_selesai', 
                        id_ruangan = '$id_ruangan', 
                        id_karyawan = '$id_karyawan', 
                        agenda = '$agenda' 
                     WHERE id_peminjaman = '$id_peminjaman'";
    
    if (mysqli_query($koneksi, $query_update)) {
        echo "<script>alert('Data peminjaman berhasil diperbarui!'); window.location.href = 'index.php';</script>";
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}
?>
