<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ansika_online"; // Pastikan ini nama database yang benar di phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>