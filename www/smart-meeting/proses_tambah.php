<?php
 
$koneksi = mysqli_connect('db', 'root', 'password', 'db_office_smart');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $tanggal_rapat = $_POST['tanggal_rapat'];
    $jam_mulai     = $_POST['jam_mulai'];
    $jam_selesai   = $_POST['jam_selesai'];
    $id_ruangan    = $_POST['id_ruangan'];
    $id_karyawan   = $_POST['id_karyawan'];
    $agenda        = $_POST['agenda'];
    $status_default = "Pinjam"; // Status default sesuai ketentuan soal

     
    if ($jam_mulai >= $jam_selesai) {
        echo "<script>
                alert('Maaf, jam mulai rapat tidak boleh melewati atau sama dengan jam selesai!');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    
    $query_cek = "SELECT b.*, k.devisi FROM t_booking b
                  JOIN m_karyawan k ON b.id_karyawan = k.id_karyawan
                  WHERE b.id_ruang = '$id_ruangan' 
                    AND b.tanggal_ratat = '$tanggal_rapat'
                    AND (
                        ('$jam_mulai' >= b.jam_mulai AND '$jam_mulai' < b.jam_selesai) OR 
                        ('$jam_selesai' > b.jam_mulai AND '$jam_selesai' <= b.jam_selesai) OR
                        (b.jam_mulai >= '$jam_mulai' AND b.jam_mulai < '$jam_selesai')
                    )";
    
    $res_cek = mysqli_query($koneksi, $query_cek);

    if (mysqli_num_rows($res_cek) > 0) {
        $data_bentrok = mysqli_fetch_assoc($res_cek);
       
        echo "<script>
                alert('Maaf, ruangan sudah digunakan oleh Divisi [" . $data_bentrok['devisi'] . "] untuk agenda [" . $data_bentrok['Agenda'] . "]!');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    
    $query_insert = "INSERT INTO t_booking (id_karyawan, id_ruang, tanggal_ratat, jam_mulai, jam_selesai, Agenda, Status) 
                     VALUES ('$id_karyawan', '$id_ruangan', '$tanggal_rapat', '$jam_mulai', '$jam_selesai', '$agenda', '$status_default')";

    if (mysqli_query($koneksi, $query_insert)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}
?>
