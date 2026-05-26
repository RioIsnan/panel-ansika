<?php
include "cek_login.php";
include "koneksi.php";

$sql = "SELECT * FROM tabel_cocok_kata";
$result = $conn->query($sql);
$data = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>