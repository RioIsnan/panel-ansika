<?php
include "koneksi.php";

// Ambil data dari tabel kuis
$kuis = $conn->query("SELECT * FROM tabel_kuis")->fetch_all(MYSQLI_ASSOC);

// Ambil data dari tabel cocok kata
$cocok = $conn->query("SELECT * FROM tabel_cocok_kata")->fetch_all(MYSQLI_ASSOC);

// Gabungkan dalam satu paket
$response = [
    "tabel_kuis" => $kuis,
    "tabel_cocok_kata" => $cocok
];

header('Content-Type: application/json');
echo json_encode($response);
?>