<?php
// financial-record/input_saldo.php
$host = "127.0.0.1";
$user = "root";
$password = "password_baru";
$database = "finance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $tanggal = $_POST['tanggal'];
    $saldo   = $_POST['saldo'];

    $sql = "INSERT INTO daily_balance (tanggal, saldo) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $tanggal, $saldo);
    if($stmt->execute()){
        $message = "Data saldo berhasil disimpan.";
    } else {
        $message = "Terjadi kesalahan: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Input Saldo Harian</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Input Saldo Harian</h2>
    <?php if(isset($message)): ?>
      <div class="alert alert-info"><?= $message; ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="saldo">Saldo</label>
        <input type="number" step="0.01" id="saldo" name="saldo" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Simpan Saldo</button>
    </form>
    <a href="index.php" class="btn btn-secondary mt-3">Kembali</a>
  </div>
</body>
</html>
