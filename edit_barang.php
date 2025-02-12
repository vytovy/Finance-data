<?php
// edit_barang.php

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

// Ambil data barang berdasarkan ID
$sql = "SELECT * FROM barang WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal      = $conn->real_escape_string($_POST['tanggal']);
    $nama_toko    = $conn->real_escape_string($_POST['nama_toko']);
    $alamat_toko  = $conn->real_escape_string($_POST['alamat_toko']);
    $nama_barang  = $conn->real_escape_string($_POST['nama_barang']);
    $harga_barang = (float)$_POST['harga_barang'];
    $status       = $conn->real_escape_string($_POST['status']);
    $deskripsi    = $conn->real_escape_string($_POST['deskripsi']);

    // Tangani upload foto (opsional)
    if (!empty($_FILES['foto']['name'])) {
        $foto_dir = "uploads/";
        $foto     = $foto_dir . basename($_FILES['foto']['name']);
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $foto)) {
            $error = "Gagal mengupload foto.";
        }
    } else {
        // Jika tidak mengunggah foto baru, pertahankan foto lama
        $foto = $data['foto'];
    }

    if (empty($error)) {
        $update_sql = "UPDATE barang SET tanggal = ?, nama_toko = ?, alamat_toko = ?, nama_barang = ?, harga_barang = ?, status = ?, deskripsi = ?, foto = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ssssdsssi", $tanggal, $nama_toko, $alamat_toko, $nama_barang, $harga_barang, $status, $deskripsi, $foto, $id);
        
        if ($stmt_update->execute()) {
            header("Location: index.php"); // Atau redirect ke halaman daftar barang
            exit;
        } else {
            $error = "Gagal mengupdate data: " . $stmt_update->error;
        }
        $stmt_update->close();
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Barang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Data Barang</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Kembali</a>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
        </div>
        <div class="form-group">
            <label>Nama Toko</label>
            <input type="text" name="nama_toko" class="form-control" value="<?= htmlspecialchars($data['nama_toko']) ?>" required>
        </div>
        <div class="form-group">
            <label>Alamat Toko</label>
            <textarea name="alamat_toko" class="form-control" required><?= htmlspecialchars($data['alamat_toko']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($data['nama_barang']) ?>" required>
        </div>
        <div class="form-group">
            <label>Harga Barang</label>
            <input type="number" step="0.01" name="harga_barang" class="form-control" value="<?= $data['harga_barang'] ?>" required>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Dimiliki" <?= $data['status'] == 'Dimiliki' ? 'selected' : '' ?>>Dimiliki</option>
                <option value="Belum Dimiliki" <?= $data['status'] == 'Belum Dimiliki' ? 'selected' : '' ?>>Belum Dimiliki</option>
            </select>
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Upload Foto (Kosongkan jika tidak ingin mengubah)</label>
            <input type="file" name="foto" class="form-control-file">
            <?php if (!empty($data['foto'])): ?>
                <img src="<?= $data['foto'] ?>" width="100" class="mt-2">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>
