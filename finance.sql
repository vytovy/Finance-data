Enter password: 
/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.0-MariaDB, for Android (aarch64)
--
-- Host: localhost    Database: finance
-- ------------------------------------------------------
-- Server version	11.8.0-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `barang`
--

DROP TABLE IF EXISTS `barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `nama_toko` varchar(255) NOT NULL,
  `alamat_toko` text NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `harga_barang` decimal(10,2) NOT NULL,
  `status` enum('Dimiliki','Belum Dimiliki') NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barang`
--

LOCK TABLES `barang` WRITE;
/*!40000 ALTER TABLE `barang` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `barang` VALUES
(5,'2025-02-12','Mr Diy, toko perkakas dll','Pasar wisma, bigmall, jl Hidayatullah cek disini','Sprayer busa',0.00,'Belum Dimiliki','Belum ada ngecek. Harga barang tolong segera dicek',NULL),
(6,'2025-02-12','Delta auto parts, Mr Diy','Dekat Mesra indah, bung tomo, pasar wisma','Wiper kaca mobil',0.00,'Belum Dimiliki','Cek harga dan cari yang cocok',NULL),
(7,'2025-02-12','Bengkel kilo 2, Mr Diy wisma','Kilo 2 loa Janan, pasar wisma','brake cleaner',0.00,'Belum Dimiliki','Untuk service part mobil segera di beli, brake cleane',NULL),
(8,'2025-02-12','Online','Online','Foam cleaner mobil',0.00,'Belum Dimiliki','lihat di online shop atau di mr Diy bigmall',NULL),
(9,'2025-02-12','Sumber mas','Jl pasar pagi seberang pelabuhan','Alat Polish body mobil',360000.00,'Belum Dimiliki','Segera beli untuk Polish mobil',NULL);
/*!40000 ALTER TABLE `barang` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `daily_balance`
--

DROP TABLE IF EXISTS `daily_balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `waktu` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_balance`
--

LOCK TABLES `daily_balance` WRITE;
/*!40000 ALTER TABLE `daily_balance` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `daily_balance` VALUES
(1,'2025-02-05',350000.00,'2025-02-05 12:14:08'),
(2,'2025-02-06',415000.00,'2025-02-06 00:32:36'),
(6,'2025-02-09',549124.00,'2025-02-09 14:22:06');
/*!40000 ALTER TABLE `daily_balance` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `debts_loans`
--

DROP TABLE IF EXISTS `debts_loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debts_loans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tipe` enum('hutang','pinjaman') NOT NULL,
  `status` enum('lunas','belum lunas') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debts_loans`
--

LOCK TABLES `debts_loans` WRITE;
/*!40000 ALTER TABLE `debts_loans` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `debts_loans` VALUES
(1,'2025-02-12','13:54:00',200000.00,'Hutang ke Bu Ifah','hutang','belum lunas'),
(3,'2025-01-31','17:02:00',140000.00,'Hutang cs Bu Dani belum dibayar bulan Januari','pinjaman','belum lunas');
/*!40000 ALTER TABLE `debts_loans` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `expense_tags`
--

DROP TABLE IF EXISTS `expense_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_tag` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_tags`
--

LOCK TABLES `expense_tags` WRITE;
/*!40000 ALTER TABLE `expense_tags` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `expense_tags` VALUES
(1,'Belanja','2025-02-05 12:31:46'),
(2,'Bensin','2025-02-05 12:31:52'),
(4,'Tol','2025-02-05 13:30:34'),
(5,'Hutang ke Bu Ifah kec km4','2025-02-06 00:33:08'),
(6,'Kembalian','2025-02-06 09:34:04'),
(7,'Kasih pinjaman ke mas','2025-02-06 10:59:27'),
(8,'Bayar parkir','2025-02-06 14:50:19'),
(9,'Cicilan perbulan mobil kk','2025-02-07 14:13:11'),
(10,'Tabungan Rencana Mandiri','2025-02-07 14:22:22'),
(11,'Hilang','2025-02-07 14:26:28'),
(12,'Perawatan mobil','2025-02-08 03:42:00'),
(13,'Biaya bulanan','2025-02-12 00:33:18');
/*!40000 ALTER TABLE `expense_tags` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `tipe` enum('pemasukan','pengeluaran','pinjaman','bayar pinjaman','beri pinjaman') NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `expense_tags` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `transactions` VALUES
(7,'2025-02-06','08:00:00','pinjaman',200000.00,'Bu ifah',NULL,'2025-02-06 03:23:39'),
(8,'2025-02-06','09:00:00','pengeluaran',200000.00,'Bensin untuk ke BPN hari Jumat 7 feb 2025',2,'2025-02-06 03:24:30'),
(9,'2025-02-06','16:00:00','pemasukan',100000.00,'Dibayar mas antar dari loa bakung',NULL,'2025-02-06 08:08:05'),
(10,'2025-02-06','16:33:00','pemasukan',52000.00,'Bayaran Bu ifah',NULL,'2025-02-06 09:33:38'),
(11,'2025-02-06','16:34:00','pengeluaran',10000.00,'Bu Ifah kembalian',6,'2025-02-06 09:34:32'),
(13,'2025-02-06','22:49:00','pemasukan',100000.00,'Dibayar mas waktu malam',NULL,'2025-02-06 14:50:00'),
(14,'2025-02-06','22:50:00','pengeluaran',3000.00,'Parkir pak ndur bandar Samarinda jl juanda',8,'2025-02-06 14:50:59'),
(15,'2025-02-07','07:43:00','pemasukan',42000.00,'Bayar ke kcmtn Bu ifah',NULL,'2025-02-06 23:44:14'),
(16,'2025-02-07','10:04:00','pengeluaran',300000.00,'Isi saldo tol',4,'2025-02-07 02:04:23'),
(17,'2025-02-07','14:01:00','pemasukan',800000.00,'Lunas bayar Carter ibram',NULL,'2025-02-07 06:01:31'),
(18,'2025-02-07','22:14:00','bayar pinjaman',300000.00,'Bayar ke kakak bulanan hanya 300 ribu bulan ini karena banyak bayar pajak dan services mobil',9,'2025-02-07 14:14:22'),
(19,'2025-02-07','21:55:00','pengeluaran',30000.00,'Beli nasgor 2 bungkus',1,'2025-02-07 14:15:32'),
(20,'2025-02-07','14:40:00','pemasukan',20000.00,'Tambahan parkir di bandara',NULL,'2025-02-07 14:17:50'),
(21,'2025-02-07','22:22:00','pengeluaran',100000.00,'Bayar cicilan Tahunan mandiri 100.000 bulan Januari yang kurang',10,'2025-02-07 14:23:47'),
(22,'2025-02-07','22:26:00','pengeluaran',2000.00,'Hilang belum nemu',11,'2025-02-07 14:27:10'),
(23,'2025-02-08','10:42:00','pengeluaran',160000.00,'Beli kunci pembuka filter oli universal',12,'2025-02-08 03:42:36'),
(24,'2025-02-08','00:42:00','pengeluaran',100000.00,'Tabungan mandiri rencana bulanan',10,'2025-02-08 03:43:21'),
(25,'2025-02-08','16:19:00','pengeluaran',33000.00,'Belanja makanan di jessica TF mandiri',1,'2025-02-08 08:20:24'),
(26,'2025-02-08','16:53:00','pengeluaran',15000.00,'Belanja tempe mendoan di jessica',1,'2025-02-08 08:53:39'),
(27,'2025-02-08','20:59:00','pengeluaran',5000.00,'Parki solaria',8,'2025-02-08 12:59:44'),
(28,'2025-02-09','07:44:00','pemasukan',50000.00,'Antar Bu Ifah ke pasar baqa',NULL,'2025-02-09 00:45:16'),
(30,'2025-02-09','22:14:00','pengeluaran',120000.00,'Beli paket data seluler tri 28 hari, habis tanggal 8 maret 2025',1,'2025-02-09 14:14:51'),
(31,'2025-02-10','07:58:00','pemasukan',30000.00,'Antar Bu Ifah ke kantor',NULL,'2025-02-09 23:58:49'),
(32,'2025-02-08','20:59:00','pengeluaran',5000.00,'Parki solaria',8,'2025-02-10 02:39:10'),
(33,'2025-02-10','08:39:00','pengeluaran',25000.00,'Beli sarapan',1,'2025-02-10 02:39:48'),
(34,'2025-02-10','07:55:00','pengeluaran',80000.00,'Bensin',2,'2025-02-10 02:41:20'),
(35,'2025-02-10','20:50:00','pengeluaran',24000.00,'Belanja sabun di eramart',1,'2025-02-10 13:18:21'),
(36,'2025-02-10','21:36:00','pengeluaran',1000.00,'Hilang',11,'2025-02-10 13:36:36'),
(37,'2025-02-11','08:03:00','pengeluaran',10000.00,'Sarapan mie merah',1,'2025-02-11 02:03:38'),
(38,'2025-02-11','08:03:00','pemasukan',60000.00,'Antar Bu Ifah ke kecamatan ',NULL,'2025-02-11 02:04:13'),
(39,'2025-02-11','10:51:00','pemasukan',300000.00,'Bayar BPN mas',NULL,'2025-02-11 02:51:44'),
(40,'2025-02-11','16:56:00','pemasukan',70000.00,'Dari kecamatan loa janan ilir - sungai keledang - bukit pinang perum',NULL,'2025-02-11 09:00:35'),
(41,'2025-02-11','17:10:00','pengeluaran',15000.00,'Jajan pentol',1,'2025-02-11 09:33:49'),
(42,'2025-02-12','08:00:00','pemasukan',52000.00,'Antar Bu ifah',NULL,'2025-02-12 00:30:22'),
(43,'2025-02-12','08:00:00','pengeluaran',10000.00,'Kembalian Bu ifah',6,'2025-02-12 00:30:51'),
(44,'2025-02-12','08:30:00','pengeluaran',100000.00,'Bensin',2,'2025-02-12 00:31:12'),
(45,'2025-02-12','08:32:00','pengeluaran',100000.00,'Hilang',11,'2025-02-12 00:32:40'),
(46,'2025-02-12','08:33:00','pengeluaran',100000.00,'Bayar air perbulan',13,'2025-02-12 00:33:45'),
(47,'2025-02-12','13:05:00','pemasukan',150000.00,'Dikasih bulek',NULL,'2025-02-12 05:37:36'),
(48,'2025-02-12','16:50:00','pemasukan',50000.00,'Antara Bu Ifah',NULL,'2025-02-12 09:01:57'),
(49,'2025-02-12','20:16:00','pemasukan',150000.00,'Antar PP Bu Nanik ke RS dirgahayu',NULL,'2025-02-12 12:43:02');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-02-12 21:06:28
