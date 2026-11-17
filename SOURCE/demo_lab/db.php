<?php
$host = "localhost:3307";
$user = "root";
$pass = ""; // Mặc định của XAMPP để trống
$dbname = "demo_lab";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>