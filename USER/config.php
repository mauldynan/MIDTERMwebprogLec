<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "midterm_webprog";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
}
?>
