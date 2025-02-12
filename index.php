<?php
// final index.php

// Konfigurasi koneksi database
$host     = "127.0.0.1";
$user     = "root";
$password = "password_baru";
$database = "finance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}


// Definisikan variabel filter_date agar tidak undefined
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';



/* ---------------------------------------------------------------------------
   1. Data untuk Riwayat Saldo Harian
--------------------------------------------------------------------------- */
$sqlSaldo = "SELECT * FROM daily_balance ORDER BY tanggal DESC, id DESC";
$resultSaldo = $conn->query($sqlSaldo);

/* ---------------------------------------------------------------------------
   2. Data untuk Riwayat Transaksi (default: tanpa filter)
--------------------------------------------------------------------------- */
$sqlTrans = "SELECT t.*, et.nama_tag 
             FROM transactions t 
             LEFT JOIN expense_tags et ON t.tag_id = et.id 
             ORDER BY t.tanggal DESC, t.waktu DESC";
$resultTrans = $conn->query($sqlTrans);

/* ---------------------------------------------------------------------------
   3. Perhitungan Mutasi Keseluruhan
   – Menghitung saldo akhir berdasarkan saldo harian terakhir yang diinput 
     dan penjumlahan transaksi masuk (pemasukan & pinjaman) dan keluar 
     (pengeluaran, bayar pinjaman, beri pinjaman)
--------------------------------------------------------------------------- */
// Ambil saldo harian terakhir (input terbaru)
$sqlSaldoTerakhir = "SELECT saldo FROM daily_balance ORDER BY tanggal DESC, id DESC LIMIT 1";
$resultSaldoTerakhir = $conn->query($sqlSaldoTerakhir);
$saldo_harian_terakhir = 0;
if ($resultSaldoTerakhir && $resultSaldoTerakhir->num_rows > 0) {
    $rowSaldo = $resultSaldoTerakhir->fetch_assoc();
    $saldo_harian_terakhir = $rowSaldo['saldo'];
}

// Hitung total transaksi masuk (pemasukan dan pinjaman)
$sqlTotalMasuk = "SELECT IFNULL(SUM(jumlah), 0) AS total_masuk 
                  FROM transactions 
                  WHERE tipe IN ('pemasukan','pinjaman')";
$resultMasuk  = $conn->query($sqlTotalMasuk);
$rowMasuk     = $resultMasuk->fetch_assoc();
$total_masuk  = $rowMasuk['total_masuk'];

// Hitung total transaksi keluar (pengeluaran, bayar pinjaman, beri pinjaman)
$sqlTotalKeluar = "SELECT IFNULL(SUM(jumlah), 0) AS total_keluar 
                   FROM transactions 
                   WHERE tipe IN ('pengeluaran','bayar pinjaman','beri pinjaman')";
$resultKeluar = $conn->query($sqlTotalKeluar);
$rowKeluar    = $resultKeluar->fetch_assoc();
$total_keluar = $rowKeluar['total_keluar'];

// Saldo akhir keseluruhan
$final_balance = $saldo_harian_terakhir + $total_masuk - $total_keluar;

/* ---------------------------------------------------------------------------
   4. Perhitungan Mutasi Per Hari
   – Mengumpulkan tanggal unik dari transaksi dan saldo
   – Untuk tiap tanggal, cek apakah ada input saldo harian untuk menentukan saldo awal.
   – Jika tidak ada, gunakan saldo akhir hari sebelumnya.
--------------------------------------------------------------------------- */
// Ambil tanggal unik dari transaksi
$dates = array();
$sqlDistinctTrans = "SELECT DISTINCT tanggal FROM transactions";
$resultDistinctTrans = $conn->query($sqlDistinctTrans);
if ($resultDistinctTrans) {
    while ($row = $resultDistinctTrans->fetch_assoc()){
        $dates[] = $row['tanggal'];
    }
}
// Ambil tanggal unik dari daily_balance
$sqlDistinctSaldo = "SELECT DISTINCT tanggal FROM daily_balance";
$resultDistinctSaldo = $conn->query($sqlDistinctSaldo);
if ($resultDistinctSaldo) {
    while ($row = $resultDistinctSaldo->fetch_assoc()){
        $dates[] = $row['tanggal'];
    }
}
$dates = array_unique($dates);
sort($dates);

