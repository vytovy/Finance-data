<?php
// hapus_barang.php

// Koneksi database
$host     = "127.0.0.1";
$user     = "root";
$password = "password_baru";
$database = "finance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan ID dikirim melalui URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = (int)$_GET['id'];

// Ambil data untuk mendapatkan path foto (jika ada)
$sql = "SELECT foto FROM barang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

// Jika ada foto dan file tersebut ada di server, hapus file-nya (opsional)
if ($data && !empty($data['foto']) && file_exists($data['foto'])) {
    unlink($data['foto']);
}

// Hapus data barang
$sqlDelete = "DELETE FROM barang WHERE id = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("i", $id);
if ($stmtDelete->execute()) {
    header("Location: index.php"); // Redirect ke halaman utama atau ke tab daftar barang
    exit;
} else {
    echo "Gagal menghapus data: " . $stmtDelete->error;
}
$stmtDelete->close();
$conn->close();
?>
