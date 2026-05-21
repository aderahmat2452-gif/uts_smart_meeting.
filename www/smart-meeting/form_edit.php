<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);


$koneksi = mysqli_connect('db', 'root', 'password', 'db_office_smart');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}


if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_booking = $_GET['id'];


$query_data = "SELECT b.*, k.nama_karyawan, k.devisi, r.nama_ruangan 
               FROM t_booking b
               JOIN m_karyawan k ON b.id_karyawan = k.id_karyawan
               JOIN r.id_ruangan = b.id_ruang -- Menyesuaikan nama kolom foreign key t_booking
               WHERE b.id_booking = '$id_booking'";

 
$query_data = "SELECT * FROM t_booking WHERE id_booking = '$id_booking'";
$res_data = mysqli_query($koneksi, $query_data);

if (mysqli_num_rows($res_data) === 0) {
    echo "<script>alert('Data booking tidak ditemukan!'); window.location.href='index.php';</script>";
    exit();
}

$data_edit = mysqli_fetch_assoc($res_data);

 
if (isset($_POST['proses_update_booking'])) {
    $tanggal_rapat = $_POST['tanggal_rapat'];
    $jam_mulai     = $_POST['jam_mulai'];
    $jam_selesai   = $_POST['jam_selesai'];
    $agenda        = $_POST['agenda'];

    
    $query_update = "UPDATE t_booking SET 
                        tanggal_ratat = '$tanggal_rapat', 
                        jam_mulai = '$jam_mulai', 
                        jam_selesai = '$jam_selesai', 
                        Agenda = '$agenda' 
                     WHERE id_booking = '$id_booking'";

    if (mysqli_query($koneksi, $query_update)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjaman Ruangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .card-edit { max-width: 550px; margin: 50px auto; border: none; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-network-wired me-2"></i>SMART-MEETING SYSTEM</a>
        </div>
    </nav>

    <div class="container">
        <div class="card card-edit bg-white p-4">
            <h4 class="fw-bold text-primary mb-1"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Peminjaman</h4>
            <p class="text-muted small border-bottom pb-2">Perbarui berkas jadwal reservasi rapat Anda</p>
            
            <form action="" method="POST">
                <input type="hidden" name="proses_update_booking" value="1">

                <div class="mb-3">
                    <label class="form-label fw-bold small">Tanggal Rapat</label>
                    <input type="date" name="tanggal_rapat" class="form-control" value="<?= $data_edit['tanggal_ratat']; ?>" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col">
                        <label class="form-label fw-bold small">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" value="<?= substr($data_edit['jam_mulai'], 0, 5); ?>" required>
                    </div>
                    <div class="col">
                        <label class="form-label fw-bold small">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" value="<?= substr($data_edit['jam_selesai'], 0, 5); ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small">Agenda Rapat</label>
                    <input type="text" name="agenda" class="form-control" value="<?= $data_edit['Agenda']; ?>" required>
                </div>
                
                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="index.php" class="btn btn-light border fw-semibold small">Batal</a>
                    <button type="submit" class="btn btn-warning text-white fw-semibold small px-4">Update Data</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>s
