<?php
// input_transaksi.php
$host     = "127.0.0.1";
$user     = "root";
$password = "password_baru";
$database = "finance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data tag untuk transaksi yang memerlukan kategori (pengeluaran, bayar pinjaman, beri pinjaman)
$tags = [];
$sqlTag = "SELECT * FROM expense_tags ORDER BY nama_tag ASC";
$resultTag = $conn->query($sqlTag);
if ($resultTag && $resultTag->num_rows > 0) {
    while ($row = $resultTag->fetch_assoc()) {
        $tags[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal   = $_POST['tanggal'];
    $waktu     = $_POST['waktu'];
    $tipe      = $_POST['tipe'];
    $jumlah    = $_POST['jumlah'];
    $deskripsi = $_POST['deskripsi'];
    
    // Jika tag_id kosong atau tidak diisi, set ke null
    $tag_id = (!empty($_POST['tag_id'])) ? $_POST['tag_id'] : null;
    
    // Untuk jenis transaksi yang tidak memerlukan tag (pemasukan dan pinjaman), pastikan $tag_id = null
    if ($tipe == 'pemasukan' || $tipe == 'pinjaman') {
        $tag_id = null;
    }
    
    // Gunakan query dengan 6 placeholder untuk semua jenis transaksi
    $sql = "INSERT INTO transactions (tanggal, waktu, tipe, jumlah, deskripsi, tag_id)
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Gagal mempersiapkan statement: " . $conn->error);
    }
    
    // Gunakan format: s = string, d = double, i = integer.
    // Meskipun $tag_id bernilai null, bind_param harus mendapatkan 6 variabel.
    $stmt->bind_param("sssdsi", $tanggal, $waktu, $tipe, $jumlah, $deskripsi, $tag_id);
    
    if ($stmt->execute()) {
        $message = "Transaksi berhasil disimpan.";
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
  <title>Input Transaksi</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script>
    $(document).ready(function(){
      // Tampilkan dropdown tag hanya untuk jenis transaksi yang memerlukan kategori
      $('#tipe').on('change', function(){
        var tipe = $(this).val();
        if(tipe == 'pemasukan' || tipe == 'pinjaman'){
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
    <h2>Input Transaksi</h2>
    <?php if(isset($message)): ?>
      <div class="alert alert-info"><?= $message; ?></div>
    <?php endif; ?>
    <form method="post" action="">
      <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" id="tanggal" name="tanggal" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="waktu">Waktu</label>
        <input type="time" id="waktu" name="waktu" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="tipe">Tipe Transaksi</label>
        <select id="tipe" name="tipe" class="form-control">
  <option value="pemasukan">Pemasukan</option>
  <option value="pengeluaran">Pengeluaran</option>
  <option value="pinjaman">Pinjaman</option>
  <option value="bayar pinjaman">Bayar Pinjaman</option>
  <option value="beri pinjaman">Beri Pinjaman</option>
</select>


      </div>
      <div class="form-group">
        <label for="jumlah">Jumlah</label>
        <input type="number" step="0.01" id="jumlah" name="jumlah" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
      </div>
      <div class="form-group" id="tagGroup">
        <label for="tag_id">Tag (Untuk Pengeluaran, Bayar Pinjaman, dan Beri Pinjaman)</label>
        <select id="tag_id" name="tag_id" class="form-control">
          <option value="">-- Pilih Tag --</option>
          <?php foreach($tags as $tag): ?>
            <option value="<?= $tag['id']; ?>"><?= $tag['nama_tag']; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
    </form>
    <a href="index.php" class="btn btn-secondary mt-3">Kembali</a>
  </div>
</body>
</html>
