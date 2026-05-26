<?php
include "cek_login.php";
include "koneksi.php";

// Ambil data dari URL
$id = $_GET['id'];
$tab = $_GET['tab'];

// Tentukan tabel berdasarkan tab
$nama_tabel = ($tab == 'kuis') ? 'tabel_kuis' : 'tabel_cocok_kata';

// Jalankan query hapus
$sql = "DELETE FROM $nama_tabel WHERE id=$id";

if($conn->query($sql)){
    // Jika berhasil, arahkan kembali ke index dengan pesan deleted
    header("Location: index.php?tab=$tab&msg=deleted");
    exit(); 
} else {
    // Jika gagal, tampilkan pesan error
    echo "Error saat menghapus data: " . $conn->error;
}


?>