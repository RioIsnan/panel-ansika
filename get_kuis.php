<?php
// Gunakan koneksi yang sudah dibuat sebelumnya agar ringkas
include "cek_login.php";
include "koneksi.php";

// Set header agar dibaca sebagai JSON oleh Android
header('Content-Type: application/json; charset=utf-8');

// Query mengambil semua kolom dari tabel_kuis
$sql = "SELECT id, pertanyaan, pilihan_0, pilihan_1, pilihan_2, pilihan_3, jawaban_index, level FROM tabel_kuis";
$result = $conn->query($sql);

$data = array();

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Memastikan data integer tetap dikirim sebagai integer (opsional, tapi bagus untuk Retrofit)
            $row['id'] = (int)$row['id'];
            $row['jawaban_index'] = (int)$row['jawaban_index'];
            $row['level'] = (int)$row['level'];
            
            $data[] = $row;
        }
        // Kirim response sukses
        echo json_encode([
            "status" => "success",
            "message" => "Data berhasil diambil",
            "payload" => $data
        ]);
    } else {
        // Jika tabel kosong
        echo json_encode([
            "status" => "empty",
            "message" => "Tidak ada data soal",
            "payload" => []
        ]);
    }
} else {
    // Jika query error
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Kesalahan query: " . $conn->error
    ]);
}

$conn->close();
?>