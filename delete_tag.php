<?php
// delete_tag.php

$host     = "127.0.0.1";
$user     = "root";
$password = "password_baru";
$database = "finance";

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan parameter id tersedia di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tag tidak ditemukan.");
}

$tag_id = $_GET['id'];

// Siapkan statement untuk menghapus tag berdasarkan id
$sql = "DELETE FROM expense_tags WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Gagal mempersiapkan statement: " . $conn->error);
}

$stmt->bind_param("i", $tag_id);

// Eksekusi statement dan redirect jika berhasil
if ($stmt->execute()) {
    header("Location: tags.php");
    exit();
} else {
    echo "Terjadi kesalahan saat menghapus tag: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