// Perhitungan mutasi per hari
$overall_mutations = array();
$prev_closing_balance = 0; // Basis awal jika tidak ada input saldo

foreach ($dates as $date) {
    // Cek apakah ada input saldo harian pada tanggal tersebut
    $sqlSaldoForDay = "SELECT saldo FROM daily_balance WHERE tanggal = '$date' ORDER BY waktu DESC LIMIT 1";
    $resultSaldoForDay = $conn->query($sqlSaldoForDay);
    if ($resultSaldoForDay && $resultSaldoForDay->num_rows > 0) {
         $rowSaldoForDay = $resultSaldoForDay->fetch_assoc();
         $starting_balance = $rowSaldoForDay['saldo'];
    } else {
         $starting_balance = $prev_closing_balance;
    }
    
    // Hitung total transaksi pada tanggal tersebut
    $sqlTransForDay = "SELECT 
       IFNULL(SUM(CASE WHEN tipe IN ('pemasukan','pinjaman') THEN jumlah ELSE 0 END),0) AS total_in,
       IFNULL(SUM(CASE WHEN tipe IN ('pengeluaran','bayar pinjaman','beri pinjaman') THEN jumlah ELSE 0 END),0) AS total_out
       FROM transactions WHERE tanggal = '$date'";
    $resultTransForDay = $conn->query($sqlTransForDay);
    $rowTransForDay = $resultTransForDay->fetch_assoc();
    $total_in = $rowTransForDay['total_in'];
    $total_out = $rowTransForDay['total_out'];
    
    $closing_balance = $starting_balance + $total_in - $total_out;
    
    $overall_mutations[] = array(
         'tanggal' => $date,
         'starting_balance' => $starting_balance,
         'total_in' => $total_in,
         'total_out' => $total_out,
         'closing_balance' => $closing_balance
    );
    
    $prev_closing_balance = $closing_balance;
    
    
    // ... kode awal index.php ...

// Handle CRUD Hutang & Pinjaman
if (isset($_POST['submit_debt'])) {
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $waktu = $conn->real_escape_string($_POST['waktu']);
    $jumlah = (float)$_POST['jumlah'];
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $tipe = $conn->real_escape_string($_POST['tipe']);
    $status = $conn->real_escape_string($_POST['status']);

    $stmt = $conn->prepare("INSERT INTO debts_loans (tanggal, waktu, jumlah, deskripsi, tipe, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $tanggal, $waktu, $jumlah, $deskripsi, $tipe, $status);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: ".$stmt->error."');</script>";
    }
    $stmt->close();
}

// Handle Delete
if (isset($_GET['delete_debt'])) {
    $id = (int)$_GET['delete_debt'];
    $conn->query("DELETE FROM debts_loans WHERE id = $id");
    header("Location: index.php");
    exit;
    }

}

/* ---------------------------------------------------------------------------
   5. Data untuk Hutang & Pinjaman
--------------------------------------------------------------------------- */
$sqlHutang = "SELECT * FROM debts_loans ORDER BY tanggal DESC, waktu DESC";
$resultHutang = $conn->query($sqlHutang);

// Handle filter tanggal
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';



// Ambil semua data barang

// Pastikan koneksi ke database sudah ada dan query untuk data barang sudah dijalankan
$queryBarang = "SELECT * FROM barang ORDER BY tanggal DESC";
$resultBarang = $conn->query($queryBarang);


?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Catatan Keuangan</title>
<!-- Meta tag viewport untuk responsivitas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Bootstrap CSS responsif -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* Custom style tambahan (opsional) */
    .jumbotron { padding: 2rem 1rem; }
    .tab-content { margin-top: 20px; }
  </style>
  
