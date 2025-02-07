<?php
// financial-record/edit_transaksi.php
$host     = "127.0.0.1";
$user     = "root";
$password = "password_baru";
$database = "finance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID transaksi tidak ditemukan.");
}

$id = $_GET['id'];

// Ambil data tag untuk dropdown (untuk pengeluaran)
$tags = [];
$sqlTag = "SELECT * FROM expense_tags ORDER BY nama_tag ASC";
$resultTag = $conn->query($sqlTag);
if ($resultTag->num_rows > 0) {
    while ($row = $resultTag->fetch_assoc()) {
        $tags[] = $row;
    }
}

// Ambil data transaksi berdasarkan ID
$sql = "SELECT * FROM transactions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Data transaksi tidak ditemukan.");
}
$data = $result->fetch_assoc();
$stmt->close();

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal   = $_POST['tanggal'];
    $waktu     = $_POST['waktu'];
    $tipe      = $_POST['tipe'];
    $jumlah    = $_POST['jumlah'];
    $deskripsi = $_POST['deskripsi'];
    $tag_id    = (!empty($_POST['tag_id'])) ? $_POST['tag_id'] : null;
    
    if ($tipe == 'income') {
        $sql = "UPDATE transactions SET tanggal = ?, waktu = ?, tipe = ?, jumlah = ?, deskripsi = ?, tag_id = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsi", $tanggal, $waktu, $tipe, $jumlah, $deskripsi, $id);
    } else {
        $sql = "UPDATE transactions SET tanggal = ?, waktu = ?, tipe = ?, jumlah = ?, deskripsi = ?, tag_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsii", $tanggal, $waktu, $tipe, $jumlah, $deskripsi, $tag_id, $id);
    }
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Transaksi</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script>
    $(document).ready(function(){
      $('#tipe').on('change', function(){
        if ($(this).val() == 'income') {
          $('#tagGroup').hide();
        } else {
          $('#tagGroup').show();
        }
      });
      $('#tipe').trigger('change');
    });
  </script>
</head>
<body>
  <div class="container mt-5">
    <h2>Edit Transaksi</h2>
    <?php if (isset($message)): ?>
      <div class="alert alert-danger"><?= $message; ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= $data['tanggal']; ?>" required>
      </div>
      <div class="form-group">
        <label for="waktu">Waktu</label>
        <input type="time" name="waktu" id="waktu" class="form-control" value="<?= $data['waktu']; ?>" required>
      </div>
      <div class="form-group">
        <label for="tipe">Tipe Transaksi</label>
        <select name="tipe" id="tipe" class="form-control">
          <option value="income" <?= ($data['tipe'] == 'income') ? 'selected' : ''; ?>>Pemasukan</option>
          <option value="expense" <?= ($data['tipe'] == 'expense') ? 'selected' : ''; ?>>Pengeluaran</option>
        </select>
      </div>
      <div class="form-group">
        <label for="jumlah">Jumlah</label>
        <input type="number" step="0.01" name="jumlah" id="jumlah" class="form-control" value="<?= $data['jumlah']; ?>" required>
      </div>
      <div class="form-group">
        <label for="deskripsi">Deskripsi (Untuk Pemasukan)</label>
        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"><?= $data['deskripsi']; ?></textarea>
      </div>
      <div class="form-group" id="tagGroup">
        <label for="tag_id">Tag (Untuk Pengeluaran)</label>
        <select name="tag_id" id="tag_id" class="form-control">
          <option value="">-- Pilih Tag --</option>
          <?php foreach ($tags as $tag): ?>
            <option value="<?= $tag['id']; ?>" <?= ($data['tag_id'] == $tag['id']) ? 'selected' : ''; ?>><?= $tag['nama_tag']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Perbarui Transaksi</button>
      <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
  </div>
</body>
</html>
