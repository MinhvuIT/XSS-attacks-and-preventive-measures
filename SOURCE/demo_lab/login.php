<?php
session_start();
// Đảm bảo dòng require_once này nằm trên cùng để nạp biến $conn trước khi xử lý logic
require_once 'db.php'; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); 

    // Kiểm tra xem biến $conn đã tồn tại chưa để tránh lỗi Fatal Error
    if (isset($conn) && $conn !== null) {
        // Sử dụng real_escape_string để câu lệnh SQL không bị lỗi cú pháp
        $username_escaped = $conn->real_escape_string($username);
        $password_escaped = $conn->real_escape_string($password);

        $sql = "SELECT id FROM users WHERE username = '$username_escaped' AND password = '$password_escaped'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $_SESSION['username'] = $username; 
            header("Location: index.php");
            exit();
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
        }
    } else {
        $error = "Lỗi hệ thống: Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra lại file db.php!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Nhập - Lab XSS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); width: 300px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Đăng Nhập</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>