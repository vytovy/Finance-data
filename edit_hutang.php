<?php
// Koneksi database
$host = "127.0.0.1";
$user = "root";
$password = "password_baru";
$database = "finance";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = (int)$_GET['id']; 

// Ambil data berdasarkan ID
$sql = "SELECT * FROM debts_loans WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Jika data tidak ditemukan
if (!$data) {
    die("Data tidak ditemukan.");
}

// Proses Form Submit untuk Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $waktu = $conn->real_escape_string($_POST['waktu']);
    $jumlah = (float)$_POST['jumlah'];
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $tipe = $conn->real_escape_string($_POST['tipe']);
    $status = $conn->real_escape_string($_POST['status']);

    $update_sql = "UPDATE debts_loans SET tanggal=?, waktu=?, jumlah=?, deskripsi=?, tipe=?, status=? WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssdsssi", $tanggal, $waktu, $jumlah, $deskripsi, $tipe, $status, $id);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Gagal memperbarui data: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Hutang/Pinjaman</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Edit Hutang/Pinjaman</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Kembali</a>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>" required>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label>Waktu</label>
                    <input type="time" name="waktu" class="form-control" value="<?= $data['waktu'] ?>" required>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" step="0.01" name="jumlah" class="form-control" value="<?= $data['jumlah'] ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tipe</label>
                    <select name="tipe" class="form-control" required>
                        <option value="hutang" <?= $data['tipe'] == "hutang" ? "selected" : "" ?>>Pemberi Pinjaman</option>
                        <option value="pinjaman" <?= $data['tipe'] == "pinjaman" ? "selected" : "" ?>>Berikan Pinjaman</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="belum lunas" <?= $data['status'] == "belum lunas" ? "selected" : "" ?>>Belum Lunas</option>
                        <option value="lunas" <?= $data['status'] == "lunas" ? "selected" : "" ?>>Lunas</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"><?= $data['deskripsi'] ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>
