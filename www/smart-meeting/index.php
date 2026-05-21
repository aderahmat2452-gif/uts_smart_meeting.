 <?php
 
$koneksi = mysqli_connect('db', 'root', 'password', 'db_office_smart');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

 
$q_total_ruangan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM m_ruangan");
$r_total_ruangan = mysqli_fetch_assoc($q_total_ruangan)['total'];

$hari_ini = date('Y-m-d');
$q_ruangan_digunakan = mysqli_query($koneksi, "SELECT COUNT(DISTINCT id_ruang) as total FROM t_booking WHERE tanggal_ratat = '$hari_ini'");
$r_ruangan_digunakan = mysqli_fetch_assoc($q_ruangan_digunakan)['total'];

$r_ruangan_tersedia = $r_total_ruangan - $r_ruangan_digunakan;

 
$q_total_karyawan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM m_karyawan");
$r_total_karyawan = mysqli_fetch_assoc($q_total_karyawan)['total'];

$q_total_divisi = mysqli_query($koneksi, "SELECT COUNT(DISTINCT devisi) as total FROM m_karyawan");
$r_total_divisi = mysqli_fetch_assoc($q_total_divisi)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart-Meeting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
        .navbar-brand { font-weight: 700; letter-spacing: 0.5px; }
        
        .nav-tabs-custom { display: flex; gap: 8px; border-bottom: 2px solid #dee2e6; padding-bottom: 1px; margin-bottom: 25px; }
        .tab-link { padding: 10px 20px; border: none; background: transparent; color: #495057; font-weight: 600; font-size: 15px; border-bottom: 3px solid transparent; transition: all 0.2s; }
        .tab-link:hover { color: #0d6efd; }
        .tab-link.active { color: #0d6efd; border-bottom-color: #0d6efd; }

        .table-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #eef2f5; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1050; }
        .modal-content { background: white; padding: 25px; border-radius: 14px; width: 100%; max-width: 480px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: none; }
        .close-btn { position: absolute; top: 15px; right: 20px; cursor: pointer; font-size: 24px; color: #aaa; transition: color 0.2s; }
        .close-btn:hover { color: #333; }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: transform 0.2s; background: white; }
        .card-custom:hover { transform: translateY(-3px); }

        /* ==================== BARU: ANIMATED PRODUCT CARD STYLE (UNTUK RUANGAN) ==================== */
        .room-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 20px; 
            margin-bottom: 25px; 
        }

        .animated-card {
            position: relative;
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #eef2f5;
            transition: 0.5s;
        }

        
        .animated-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 120px;
            height: 120px;
            background: var(--clr, #0d6efd);
            border-radius: 50%;
            transition: 0.5s ease-in-out;
            transition-delay: 0s;
            z-index: 1;
        }

        
        .animated-card:hover::before {
            width: 100%;
            height: 100%;
            top: 0;
            right: 0;
            border-radius: 20px;
        }

       
        .card-body-content {
            position: relative;
            z-index: 3;
            transition: 0.5s;
        }

        .animated-card:hover .card-body-content {
            color: #fff;  
        }

        .animated-card:hover .text-primary,
        .animated-card:hover .text-muted {
            color: #fff !important;
        }

        
        .card-room-icon {
            position: absolute;
            bottom: -15px;
            right: -10px;
            font-size: 80px;
            color: rgba(13, 110, 253, 0.08); /* Warna pudar saat normal */
            z-index: 2;
            transition: 0.5s ease-in-out;
        }

       
        .animated-card:hover .card-room-icon {
            color: rgba(255, 255, 255, 0.25);
            transform: scale(1.3) rotate(-15deg) translate(-10px, -10px);
        }


        
        .dot-navigation-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
        }

        .dot-navigation {
            position: relative;
            width: 60px;
            height: 60px;
            background: #212529;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: 0.5s;
            transition-delay: 0.2s;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }

        .dot-navigation.active {
            width: 200px;
            height: 200px;
            background: #1a1d20;
            border-radius: 20px;
            transition-delay: 0s;
        }

        .dot-navigation span {
            position: absolute;
            width: 7px;
            height: 7px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.5s, width 0.5s, height 0.5s, background 0.5s;
            transition-delay: calc(0.04s * var(--i));
        }

        .dot-navigation.active span {
            width: 45px;
            height: 45px;
            background: #2b3035;
            border-radius: 10px;
            color: #fff;
            transform: translate(calc(55px * var(--x)), calc(55px * var(--y)));
        }

        .dot-navigation.active span.action-btn:hover { background: #0d6efd; color: #fff; }
        .dot-navigation.active span.nav-btn:hover { background: #ffc107; color: #000; }
        .dot-navigation.active span.util-btn:hover { background: #17a2b8; color: #fff; }

        .dot-navigation span i {
            font-size: 0px;
            transition: font-size 0.3s;
        }

        .dot-navigation.active span i {
            font-size: 18px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fa-solid fa-network-wired me-2"></i>SMART-MEETING SYSTEM</a>
        </div>
    </nav>

    <div class="container">
        <div class="nav-tabs-custom">
            <button class="tab-link active" onclick="switchTab('tab-peminjaman')"><i class="fa-solid fa-list-check me-2"></i>Data Peminjaman</button>
            <button class="tab-link" onclick="switchTab('tab-ruangan')"><i class="fa-solid fa-door-open me-2"></i>Data Ruangan</button>
            <button class="tab-link" onclick="switchTab('tab-karyawan')"><i class="fa-solid fa-users me-2"></i>Data Karyawan</button>
        </div>

        <div id="tab-peminjaman" class="tab-content active">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Dashboard Rapat</h2>
                    <p class="text-muted mb-0">Kelola reservasi dan ketersediaan ruang rapat harian</p>
                </div>
                <button class="btn btn-primary px-4 py-2 shadow-sm fw-semibold" onclick="toggleModal('modalPeminjaman', true)">
                    <i class="fa-solid fa-plus me-2"></i>Tambah Peminjaman
                </button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-custom border-start border-primary border-4 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold">Total Ruangan</span>
                                <h2 class="fw-bold text-primary mt-1 mb-0"><?= $r_total_ruangan; ?></h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle"><i class="fa-solid fa-building fa-xl"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom border-start border-success border-4 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold">Ruangan Tersedia Hari Ini</span>
                                <h2 class="fw-bold text-success mt-1 mb-0"><?= $r_ruangan_tersedia; ?></h2>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle"><i class="fa-solid fa-circle-check fa-xl"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom border-start border-danger border-4 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold">Digunakan Hari Ini</span>
                                <h2 class="fw-bold text-danger mt-1 mb-0"><?= $r_ruangan_digunakan; ?></h2>
                            </div>
                            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle"><i class="fa-solid fa-hourglass-split fa-xl"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-table-cells me-2"></i>Ketersediaan Ruangan (Animated Card CSS Grid)</h5>
            <div class="room-grid">
                <?php
                $q_ruang = mysqli_query($koneksi, "SELECT * FROM m_ruangan");
                $index_warna = 0;
                // Variasi warna lingkaran background pas di-hover (Biru, Hijau, Ungu, Orange, Merah)
                $warna_pilihan = ['#0d6efd', '#198754', '#6f42c1', '#fd7e14', '#dc3545'];
                
                while($room = mysqli_fetch_assoc($q_ruang)):
                    $clr = $warna_pilihan[$index_warna % count($warna_pilihan)];
                    $index_warna++;
                ?>
                    <div class="animated-card" style="--clr: <?= $clr; ?>;">
                        <div class="card-room-icon">
                            <i class="fa-solid fa-building-user"></i>
                        </div>
                        
                        <div class="card-body-content">
                            <h4 class="fw-bold text-primary mb-3"><i class="fa-regular fa-square-check me-2"></i><?= $room['nama_ruangan']; ?></h4>
                            <div class="small text-muted mb-2"><i class="fa-solid fa-users-viewfinder me-2"></i><strong>Kapasitas:</strong> <?= $room['kapasitas']; ?> Orang</div>
                            <div class="small text-muted"><i class="fa-solid fa-toolbox me-2"></i><strong>Fasilitas:</strong> <?= $room['fasilitas']; ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="p shadow-sm p-3 mb-3 bg-white rounded-3 d-flex align-items-center border border-light">
                <i class="fa-solid fa-filter text-muted me-2"></i>
                <label for="filter_tanggal" class="me-2 fw-semibold mb-0">Cari Tanggal:</label>
                <input type="date" id="filter_tanggal" onchange="filterTabelDinamis()" class="form-control form-control-sm w-auto">
            </div>

            <div class="table-container">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small fw-bold text-uppercase">
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Nama Ruangan</th>
                            <th>Nama Peminjam</th>
                            <th>Divisi</th>
                            <th>Agenda</th>
                            <th class="text-center" width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel_peminjaman">
                        <?php
                        $query_tabel = "SELECT p.*, r.nama_ruangan, k.nama_karyawan, k.devisi 
                                        FROM t_booking p
                                        JOIN m_ruangan r ON p.id_ruang = r.id_ruangan
                                        JOIN m_karyawan k ON p.id_karyawan = k.id_karyawan
                                        ORDER BY p.tanggal_ratat DESC, p.jam_mulai ASC";
                        $res_tabel = mysqli_query($koneksi, $query_tabel);
                        $no = 1;
                        while($row = mysqli_fetch_assoc($res_tabel)):
                        ?>
                            <tr data-tanggal="<?= $row['tanggal_ratat']; ?>">
                                <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                <td class="fw-semibold"><i class="fa-regular fa-calendar me-2 text-muted"></i><?= date('d-m-Y', strtotime($row['tanggal_ratat'])); ?></td>
                                <td><span class="badge bg-light text-dark border"><i class="fa-regular fa-clock text-primary me-1"></i><?= substr($row['jam_mulai'], 0, 5) . " - " . substr($row['jam_selesai'], 0, 5); ?></span></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1.5"><?= $row['nama_ruangan']; ?></span></td>
                                <td><?= $row['nama_karyawan']; ?></td>
                                <td><span class="text-muted small fw-medium"><?= $row['devisi']; ?></span></td>
                                <td class="fw-bold text-dark"><?= $row['Agenda']; ?></td>
                                <td class="text-center">
                                    <a href="form_edit.php?id=<?= $row['id_booking']; ?>" class="btn btn-sm btn-outline-warning me-1"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <button onclick="konfirmasiHapus(<?= $row['id_booking']; ?>)" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-ruangan" class="tab-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Master Ruangan</h2>
                    <p class="text-muted mb-0">Kelola daftar seluruh ruangan rapat korporat</p>
                </div>
                <button class="btn btn-primary px-4 py-2 shadow-sm fw-semibold" onclick="toggleModal('modalRuangan', true)">
                    <i class="fa-solid fa-plus me-2"></i>Tambah Ruangan
                </button>
            </div>
            
            <div class="table-container">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small fw-bold text-uppercase">
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Nama Ruangan</th>
                            <th>Kapasitas</th>
                            <th>Fasilitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $q_r = mysqli_query($koneksi, "SELECT * FROM m_ruangan ORDER BY nama_ruangan ASC");
                        while($row = mysqli_fetch_assoc($q_r)):
                        ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                            <td class="fw-bold text-primary"><?= $row['nama_ruangan']; ?></td>
                            <td><span class="badge bg-dark bg-opacity-10 text-dark"><i class="fa-solid fa-user-group me-2"></i><?= $row['kapasitas']; ?> Orang</span></td>
                            <td class="text-muted small"><i class="fa-solid fa-circle-info text-info me-2"></i><?= $row['fasilitas']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-karyawan" class="tab-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Master Karyawan</h2>
                    <p class="text-muted mb-0">Kelola database profil karyawan dan hak divisi</p>
                </div>
                <button class="btn btn-primary px-4 py-2 shadow-sm fw-semibold" onclick="toggleModal('modalKaryawan', true)">
                    <i class="fa-solid fa-user-plus me-2"></i>Tambah Karyawan
                </button>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card card-custom border-start border-primary border-4 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold">Total Karyawan</span>
                                <h4 class="fw-bold text-dark mt-1 mb-0"><?= $r_total_karyawan; ?> Orang</h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle"><i class="fa-solid fa-users fa-lg"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-custom border-start border-info border-4 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold">Jumlah Divisi</span>
                                <h4 class="fw-bold text-dark mt-1 mb-0"><?= $r_total_divisi; ?> Divisi</h4>
                            </div>
                            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle"><i class="fa-solid fa-sitemap fa-lg"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small fw-bold text-uppercase">
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Divisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $q_k = mysqli_query($koneksi, "SELECT * FROM m_karyawan ORDER BY nama_karyawan ASC");
                        while($row = mysqli_fetch_assoc($q_k)):
                        ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                            <td class="text-monospace text-muted small"><?= $row['nik']; ?></td>
                            <td class="fw-semibold text-dark"><?= $row['nama_karyawan']; ?></td>
                            <td><span class="badge bg-secondary px-2.5 py-1.5"><?= $row['devisi']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="dot-navigation-container">
        <div class="dot-navigation" id="toggleDotMenu" onclick="toggleDotMenu()">
            <span style="--i:0; --x:-1; --y:-1;" class="action-btn" onclick="toggleModal('modalPeminjaman', true)" title="Tambah Peminjaman">
                <i class="fa-solid fa-calendar-plus"></i>
            </span>
            <span style="--i:1; --x:0; --y:-1;" class="action-btn" onclick="toggleModal('modalRuangan', true)" title="Tambah Ruangan">
                <i class="fa-solid fa-door-open"></i>
            </span>
            <span style="--i:2; --x:1; --y:-1;" class="action-btn" onclick="toggleModal('modalKaryawan', true)" title="Tambah Karyawan">
                <i class="fa-solid fa-user-plus"></i>
            </span>
            
            <span style="--i:3; --x:-1; --y:0;" class="util-btn" onclick="setFilterHariIni()" title="Filter Hari Ini">
                <i class="fa-solid fa-calendar-day"></i>
            </span>
            <span style="--i:4; --x:0; --y:0;" class="util-btn" onclick="resetFilterTanggal()" title="Reset Filter Pencarian">
                <i class="fa-solid fa-arrow-rotate-left"></i>
            </span>
            <span style="--i:5; --x:1; --y:0;" class="util-btn" onclick="location.reload();" title="Refresh Halaman Web">
                <i class="fa-solid fa-sync-alt"></i>
            </span>
            
            <span style="--i:6; --x:-1; --y:1;" class="nav-btn" onclick="switchTab('tab-peminjaman')" title="Lihat Peminjaman">
                <i class="fa-solid fa-list-check"></i>
            </span>
            <span style="--i:7; --x:0; --y:1;" class="nav-btn" onclick="switchTab('tab-ruangan')" title="Lihat Ruangan">
                <i class="fa-solid fa-building"></i>
            </span>
            <span style="--i:8; --x:1; --y:1;" class="nav-btn" onclick="switchTab('tab-karyawan')" title="Lihat Karyawan">
                <i class="fa-solid fa-users"></i>
            </span>
        </div>
    </div>

    <div class="modal" id="modalPeminjaman">
        <div class="modal-content">
            <span class="close-btn" onclick="toggleModal('modalPeminjaman', false)">&times;</span>
            <h4 class="fw-bold text-primary mb-1"><i class="fa-solid fa-calendar-plus me-2"></i>Tambah Peminjaman</h4>
            <p class="text-muted small border-bottom pb-2">Masukkan berkas reservasi jadwal rapat</p>
            
            <form action="proses_tambah.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Tanggal Rapat</label>
                    <input type="date" name="tanggal_rapat" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col">
                        <label class="form-label fw-bold small">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>
                    <div class="col">
                        <label class="form-label fw-bold small">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nama Ruangan</label>
                    <select name="id_ruangan" class="form-select" required>
                        <option value="">-- Pilih Ruangan --</option>
                        <?php
                        $r_dropdown = mysqli_query($koneksi, "SELECT * FROM m_ruangan");
                        while($rd = mysqli_fetch_assoc($r_dropdown)) {
                            echo "<option value='".$rd['id_ruangan']."'>".$rd['nama_ruangan']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nama Peminjam</label>
                    <select name="id_karyawan" id="pilih_karyawan" onchange="isiDevisiOtomatis()" class="form-select" required>
                        <option value="">-- Pilih Karyawan --</option>
                        <?php
                        $k_dropdown = mysqli_query($koneksi, "SELECT * FROM m_karyawan");
                        while($kd = mysqli_fetch_assoc($k_dropdown)) {
                            echo "<option value='".$kd['id_karyawan']."' data-devisi='".$kd['devisi']."'>".$kd['nama_karyawan']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Devisi / Divisi</label>
                    <input type="text" id="input_devisi" disabled class="form-control bg-light text-muted fw-bold" placeholder="Terisi otomatis...">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small">Agenda Rapat</label>
                    <input type="text" name="agenda" class="form-control" placeholder="Contoh: Koordinasi Proyek UTS" required>
                </div>
                
                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <button type="button" class="btn btn-light border fw-semibold small" onclick="toggleModal('modalPeminjaman', false)">Batal</button>
                    <button type="submit" class="btn btn-primary fw-semibold small px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="modalRuangan">
        <div class="modal-content">
            <span class="close-btn" onclick="toggleModal('modalRuangan', false)">&times;</span>
            <h4 class="fw-bold text-primary mb-1">Tambah Ruangan</h4>
            <form action="proses_tambah_ruangan.php" method="POST">
                <div class="mb-3"><label class="form-label fw-bold small">Nama Ruangan</label><input type="text" name="nama_ruangan" class="form-control" required></div>
                <div class="mb-3"><label class="form-label fw-bold small">Kapasitas</label><input type="number" name="kapasitas" class="form-control" required></div>
                <div class="mb-4"><label class="form-label fw-bold small">Fasilitas</label><input type="text" name="fasilitas" class="form-control" required></div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold">Simpan Ruangan</button>
            </form>
        </div>
    </div>

    <div class="modal" id="modalKaryawan">
        <div class="modal-content">
            <span class="close-btn" onclick="toggleModal('modalKaryawan', false)">&times;</span>
            <h4 class="fw-bold text-primary mb-1">Tambah Karyawan</h4>
            <form action="proses_tambah_karyawan.php" method="POST">
                <div class="mb-3"><label class="form-label fw-bold small">NIK Karyawan</label><input type="text" name="nik" class="form-control" required></div>
                <div class="mb-3"><label class="form-label fw-bold small">Nama Lengkap</label><input type="text" name="nama_karyawan" class="form-control" required></div>
                <div class="mb-4"><label class="form-label fw-bold small">Devisi</label><input type="text" name="devisi" class="form-control" required></div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold">Simpan Karyawan</button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            var contents = document.getElementsByClassName('tab-content');
            for (var i = 0; i < contents.length; i++) contents[i].classList.remove('active');
            var links = document.getElementsByClassName('tab-link');
            for (var i = 0; i < links.length; i++) links[i].classList.remove('active');
            document.getElementById(tabId).classList.add('active');
            
            if(window.event && window.event.currentTarget && window.event.currentTarget.classList.contains('tab-link')){
                window.event.currentTarget.classList.add('active');
            } else {
                var targetBtn = document.querySelector(`button[onclick="switchTab('${tabId}')"]`);
                if(targetBtn) targetBtn.classList.add('active');
            }
        }

        function toggleModal(modalId, show) {
            document.getElementById(modalId).style.display = show ? 'flex' : 'none';
            if(show) {
                document.getElementById('toggleDotMenu').classList.remove('active');
            }
        }

        function toggleDotMenu() {
            if (event.target.tagName === 'SPAN' || event.target.tagName === 'I') {
                var menu = document.getElementById('toggleDotMenu');
                if(!menu.classList.contains('active')){
                    menu.classList.add('active');
                    event.stopPropagation();
                    return;
                }
                event.stopPropagation();
            } else {
                document.getElementById('toggleDotMenu').classList.toggle('active');
            }
        }

        document.addEventListener('click', function(e) {
            var menu = document.getElementById('toggleDotMenu');
            if (!menu.contains(e.target)) {
                menu.classList.remove('active');
            }
        });

        function setFilterHariIni() {
            var hariIni = "<?= date('Y-m-d'); ?>";
            document.getElementById("filter_tanggal").value = hariIni;
            filterTabelDinamis();
            switchTab('tab-peminjaman');
        }

        function resetFilterTanggal() {
            document.getElementById("filter_tanggal").value = "";
            filterTabelDinamis();
        }

        function isiDevisiOtomatis() {
            var selectKaryawan = document.getElementById("pilih_karyawan");
            var inputDevisi = document.getElementById("input_devisi");
            var selectedOption = selectKaryawan.options[selectKaryawan.selectedIndex];
            var devisi = selectedOption.getAttribute("data-devisi");
            inputDevisi.value = devisi ? devisi : "";
        }

        function filterTabelDinamis() {
            var inputTanggal = document.getElementById("filter_tanggal").value;
            var tabelBody = document.getElementById("tabel_peminjaman");
            var barisTabel = tabelBody.getElementsByTagName("tr");

            for (var i = 0; i < barisTabel.length; i++) {
                var tanggalBaris = barisTabel[i].getAttribute("data-tanggal");
                barisTabel[i].style.display = (inputTanggal === "" || tanggalBaris === inputTanggal) ? "" : "none";
            }
        }

        function konfirmasiHapus(id) {
            var kode = prompt("Anda yakin menghapus data peminjaman ini? \nMasukkan kode: HAPUS");
            if (kode === "HAPUS") {
                window.location.href = "proses_hapus.php?id=" + id;
            } else if (kode !== null) {
                alert("Kode salah! Data batal dihapus.");
            }
        }
    </script>
</body>
</html>
