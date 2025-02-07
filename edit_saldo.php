<?php
// financial-record/edit_saldo.php
$host     = "127.0.0.1";
$user     = "root";
$password = "password_baru";
$database = "finance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID saldo tidak ditemukan.");
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $saldo   = $_POST['saldo'];
    
    $sql = "UPDATE daily_balance SET tanggal = ?, saldo = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdi", $tanggal, $saldo, $id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$sql = "SELECT * FROM daily_balance WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Data saldo tidak ditemukan.");
}
$data = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Saldo Harian</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
 <div class="container mt-5">
   <h2>Edit Saldo Harian</h2>
   <?php if (isset($message)): ?>
      <div class="alert alert-danger"><?= $message; ?></div>
   <?php endif; ?>
   <form method="post" action="">
     <div class="form-group">
       <label for="tanggal">Tanggal</label>
       <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= $data['tanggal']; ?>" required>
     </div>
     <div class="form-group">
       <label for="saldo">Saldo</label>
       <input type="number" step="0.01" name="saldo" id="saldo" class="form-control" value="<?= $data['saldo']; ?>" required>
     </div>
     <button type="submit" class="btn btn-primary">Perbarui</button>
     <a href="index.php" class="btn btn-secondary">Kembali</a>
   </form>
 </div>
</body>
</html>
