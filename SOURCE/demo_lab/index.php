<?php
session_start([
    'cookie_httponly' => false
]);
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' http://localhost:8000;");
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Thêm bình luận (Chứa lỗ hổng Stored XSS do không filter dữ liệu đầu vào)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_submit'])) {
    $content = $_POST['content']; 
    //$content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    // Ép kiểu chống SQL Injection cơ bản để bảo vệ câu lệnh, nhưng vẫn giữ nguyên chuỗi HTML/JS gây XSS
    $content_escaped = $conn->real_escape_string($content);
    
    $sql = "INSERT INTO comments (username, content) VALUES ('$username', '$content_escaped')";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

// Tìm kiếm bình luận
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
    $sql_select = "SELECT * FROM comments WHERE content LIKE '%$search_query%' ORDER BY created_at DESC";
} else {
    $sql_select = "SELECT * FROM comments ORDER BY created_at DESC";
}
$result = $conn->query($sql_select);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Bình luận - Lab XSS</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 30px auto; padding: 0 20px; background: #fafafa; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .search-box { margin: 20px 0; }
        .search-box input { padding: 8px; width: 250px; }
        .search-box button { padding: 8px 15px; }
        .comment-form textarea { width: 100%; height: 80px; margin-top: 10px; padding: 10px; box-sizing: border-box; }
        .comment-form button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; margin-top: 5px; }
        .comment-list { margin-top: 30px; }
        .comment-item { background: white; padding: 15px; border-left: 5px solid #007bff; margin-bottom: 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .comment-meta { font-size: 12px; color: #666; margin-bottom: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Xin chào, <b><?php echo htmlspecialchars($username); ?></b>!</h2>
        <a href="logout.php" style="color: red; text-decoration: none; font-weight: bold;">Đăng xuất</a>
    </div>

    <div class="search-box">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Tìm kiếm bình luận..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Tìm kiếm</button>
            <?php if (isset($_GET['search'])): ?>
                <a href="index.php">Xóa bộ lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="comment-form">
        <h3>Để lại bình luận của bạn:</h3>
        <form method="POST" action="">
            <textarea name="content" placeholder="Nhập nội dung hoặc mã script test XSS tại đây..." required></textarea>
            <br>
            <button type="submit" name="comment_submit">Gửi bình luận</button>
        </form>
    </div>

    <div class="comment-list">
        <h3>Tất cả bình luận (<?php echo $result->num_rows; ?>)</h3>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="comment-item">
                    <div class="comment-meta">
                        <b><?php echo htmlspecialchars($row['username']); ?></b> vào lúc <?php echo $row['created_at']; ?>
                    </div>
                    <div class="comment-content">
                        <?php echo $row['content']; ?>
                        
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Chưa có bình luận nào.</p>
        <?php endif; ?>
    </div>

</body>
</html>