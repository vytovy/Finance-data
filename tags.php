<?php
// financial-record/tags.php
$host = "127.0.0.1";
$user = "root";
$password = "password_baru";
$database = "finance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT * FROM expense_tags ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Tag Pengeluaran</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h1>Kelola Tag Pengeluaran</h1>
    
    <!-- Form Tambah Tag -->
    <form action="add_tag.php" method="post" class="mb-4">
      <div class="form-group">
        <label for="nama_tag">Nama Tag</label>
        <input type="text" name="nama_tag" id="nama_tag" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Tambah Tag</button>
    </form>
    
    <!-- Tabel Daftar Tag -->
    <table class="table table-bordered">
      <thead class="thead-light">
        <tr>
          <th>No</th>
          <th>Nama Tag</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td>".$row['id']."</td>";
                echo "<td>".$row['nama_tag']."</td>";
                echo "<td>
                        <a href='edit_tag.php?id=".$row['id']."' class='btn btn-sm btn-warning'>Edit</a>
                        <a href='delete_tag.php?id=".$row['id']."' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus tag ini?\")'>Hapus</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Belum ada tag</td></tr>";
        }
        ?>
      </tbody>
    </table>
    <a href="index.php" class="btn btn-secondary mt-3">Kembali</a>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
