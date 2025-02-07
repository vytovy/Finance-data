<?php
// edit_tag.php

$host     = "127.0.0.1";
$user     = "root";
$password = "password_baru";
$database = "finance";

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan parameter ID ada
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tag tidak ditemukan.");
}

$tag_id = $_GET['id'];

// Jika form disubmit, proses update data tag
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_tag = trim($_POST['nama_tag']);

    if (empty($nama_tag)) {
        $message = "Nama tag tidak boleh kosong.";
    } else {
        $sql = "UPDATE expense_tags SET nama_tag = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Gagal mempersiapkan statement: " . $conn->error);
        }
        $stmt->bind_param("si", $nama_tag, $tag_id);
        if ($stmt->execute()) {
            // Setelah update berhasil, alihkan kembali ke tags.php
            header("Location: tags.php");
            exit();
        } else {
            $message = "Terjadi kesalahan saat memperbarui tag: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Ambil data tag berdasarkan ID
$sql = "SELECT * FROM expense_tags WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Gagal mempersiapkan statement: " . $conn->error);
}
$stmt->bind_param("i", $tag_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Tag tidak ditemukan.");
}
$tag = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Tag Pengeluaran</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Edit Tag Pengeluaran</h2>
    <?php if (isset($message)): ?>
      <div class="alert alert-danger"><?= $message; ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <div class="form-group">
        <label for="nama_tag">Nama Tag</label>
        <input type="text" name="nama_tag" id="nama_tag" class="form-control" value="<?= htmlspecialchars($tag['nama_tag']); ?>" required>
      </div>
      <button type="submit" class="btn btn-primary">Perbarui Tag</button>
      <a href="tags.php" class="btn btn-secondary">Kembali</a>
    </form>
  </div>
</body>
</html>
