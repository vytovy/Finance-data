<?php
// add_tag.php
$host = "127.0.0.1";
$user = "root";
$password = "password_baru";
$database = "finance";

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_tag = trim($_POST['nama_tag']);

    // Pastikan input tidak kosong
    if (!empty($nama_tag)) {
        $sql = "INSERT INTO expense_tags (nama_tag) VALUES (?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $nama_tag);
            if ($stmt->execute()) {
                // Redirect kembali ke halaman tags.php setelah berhasil
                header("Location: tags.php");
                exit();
            } else {
                echo "Terjadi kesalahan saat menyimpan tag: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Gagal mempersiapkan statement: " . $conn->error;
        }
    } else {
        echo "Nama tag tidak boleh kosong.";
    }
}

$conn->close();
?>
