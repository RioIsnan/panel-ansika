<?php
include "cek_login.php";
include "koneksi.php";

// Pastikan tombol update diklik dari form modal
if (isset($_POST['update'])) {
    
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $tab = trim($_POST['tab']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);

    // --- LOGIKA UNTUK TAB KUIS ---
    if ($tab == 'kuis') {
        $p  = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $p0 = mysqli_real_escape_string($conn, $_POST['pil_a']);
        $p1 = mysqli_real_escape_string($conn, $_POST['pil_b']);
        $p2 = mysqli_real_escape_string($conn, $_POST['pil_c']);
        $p3 = mysqli_real_escape_string($conn, $_POST['pil_d']);
        
        // Konversi pilihan huruf ke angka index (0-3) untuk database
        $huruf_ke_angka = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
        $jw_index = $huruf_ke_angka[$_POST['jawaban']];

        // 1. Validasi Duplikat: Cek apakah pertanyaan sudah ada di ID lain
        $cek = $conn->query("SELECT id FROM tabel_kuis WHERE pertanyaan = '$p' AND id != '$id'");
        if ($cek->num_rows > 0) {
            header("Location: index.php?tab=$tab&msg=duplicate");
            exit();
        }

        // 2. Jalankan Update
        $sql = "UPDATE tabel_kuis SET 
                pertanyaan    = '$p', 
                pilihan_0     = '$p0', 
                pilihan_1     = '$p1', 
                pilihan_2     = '$p2', 
                pilihan_3     = '$p3', 
                jawaban_index = '$jw_index', 
                level         = '$level' 
                WHERE id      = '$id'";
    } 
    
    // --- LOGIKA UNTUK TAB COCOK KATA ---
    else if ($tab == 'cocok_kata' || $tab == 'cocok kata') {
        $soal = mysqli_real_escape_string($conn, $_POST['kata_soal']);
        $jawaban = mysqli_real_escape_string($conn, $_POST['kata_jawaban']);
        $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);

        // 1. Validasi Duplikat: Cek apakah kata soal sudah ada di ID lain
        $cek = $conn->query("SELECT id FROM tabel_cocok_kata WHERE kata_soal = '$soal' AND id != '$id'");
        if ($cek->num_rows > 0) {
            header("Location: index.php?tab=$tab&msg=duplicate");
            exit();
        }

        // 2. Jalankan Update
        $sql = "UPDATE tabel_cocok_kata SET 
                kata_soal    = '$soal', 
                kata_jawaban = '$jawaban', 
                level        = '$level', 
                jenis        = '$jenis' 
                WHERE id     = '$id'";
    } 
    
    else {
        die("Tab tidak valid!");
    }

    // Eksekusi Query ke Database
    if ($conn->query($sql)) {
        // Jika berhasil, kembali ke index dengan pesan updated
        header("Location: index.php?tab=" . urlencode($tab) . "&msg=updated");
        exit();
    } else {
        // Jika gagal karena error database
        echo "Error Database: " . $conn->error;
    }

} else {
    // Jika file diakses langsung tanpa melalui form modal
    header("Location: index.php");
    exit();
}

$conn->close();
?>