</head>
<body>
<div class="container mt-5">
  <!-- Header dan Penjelasan Aplikasi -->
  <div class="jumbotron">
    <h1 class="display-4">Selamat Datang di Aplikasi Catatan Keuangan</h1>
    <p class="lead">Aplikasi ini memungkinkan Anda untuk mencatat saldo harian, menginput transaksi dengan berbagai tipe (pemasukan, pengeluaran, pinjaman, bayar pinjaman, beri pinjaman), dan melihat laporan mutasi keuangan secara keseluruhan serta per hari.</p>
    <hr class="my-4">
    <p>Gunakan menu tab di atas untuk navigasi. Di dalamnya, Anda dapat melihat riwayat saldo harian, daftar transaksi, serta laporan mutasi yang memperlihatkan pergerakan saldo berdasarkan input harian dan transaksi.</p>
  </div>
  
  
      <!-- Tautan ke form input -->
    <div class="mb-3">
      <a href="input_saldo.php" class="btn btn-primary">Input Saldo Harian</a>
      <a href="input_transaksi.php" class="btn btn-success">Input Transaksi</a>
      <a href="tags.php" class="btn btn-warning">Kelola Tag</a>
        <a href="input_hutang.php" class="btn btn-info">Input Hutang/Pinjaman</a>
        <a href="barang.php" class="btn btn-info">Input Barang</a>

  </div>
  
  <!-- Menu Tab -->
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home-content" role="tab" aria-controls="home-content" aria-selected="true">Home</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="saldo-tab" data-toggle="tab" href="#saldo-content" role="tab" aria-controls="saldo-content" aria-selected="false">Saldo Harian</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="transaksi-tab" data-toggle="tab" href="#transaksi-content" role="tab" aria-controls="transaksi-content" aria-selected="false">Transaksi</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="mutasi-keseluruhan-tab" data-toggle="tab" href="#mutasi-keseluruhan-content" role="tab" aria-controls="mutasi-keseluruhan-content" aria-selected="false">Mutasi Keseluruhan</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="mutasi-perhari-tab" data-toggle="tab" href="#mutasi-perhari-content" role="tab" aria-controls="mutasi-perhari-content" aria-selected="false">Mutasi Per Hari</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="hutang-tab" data-toggle="tab" href="#hutang-content" role="tab" aria-controls="hutang-content" aria-selected="false">Hutang & Pinjaman</a>
    </li>
     <li class="nav-item">
    <a class="nav-link" id="barang-tab" data-toggle="tab" href="#barang-content" role="tab" aria-controls="barang-content" aria-selected="false">Daftar Barang</a>
    </li>
    
  </ul>
  
  <!-- Isi Tab -->
  <div class="tab-content" id="myTabContent">
    <!-- Home Tab -->
    <div class="tab-pane fade show active" id="home-content" role="tabpanel" aria-labelledby="home-tab">
      <h3>Penjelasan Aplikasi & Cara Penggunaan</h3>
      <p>Aplikasi Catatan Keuangan ini dirancang untuk membantu Anda memantau keuangan secara terperinci. Berikut cara menggunakannya:</p>
      <ul>
        <li><strong>Input Saldo Harian:</strong> Masukkan saldo akhir harian sebagai basis untuk perhitungan keuangan. Jika saldo harian tidak diinput ulang, sistem akan terus menggunakan saldo akhir hari sebelumnya.</li>
        <li><strong>Input Transaksi:</strong> Catat semua transaksi keuangan dengan memilih tipe transaksi yang sesuai:
          <ul>
            <li><em>Pemasukan</em> dan <em>Pinjaman</em> dianggap sebagai penambahan saldo.</li>
            <li><em>Pengeluaran</em>, <em>Bayar Pinjaman</em>, dan <em>Beri Pinjaman</em> dianggap sebagai pengurangan saldo.</li>
          </ul>
        </li>
        <li><strong>Mutasi Keseluruhan:</strong> Menampilkan saldo awal (dari input saldo harian terakhir), total transaksi masuk dan keluar, serta saldo akhir yang dihitung secara keseluruhan.</li>
        <li><strong>Mutasi Per Hari:</strong> Menampilkan rekap pergerakan keuangan per tanggal. Pada tiap hari, jika ada input saldo harian, saldo awalnya direset; jika tidak, saldo awal diambil dari saldo akhir hari sebelumnya.</li>
      </ul>
      <p>Gunakan tab di atas untuk beralih antar tampilan sesuai kebutuhan Anda.</p>
    </div>
    
    <!-- Saldo Harian Tab -->
    <div class="tab-pane fade" id="saldo-content" role="tabpanel" aria-labelledby="saldo-tab">
      <h3>Riwayat Saldo Harian</h3>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <!--th>No</th-->
            <th>Tanggal</th>
            <th>Saldo</th>
            <th>Waktu Pencatatan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($resultSaldo && $resultSaldo->num_rows > 0) {
              while ($row = $resultSaldo->fetch_assoc()) {
                  echo "<tr>";
                //  echo "<td>" . $row['id'] . "</td>";
                  echo "<td>" . $row['tanggal'] . "</td>";
                  echo "<td>Rp " . number_format($row['saldo'], 2, ',', '.') . "</td>";
                  echo "<td>" . $row['waktu'] . "</td>";
                  echo "<td>
                          <a href='edit_saldo.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning'>Edit</a>
                          <a href='delete_saldo.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus data saldo ini?\")'>Hapus</a>
                        </td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='5'>Belum ada data saldo</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
    
    <!-- Transaksi Tab dengan Filter Tanggal (Menggunakan AJAX) -->
    <div class="tab-pane fade" id="transaksi-content" role="tabpanel" aria-labelledby="transaksi-tab">
      <h3>Daftar Transaksi</h3>
      <!-- Form Filter Tanggal -->
      <form id="filterForm" class="form-inline mb-3">
        <div class="form-group mr-2">
          <label for="filter_date" class="mr-2">Pilih Tanggal:</label>
          <input type="date" id="filter_date" name="filter_date" class="form-control" value="<?= htmlspecialchars($filter_date); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Cari</button>
      </form>
      <div class="table-responsive" id="transaksiTable">
        <?php
        // Jika tidak ada filter, tampilkan seluruh transaksi
        if ($filter_date == '') {
            if ($resultTrans && $resultTrans->num_rows > 0) {
                echo '<table class="table table-bordered">';
                echo '<thead class="thead-light">';
                echo '<tr>';
                echo '<th>Tanggal</th>';
                echo '<th>Waktu</th>';
                echo '<th>Tipe</th>';
                echo '<th>Jumlah</th>';
                echo '<th>Deskripsi / Tag</th>';
                echo '<th>Aksi</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                while ($row = $resultTrans->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['tanggal'] . "</td>";
                    echo "<td>" . $row['waktu'] . "</td>";
                    echo "<td>" . ucfirst($row['tipe']) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah'], 2, ',', '.') . "</td>";
                    if ($row['tipe'] == 'pemasukan' || $row['tipe'] == 'pinjaman') {
                        echo "<td>" . $row['deskripsi'] . "</td>";
                    } else {
                        echo "<td>" . $row['nama_tag'] . "</td>";
                    }
                    echo "<td>
                            <a href='edit_transaksi.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning'>Edit</a>
                            <a href='delete_transaksi.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus transaksi ini?\")'>Hapus</a>
                          </td>";
                    echo "</tr>";
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<div class="alert alert-warning">Tidak ada data transaksi.</div>';
            }
        }
        ?>
      </div>
    </div>
    
    
    <!-- Mutasi Keseluruhan Tab -->
    <div class="tab-pane fade" id="mutasi-keseluruhan-content" role="tabpanel" aria-labelledby="mutasi-keseluruhan-tab">
    <h3 class="mt-5">Mutasi Uang Keseluruhan Per Hari</h3>
    <table class="table table-bordered">
      <thead class="thead-light">
        <tr>
          <th>Tanggal</th>
          <th>Saldo Awal</th>
          <th>Total Masuk (Pemasukan & Pinjaman)</th>
          <th>Total Keluar (Pengeluaran, Bayar Pinjaman & Beri Pinjaman)</th>
          <th>Saldo Akhir</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (!empty($overall_mutations)) {
            foreach ($overall_mutations as $row) {
                echo "<tr>";
                echo "<td>" . $row['tanggal'] . "</td>";
                echo "<td>Rp " . number_format($row['starting_balance'], 2, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($row['total_in'], 2, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($row['total_out'], 2, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($row['closing_balance'], 2, ',', '.') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Belum ada data mutasi</td></tr>";
        }
        ?>
      </tbody>
    </table>
    
  </div>
    
  
    <!-- Mutasi Per Hari Tab -->
    <div class="tab-pane fade" id="mutasi-perhari-content" role="tabpanel" aria-labelledby="mutasi-perhari-tab">
      <h3>Mutasi Per Hari</h3>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>Tanggal</th>
            <th>Saldo Awal</th>
            <th>Total Masuk</th>
            <th>Total Keluar</th>
            <th>Saldo Akhir</th>
            <th>NET Mutasi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (!empty($overall_mutations)) {
              foreach ($overall_mutations as $row) {
                  $net = $row['total_in'] - $row['total_out'];
                  echo "<tr>";
                  echo "<td>" . $row['tanggal'] . "</td>";
                  echo "<td>Rp " . number_format($row['starting_balance'], 2, ',', '.') . "</td>";
                  echo "<td>Rp " . number_format($row['total_in'], 2, ',', '.') . "</td>";
                  echo "<td>Rp " . number_format($row['total_out'], 2, ',', '.') . "</td>";
                  echo "<td>Rp " . number_format($row['closing_balance'], 2, ',', '.') . "</td>";
                  echo "<td>Rp " . number_format($net, 2, ',', '.') . "</td>";
                echo "</tr>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='5'>Belum ada data mutasi</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
          <!--hutang tab-->
<div class="tab-pane fade" id="hutang-content" role="tabpanel" aria-labelledby="hutang-tab">
  <h3>Kelola Hutang & Pinjaman</h3>

  <!-- Tabel Data -->
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Waktu</th>
        <th>Tipe</th>
        <th>Jumlah</th>
        <th>Deskripsi</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $resultHutang->fetch_assoc()): ?>
      <tr>
        <td><?= $row['tanggal'] ?></td>
        <td><?= $row['waktu'] ?></td>
        <td><?= ucfirst($row['tipe']) ?></td>
        <td>Rp <?= number_format($row['jumlah'], 2, ',', '.') ?></td>
        <td><?= $row['deskripsi'] ?></td>
        <td>
          <span class="badge <?= $row['status'] == 'lunas' ? 'badge-success' : 'badge-warning' ?>">
            <?= ucfirst($row['status']) ?>
          </span>
        </td>
        <td>
          <a href="edit_hutang.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
          <!--a href="delete_hutang.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a-->
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>



<!-- Tab Daftar Barang -->
<div class="tab-pane fade" id="barang-content" role="tabpanel" aria-labelledby="barang-tab">
  <h3>Daftar Barang</h3>
  <!-- Tabel Data Barang -->
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Nama Toko</th>
        <th>Alamat</th>
        <th>Nama Barang</th>
        <th>Harga</th>
        <th>Status</th>
        <th>Deskripsi</th>
        <th>Foto</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($resultBarang && $resultBarang->num_rows > 0): ?>
        <?php while ($row = $resultBarang->fetch_assoc()): ?>
          <tr>
            <td><?= $row['tanggal'] ?></td>
            <td><?= $row['nama_toko'] ?></td>
            <td><?= $row['alamat_toko'] ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td>Rp<?= number_format($row['harga_barang'], 2, ',', '.') ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['deskripsi'] ?></td>
            <td>
              <?php if (!empty($row['foto'])): ?>
                <img src="<?= $row['foto'] ?>" width="50">
              <?php else: ?>
                Tidak ada foto
              <?php endif; ?>
            </td>
            <td>
              <a href="edit_barang.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="hapus_barang.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
          <tr>
            <td colspan="9" class="text-center">Tidak ada data barang</td>
          </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>


  </div> <!-- End tab-content -->
  
</div> <!-- End container -->



<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function(){
    $('#filterForm').submit(function(e) {
      e.preventDefault();
      var filterDate = $('#filter_date').val();
      $.ajax({
        url: 'get_transaksi.php',
        type: 'GET',
        data: { filter_date: filterDate },
        success: function(data) {
          $('#transaksiTable').html(data);
        },
        error: function() {
          $('#transaksiTable').html('<div class="alert alert-danger">Terjadi kesalahan saat mengambil data transaksi.</div>');
        }
      });
    });
  });
</script>



</body>
</html>