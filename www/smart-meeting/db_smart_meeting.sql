-- Smart-Meeting MySQL schema
-- Run this script to create the database and required tables for the smart-meeting app.

CREATE DATABASE IF NOT EXISTS `db_smart_meeting`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;

USE `db_smart_meeting`;

CREATE TABLE IF NOT EXISTS `ruangan` (
  `id_ruangan` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_ruangan` VARCHAR(150) NOT NULL,
  `kapasitas` INT UNSIGNED NOT NULL DEFAULT 0,
  `fasilitas` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_ruangan`),
  UNIQUE KEY `unik_nama_ruangan` (`nama_ruangan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `karyawan` (
  `id_karyawan` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nik` VARCHAR(50) NOT NULL,
  `nama_karyawan` VARCHAR(150) NOT NULL,
  `devisi` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_karyawan`),
  UNIQUE KEY `unik_nik` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `peminjaman` (
  `id_peminjaman` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tanggal_rapat` DATE NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `id_ruangan` INT UNSIGNED NOT NULL,
  `id_karyawan` INT UNSIGNED NOT NULL,
  `agenda` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_peminjaman`),
  KEY `idx_peminjaman_ruangan` (`id_ruangan`),
  KEY `idx_peminjaman_karyawan` (`id_karyawan`),
  CONSTRAINT `fk_peminjaman_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_peminjaman_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `t_log_aktivitas` (
  `id_log` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_peminjaman` INT UNSIGNED NOT NULL,
  `aksi` VARCHAR(30) NOT NULL,
  `dihapus_oleh` VARCHAR(150) NOT NULL,
  `waktu_log` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `idx_log_peminjaman` (`id_peminjaman`),
  CONSTRAINT `fk_log_peminjaman` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
