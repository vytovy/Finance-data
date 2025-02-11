<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM debts_loans WHERE id = $id");
    $data = $result->fetch_assoc();
}

if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $waktu = $conn->real_escape_string($_POST['waktu']);
    $jumlah = (float)$_POST['jumlah'];
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $tipe = $conn->real_escape_string($_POST['tipe']);
    $status = $conn->real_escape_string($_POST['status']);

    $stmt = $conn->prepare("UPDATE debts_loans SET 
        tanggal = ?,
        waktu = ?,
        jumlah = ?,
        deskripsi = ?,
        tipe = ?,
        status = ?
        WHERE id = ?");
    $stmt->bind_param("ssdsssi", $tanggal, $waktu, $jumlah, $deskripsi, $tipe, $status, $id);
    
    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: ".$stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Data</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">
        
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

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control"><?= $data['deskripsi'] ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tipe</label>
                    <select name="tipe" class="form-control">
                        <option value="hutang" <?= $data['tipe']=='hutang'?'selected':'' ?>>Hutang</option>
                        <option value="pinjaman" <?= $data['tipe']=='pinjaman'?'selected':'' ?>>Pinjaman</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="belum lunas" <?= $data['status']=='belum lunas'?'selected':'' ?>>Belum Lunas</option>
                        <option value="lunas" <?= $data['status']=='lunas'?'selected':'' ?>>Lunas</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" name="update" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
