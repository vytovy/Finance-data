<?php
// get_transaksi.php

// Konfigurasi koneksi database
$host     = "127.0.0.1";
$user     = "root";
$password = "password_baru";
$database = "finance";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

if ($filter_date != '') {
    $sqlTrans = "SELECT t.*, et.nama_tag 
                 FROM transactions t 
                 LEFT JOIN expense_tags et ON t.tag_id = et.id 
                 WHERE DATE(t.tanggal) = '$filter_date'
                 ORDER BY t.tanggal DESC, t.waktu DESC";
} else {
    $sqlTrans = "SELECT t.*, et.nama_tag 
                 FROM transactions t 
                 LEFT JOIN expense_tags et ON t.tag_id = et.id 
                 ORDER BY t.tanggal DESC, t.waktu DESC";
}
$resultTrans = $conn->query($sqlTrans);

if ($resultTrans && $resultTrans->num_rows > 0) {
    $output = '<table class="table table-bordered">';
    $output .= '<thead class="thead-light">';
    $output .= '<tr>';
    $output .= '<th>Tanggal</th>';
    $output .= '<th>Waktu</th>';
    $output .= '<th>Tipe</th>';
    $output .= '<th>Jumlah</th>';
    $output .= '<th>Deskripsi / Tag</th>';
    $output .= '<th>Aksi</th>';
    $output .= '</tr>';
    $output .= '</thead><tbody>';
    while ($row = $resultTrans->fetch_assoc()) {
        $output .= '<tr>';
        $output .= '<td>' . $row['tanggal'] . '</td>';
        $output .= '<td>' . $row['waktu'] . '</td>';
        $output .= '<td>' . ucfirst($row['tipe']) . '</td>';
        $output .= '<td>Rp ' . number_format($row['jumlah'], 2, ',', '.') . '</td>';
        if ($row['tipe'] == 'pemasukan' || $row['tipe'] == 'pinjaman') {
            $output .= '<td>' . $row['deskripsi'] . '</td>';
        } else {
            $output .= '<td>' . $row['nama_tag'] . '</td>';
        }
        $output .= '<td>
                        <a href="edit_transaksi.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_transaksi.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin hapus transaksi ini?\')">Hapus</a>
                    </td>';
        $output .= '</tr>';
    }
    $output .= '</tbody></table>';
    echo $output;
} else {
    echo '<div class="alert alert-warning">Tidak ada data transaksi untuk tanggal yang dipilih.</div>';
}
$conn->close();
?>
