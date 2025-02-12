<?php
// Koneksi database (sesuaikan dengan konfigurasi Anda)
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
    $waktu = $conn->real_escape_string($_POST['waktu']);
    $jumlah = (float)$_POST['jumlah'];
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $tipe = $conn->real_escape_string($_POST['tipe']);
    $status = $conn->real_escape_string($_POST['status']);

    $stmt = $conn->prepare("INSERT INTO debts_loans (tanggal, waktu, jumlah, deskripsi, tipe, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $tanggal, $waktu, $jumlah, $deskripsi, $tipe, $status);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal menyimpan data: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Hutang/Pinjaman</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Form Input Hutang/Pinjaman</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Kembali</a>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label>Waktu</label>
                    <input type="time" name="waktu" class="form-control" required>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" step="0.01" name="jumlah" class="form-control" placeholder="Rp" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tipe</label>
                    <select name="tipe" class="form-control" required>
                        <option value="hutang">Pemberi Pinjaman</option>
                        <option value="pinjaman">Berikan Pinjaman</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="belum lunas">Belum Lunas</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
</body>
</html>