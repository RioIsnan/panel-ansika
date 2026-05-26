<?php
include "koneksi.php";

// Ambil data kuis
$kuis = $conn->query("SELECT * FROM tabel_kuis")->fetch_all(MYSQLI_ASSOC);

// Ambil data cocok kata
$cocok = $conn->query("SELECT * FROM tabel_cocok_kata")->fetch_all(MYSQLI_ASSOC);

// Gabungkan dalam satu paket JSON
echo json_encode([
    "kuis" => $kuis,
    "cocok_kata" => $cocok
]);
?>