<?php
include "cek_login.php";
include "koneksi.php";

if (isset($_POST['submit'])) {
    $tab = $_GET['tab'];

    if ($tab == 'kuis') {
        // Ambil data dari Modal Kuis
        $template = $_POST['template_soal']; 
        $kata_kuis = mysqli_real_escape_string($conn, $_POST['kata_kuis']);
        
        // Gabungkan menjadi satu kalimat pertanyaan
        $pertanyaan = $template . " " . $kata_kuis . "?";

        // --- VALIDASI DUPLIKAT KUIS ---
        $cek = $conn->query("SELECT id FROM tabel_kuis WHERE pertanyaan = '$pertanyaan'");
        if ($cek->num_rows > 0) {
            header("Location: index.php?tab=$tab&msg=duplicate");
            exit();
        }
        // ------------------------------
        
        $p0 = mysqli_real_escape_string($conn, $_POST['pilihan_0']);
        $p1 = mysqli_real_escape_string($conn, $_POST['pilihan_1']);
        $p2 = mysqli_real_escape_string($conn, $_POST['pilihan_2']);
        $p3 = mysqli_real_escape_string($conn, $_POST['pilihan_3']);
        
        $jawaban_index = (int)$_POST['jawaban_manusia'] - 1; 
        $level = $_POST['level'];

        $sql = "INSERT INTO tabel_kuis (pertanyaan, pilihan_0, pilihan_1, pilihan_2, pilihan_3, jawaban_index, level) 
                VALUES ('$pertanyaan', '$p0', '$p1', '$p2', '$p3', '$jawaban_index', '$level')";

    } else {
        // Ambil data dari Modal Cocok Kata
        $kata_soal = mysqli_real_escape_string($conn, $_POST['kata_soal']);

        // --- VALIDASI DUPLIKAT COCOK KATA ---
        $cek = $conn->query("SELECT id FROM tabel_cocok_kata WHERE kata_soal = '$kata_soal'");
        if ($cek->num_rows > 0) {
            header("Location: index.php?tab=$tab&msg=duplicate");
            exit();
        }
        // ------------------------------------

        $kata_jawaban = mysqli_real_escape_string($conn, $_POST['kata_jawaban']);
        $jenis = $_POST['jenis'];
        $level = $_POST['level'];

        $sql = "INSERT INTO tabel_cocok_kata (kata_soal, kata_jawaban, jenis, level) 
                VALUES ('$kata_soal', '$kata_jawaban', '$jenis', '$level')";
    }

    // Eksekusi Query
    if ($conn->query($sql)) {
        header("Location: index.php?tab=$tab&msg=success");
    } else {
        header("Location: index.php?tab=$tab&msg=error");
    }
}
?>