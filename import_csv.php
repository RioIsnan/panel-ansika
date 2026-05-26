<?php
include "cek_login.php";
include "koneksi.php";

if (isset($_POST['proses_import'])) {
    $tab = $_POST['tab'];
    $file = $_FILES['file_csv']['tmp_name'];
    $nama_tabel = ($tab == 'kuis') ? 'tabel_kuis' : 'tabel_cocok_kata';

    if ($_FILES['file_csv']['size'] > 0) {
        $handle = fopen($file, "r");
        
        // Lewati baris pertama jika itu header (nama kolom)
        fgetcsv($handle, 10000, ",");

        $error_count = 0;
        $conn->begin_transaction(); // Gunakan transaksi agar data aman

        try {
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                // Bersihkan data dari karakter berbahaya
                $clean_data = array_map(function($val) use ($conn) {
                    return $conn->real_escape_string(trim($val));
                }, $data);

                if ($tab == 'kuis') {
                    // Struktur: pertanyaan, p0, p1, p2, p3, jawaban_idx, level
                    $sql = "INSERT INTO tabel_kuis (pertanyaan, pilihan_0, pilihan_1, pilihan_2, pilihan_3, jawaban_index, level) 
                            VALUES ('$clean_data[0]', '$clean_data[1]', '$clean_data[2]', '$clean_data[3]', '$clean_data[4]', '$clean_data[5]', '$clean_data[6]')";
                } else {
                    // Struktur: kata_soal, kata_jawaban, jenis, level
                    $sql = "INSERT INTO tabel_cocok_kata (kata_soal, kata_jawaban, jenis, level) 
                            VALUES ('$clean_data[0]', '$clean_data[1]', '$clean_data[2]', '$clean_data[3]')";
                }
                
                if (!$conn->query($sql)) $error_count++;
            }
            
            $conn->commit();
            fclose($handle);
            header("Location: index.php?tab=$tab&msg=success");
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: index.php?tab=$tab&msg=error");
        }
    } else {
        header("Location: index.php?tab=$tab&msg=error");
    }
} else {
    header("Location: index.php");
}
?>