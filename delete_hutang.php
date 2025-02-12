<?php
$host = "127.0.0.1";
$user = "root";
$password = "password_baru";
$database = "finance";
$conn = new mysqli($host, $user, $password, $database);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM debts_loans WHERE id = $id");
    header("Location: index.php");
    exit;
}
?>