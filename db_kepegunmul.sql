-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table db_kepegunmul.jabatans
DROP TABLE IF EXISTS `jabatans`;
CREATE TABLE IF NOT EXISTS `jabatans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jenis_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.jabatans: ~13 rows (approximately)
INSERT INTO `jabatans` (`id`, `jenis_pegawai`, `jenis_jabatan`, `jabatan`, `created_at`, `updated_at`) VALUES
	(1, 'Dosen', 'Dosen Fungsional', 'Asisten Ahli', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(2, 'Dosen', 'Dosen Fungsional', 'Lektor', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(3, 'Dosen', 'Dosen Fungsional', 'Lektor Kepala', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(4, 'Dosen', 'Dosen Fungsional', 'Profesor', '2025-08-06 02:11:29', '2025-08-06 02:11:29');

-- Dumping structure for table db_kepegunmul.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.migrations: ~0 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2025_07_24_222253_create_unit_kerjas_table', 1),
	(2, '2025_07_25_012556_create_sub_unit_kerjas_table', 1),
	(3, '2025_07_25_100346_create_sub_sub_unit_kerjas_table', 1),
	(4, '2025_07_25_231137_create_pangkats_table', 1),
	(5, '2025_07_26_001145_create_jabatans_table', 1),
	(6, '2025_07_28_231905_create_pegawais_table', 1),
	(7, '2025_08_02_221132_create_periode_usulans_table', 1),
	(8, '2025_08_02_221252_create_usulans_table', 1),
	(9, '2025_08_02_221739_create_usulan_penilai_jabatan_table', 1),
	(10, '2025_08_02_221833_create_usulan_logs_table', 1),
	(11, '2025_08_05_025424_create_usulan_dokumens_table', 1),
	(12, '2025_08_05_150128_create_permission_tables', 1),
	(13, '2025_08_05_233150_add_jenis_usulan_to_periode_usulans_table', 1),
	(14, '2025_08_05_233459_add_perbaikan_dates_to_periode_usulans_table', 1);

-- Dumping structure for table db_kepegunmul.model_has_permissions
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.model_has_permissions: ~0 rows (approximately)

-- Dumping structure for table db_kepegunmul.model_has_roles
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.model_has_roles: ~7 rows (approximately)
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(5, 'App\\Models\\BackendUnivUsulan\\Pegawai', 1),
	(5, 'App\\Models\\BackendUnivUsulan\\Pegawai', 2),
	(1, 'App\\Models\\BackendUnivUsulan\\Pegawai', 3),
	(2, 'App\\Models\\BackendUnivUsulan\\Pegawai', 3),
	(3, 'App\\Models\\BackendUnivUsulan\\Pegawai', 3),
	(4, 'App\\Models\\BackendUnivUsulan\\Pegawai', 3),
	(5, 'App\\Models\\BackendUnivUsulan\\Pegawai', 3);

-- Dumping structure for table db_kepegunmul.pangkats
DROP TABLE IF EXISTS `pangkats`;
CREATE TABLE IF NOT EXISTS `pangkats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pangkat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pangkats_pangkat_unique` (`pangkat`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.pangkats: ~12 rows (approximately)
INSERT INTO `pangkats` (`id`, `pangkat`, `created_at`, `updated_at`) VALUES
	(1, 'Juru Muda (I/a)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(2, 'Juru Muda Tingkat I (I/b)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(3, 'Juru Tingkat I (I/d)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(4, 'Penata Muda (III/a)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(5, 'Penata Muda Tingkat I (III/b)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(6, 'Penata (III/c)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(7, 'Penata Tingkat I (III/d)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(8, 'Pembina (IV/a)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(9, 'Pembina Tingkat I (IV/b)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(10, 'Pembina Utama Muda (IV/c)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(11, 'Pembina Utama Madya (IV/d)', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(12, 'Pembina Utama (IV/e)', '2025-08-06 02:11:29', '2025-08-06 02:11:29');

-- Dumping structure for table db_kepegunmul.pegawais
DROP TABLE IF EXISTS `pegawais`;
CREATE TABLE IF NOT EXISTS `pegawais` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pangkat_terakhir_id` bigint unsigned NOT NULL,
  `jabatan_terakhir_id` bigint unsigned NOT NULL,
  `unit_kerja_terakhir_id` bigint unsigned NOT NULL,
  `jenis_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_kepegawaian` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nuptk` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gelar_depan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gelar_belakang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_kartu_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_handphone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tmt_cpns` date DEFAULT NULL,
  `sk_cpns` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_pns` date DEFAULT NULL,
  `sk_pns` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_pangkat` date DEFAULT NULL,
  `sk_pangkat_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tmt_jabatan` date DEFAULT NULL,
  `sk_jabatan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ijazah_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transkrip_nilai_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sk_penyetaraan_ijazah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disertasi_thesis_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mata_kuliah_diampu` text COLLATE utf8mb4_unicode_ci,
  `ranting_ilmu_kepakaran` text COLLATE utf8mb4_unicode_ci,
  `url_profil_sinta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predikat_kinerja_tahun_pertama` enum('Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang','Perlu Perbaikan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skp_tahun_pertama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `predikat_kinerja_tahun_kedua` enum('Sangat Baik','Baik','Cukup','Kurang','Sangat Kurang','Perlu Perbaikan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skp_tahun_kedua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nilai_konversi` double DEFAULT NULL,
  `pak_konversi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pegawais_nip_unique` (`nip`),
  UNIQUE KEY `pegawais_email_unique` (`email`),
  KEY `pegawais_pangkat_terakhir_id_foreign` (`pangkat_terakhir_id`),
  KEY `pegawais_jabatan_terakhir_id_foreign` (`jabatan_terakhir_id`),
  KEY `pegawais_unit_kerja_terakhir_id_foreign` (`unit_kerja_terakhir_id`),
  CONSTRAINT `pegawais_jabatan_terakhir_id_foreign` FOREIGN KEY (`jabatan_terakhir_id`) REFERENCES `jabatans` (`id`),
  CONSTRAINT `pegawais_pangkat_terakhir_id_foreign` FOREIGN KEY (`pangkat_terakhir_id`) REFERENCES `pangkats` (`id`),
  CONSTRAINT `pegawais_unit_kerja_terakhir_id_foreign` FOREIGN KEY (`unit_kerja_terakhir_id`) REFERENCES `sub_sub_unit_kerjas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.pegawais: ~3 rows (approximately)
INSERT INTO `pegawais` (`id`, `pangkat_terakhir_id`, `jabatan_terakhir_id`, `unit_kerja_terakhir_id`, `jenis_pegawai`, `status_kepegawaian`, `nip`, `nuptk`, `gelar_depan`, `nama_lengkap`, `gelar_belakang`, `email`, `password`, `nomor_kartu_pegawai`, `foto`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `nomor_handphone`, `tmt_cpns`, `sk_cpns`, `tmt_pns`, `sk_pns`, `tmt_pangkat`, `sk_pangkat_terakhir`, `tmt_jabatan`, `sk_jabatan_terakhir`, `pendidikan_terakhir`, `ijazah_terakhir`, `transkrip_nilai_terakhir`, `sk_penyetaraan_ijazah`, `disertasi_thesis_terakhir`, `mata_kuliah_diampu`, `ranting_ilmu_kepakaran`, `url_profil_sinta`, `predikat_kinerja_tahun_pertama`, `skp_tahun_pertama`, `predikat_kinerja_tahun_kedua`, `skp_tahun_kedua`, `nilai_konversi`, `pak_konversi`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 'Dosen', 'PNS', '199001012015011001', NULL, NULL, 'Budi Santoso', NULL, 'budi.santoso@example.com', '$2y$12$4ubQRgJOuSgXZqb0Z126t.AFlR7tYLffPq3SgnlvMVuLaY4SpjfL.', NULL, NULL, 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', NULL, NULL, NULL, NULL, '2025-08-06', 'path/to/dummy.pdf', '2025-08-06', 'path/to/dummy.pdf', 'Sarjana (S1)', 'path/to/dummy.pdf', 'path/to/dummy.pdf', NULL, NULL, NULL, NULL, NULL, 'Baik', 'path/to/dummy.pdf', 'Baik', 'path/to/dummy.pdf', NULL, NULL, '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(2, 1, 1, 1, 'Dosen', 'PNS', '199202022016022002', NULL, NULL, 'Citra Lestari', NULL, 'citra.lestari@example.com', '$2y$12$wxLuuFpoo4P7yLDXgd9Yauubr03kzfQbsCVnOyDbUzDcNJswyic0C', NULL, NULL, 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', NULL, NULL, NULL, NULL, '2025-08-06', 'path/to/dummy.pdf', '2025-08-06', 'path/to/dummy.pdf', 'Sarjana (S1)', 'path/to/dummy.pdf', 'path/to/dummy.pdf', NULL, NULL, NULL, NULL, NULL, 'Baik', 'path/to/dummy.pdf', 'Baik', 'path/to/dummy.pdf', NULL, NULL, '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(3, 5, 1, 1, 'Dosen', 'Dosen PNS', '199405242024061001', NULL, NULL, 'Muhammad Rivani Ibrahim', 'S.Kom, M.Kom', 'admin.fakultas@kepegawaian.com', '$2y$12$Fanna5OrZMUTo//24eYfaeIg9GlkCkEDBOgPQrq/lqQ.bH1fRh/su', '123456857452314525', 'pegawai-files/foto/S5FpWHzspfnbsnXYZr6HGslkvCsUi7It7fLwYM1U.png', 'Samarinda', '1990-01-01', 'Laki-Laki', '081234567890', '2025-08-01', NULL, '2025-08-01', NULL, '2025-08-06', 'path/to/dummy.pdf', '2025-08-01', 'path/to/dummy.pdf', 'Magister (S2) / Sederajat', 'path/to/dummy.pdf', 'path/to/dummy.pdf', NULL, NULL, 'Bisnis Digital', 'Bisnis Digital', 'https://sinta.kemdikbud.go.id/', 'Baik', 'path/to/dummy.pdf', 'Baik', 'path/to/dummy.pdf', NULL, NULL, '2025-08-06 02:11:30', '2025-08-06 04:51:16');

-- Dumping structure for table db_kepegunmul.periode_usulans
DROP TABLE IF EXISTS `periode_usulans`;
CREATE TABLE IF NOT EXISTS `periode_usulans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_periode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_usulan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_periode` year NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `tanggal_mulai_perbaikan` date DEFAULT NULL,
  `tanggal_selesai_perbaikan` date DEFAULT NULL,
  `status` enum('Buka','Tutup') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tutup',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.periode_usulans: ~0 rows (approximately)
INSERT INTO `periode_usulans` (`id`, `nama_periode`, `jenis_usulan`, `tahun_periode`, `tanggal_mulai`, `tanggal_selesai`, `tanggal_mulai_perbaikan`, `tanggal_selesai_perbaikan`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Gelombang 1', 'jabatan', '2025', '2025-08-01', '2025-08-31', '2025-09-01', '2025-09-30', 'Buka', '2025-08-06 02:13:15', '2025-08-06 02:13:15');

-- Dumping structure for table db_kepegunmul.permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.permissions: ~0 rows (approximately)

-- Dumping structure for table db_kepegunmul.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.roles: ~5 rows (approximately)
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'Admin Universitas Usulan', 'pegawai', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(2, 'Admin Universitas', 'pegawai', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(3, 'Admin Fakultas', 'pegawai', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(4, 'Penilai Universitas', 'pegawai', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(5, 'Pegawai Unmul', 'pegawai', '2025-08-06 02:11:29', '2025-08-06 02:11:29');

-- Dumping structure for table db_kepegunmul.role_has_permissions
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.role_has_permissions: ~0 rows (approximately)

-- Dumping structure for table db_kepegunmul.sub_sub_unit_kerjas
DROP TABLE IF EXISTS `sub_sub_unit_kerjas`;
CREATE TABLE IF NOT EXISTS `sub_sub_unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sub_unit_kerja_id` bigint unsigned NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_sub_unit_kerjas_sub_unit_kerja_id_foreign` (`sub_unit_kerja_id`),
  CONSTRAINT `sub_sub_unit_kerjas_sub_unit_kerja_id_foreign` FOREIGN KEY (`sub_unit_kerja_id`) REFERENCES `sub_unit_kerjas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.sub_sub_unit_kerjas: ~3 rows (approximately)
INSERT INTO `sub_sub_unit_kerjas` (`id`, `sub_unit_kerja_id`, `nama`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Prodi Rekayasa Perangkat Lunak', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(2, 1, 'Prodi Jaringan Komputer', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(3, 2, 'Prodi Struktur Bangunan', '2025-08-06 02:11:29', '2025-08-06 02:11:29');

-- Dumping structure for table db_kepegunmul.sub_unit_kerjas
DROP TABLE IF EXISTS `sub_unit_kerjas`;
CREATE TABLE IF NOT EXISTS `sub_unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unit_kerja_id` bigint unsigned NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_unit_kerjas_unit_kerja_id_foreign` (`unit_kerja_id`),
  CONSTRAINT `sub_unit_kerjas_unit_kerja_id_foreign` FOREIGN KEY (`unit_kerja_id`) REFERENCES `unit_kerjas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.sub_unit_kerjas: ~4 rows (approximately)
INSERT INTO `sub_unit_kerjas` (`id`, `unit_kerja_id`, `nama`, `created_at`, `updated_at`) VALUES
	(1, 1, 'S1 Informatika', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(2, 1, 'S1 Teknik Sipil', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(3, 2, 'S1 Manajemen', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(4, 2, 'S1 Akuntansi', '2025-08-06 02:11:29', '2025-08-06 02:11:29');

-- Dumping structure for table db_kepegunmul.unit_kerjas
DROP TABLE IF EXISTS `unit_kerjas`;
CREATE TABLE IF NOT EXISTS `unit_kerjas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_kerjas_nama_unique` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.unit_kerjas: ~0 rows (approximately)
INSERT INTO `unit_kerjas` (`id`, `nama`, `created_at`, `updated_at`) VALUES
	(1, 'Fakultas Teknik', '2025-08-06 02:11:29', '2025-08-06 02:11:29'),
	(2, 'Fakultas Ekonomi dan Bisnis', '2025-08-06 02:11:29', '2025-08-06 02:11:29');

-- Dumping structure for table db_kepegunmul.usulans
DROP TABLE IF EXISTS `usulans`;
CREATE TABLE IF NOT EXISTS `usulans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pegawai_id` bigint unsigned NOT NULL,
  `periode_usulan_id` bigint unsigned NOT NULL,
  `jenis_usulan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan_lama_id` bigint unsigned DEFAULT NULL,
  `jabatan_tujuan_id` bigint unsigned DEFAULT NULL,
  `status_usulan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `data_usulan` json DEFAULT NULL,
  `catatan_verifikator` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulans_pegawai_id_foreign` (`pegawai_id`),
  KEY `usulans_periode_usulan_id_foreign` (`periode_usulan_id`),
  KEY `usulans_jabatan_lama_id_foreign` (`jabatan_lama_id`),
  KEY `usulans_jabatan_tujuan_id_foreign` (`jabatan_tujuan_id`),
  CONSTRAINT `usulans_jabatan_lama_id_foreign` FOREIGN KEY (`jabatan_lama_id`) REFERENCES `jabatans` (`id`),
  CONSTRAINT `usulans_jabatan_tujuan_id_foreign` FOREIGN KEY (`jabatan_tujuan_id`) REFERENCES `jabatans` (`id`),
  CONSTRAINT `usulans_pegawai_id_foreign` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usulans_periode_usulan_id_foreign` FOREIGN KEY (`periode_usulan_id`) REFERENCES `periode_usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.usulans: ~0 rows (approximately)

-- Dumping structure for table db_kepegunmul.usulan_dokumens
DROP TABLE IF EXISTS `usulan_dokumens`;
CREATE TABLE IF NOT EXISTS `usulan_dokumens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `diupload_oleh_id` bigint unsigned NOT NULL,
  `nama_dokumen` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_dokumens_usulan_id_created_at_index` (`usulan_id`,`created_at`),
  KEY `usulan_dokumens_diupload_oleh_id_index` (`diupload_oleh_id`),
  CONSTRAINT `usulan_dokumens_diupload_oleh_id_foreign` FOREIGN KEY (`diupload_oleh_id`) REFERENCES `pegawais` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `usulan_dokumens_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.usulan_dokumens: ~0 rows (approximately)

-- Dumping structure for table db_kepegunmul.usulan_logs
DROP TABLE IF EXISTS `usulan_logs`;
CREATE TABLE IF NOT EXISTS `usulan_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `status_sebelumnya` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_baru` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `dilakukan_oleh_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_logs_usulan_id_foreign` (`usulan_id`),
  KEY `usulan_logs_dilakukan_oleh_id_foreign` (`dilakukan_oleh_id`),
  CONSTRAINT `usulan_logs_dilakukan_oleh_id_foreign` FOREIGN KEY (`dilakukan_oleh_id`) REFERENCES `pegawais` (`id`),
  CONSTRAINT `usulan_logs_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.usulan_logs: ~0 rows (approximately)

-- Dumping structure for table db_kepegunmul.usulan_penilai
DROP TABLE IF EXISTS `usulan_penilai`;
CREATE TABLE IF NOT EXISTS `usulan_penilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usulan_id` bigint unsigned NOT NULL,
  `penilai_id` bigint unsigned NOT NULL,
  `status_penilaian` enum('Belum Dinilai','Sesuai','Perlu Perbaikan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Dinilai',
  `catatan_penilaian` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usulan_penilai_usulan_id_foreign` (`usulan_id`),
  KEY `usulan_penilai_penilai_id_foreign` (`penilai_id`),
  CONSTRAINT `usulan_penilai_penilai_id_foreign` FOREIGN KEY (`penilai_id`) REFERENCES `pegawais` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usulan_penilai_usulan_id_foreign` FOREIGN KEY (`usulan_id`) REFERENCES `usulans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table db_kepegunmul.usulan_penilai: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
