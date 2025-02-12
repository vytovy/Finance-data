<?php
$host = "127.0.0.1";
$user = "root";
$password = "password_baru";
$database = "finance";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $nama_toko = $conn->real_escape_string($_POST['nama_toko']);
    $alamat_toko = $conn->real_escape_string($_POST['alamat_toko']);
    $nama_barang = $conn->real_escape_string($_POST['nama_barang']);
    $harga_barang = (float)$_POST['harga_barang'];
    $status = $conn->real_escape_string($_POST['status']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);

    // Upload Foto (opsional)
    $foto = NULL;
    if (!empty($_FILES['foto']['name'])) {
        $foto_dir = "uploads/";
        $foto = $foto_dir . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }

    $stmt = $conn->prepare("INSERT INTO barang (tanggal, nama_toko, alamat_toko, nama_barang, harga_barang, status, deskripsi, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdsss", $tanggal, $nama_toko, $alamat_toko, $nama_barang, $harga_barang, $status, $deskripsi, $foto);
    
    if ($stmt->execute()) {
        header("Location: barang.php");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . $stmt->error;
    }
    $stmt->close();
}

// Ambil semua data barang
$result = $conn->query("SELECT * FROM barang ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Input Data Barang</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Kembali</a>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Nama Toko</label>
            <input type="text" name="nama_toko" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Alamat Toko</label>
            <textarea name="alamat_toko" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Harga Barang</label>
            <input type="number" step="0.01" name="harga_barang" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Dimiliki">Dimiliki</option>
                <option value="Belum Dimiliki">Belum Dimiliki</option>
            </select>
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Upload Foto (Opsional)</label>
            <input type="file" name="foto" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

    
</body>
</html>
