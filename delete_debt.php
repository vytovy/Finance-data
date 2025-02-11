<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM debts_loans WHERE id = $id");
    header("Location: index.php");
}
?>
