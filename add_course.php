<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: courses.php");
    exit;
}

$message = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $max_seats = (int)$_POST['max_seats'];
    $image_name = null;

    // การจัดการไฟล์อัปโหลดรูปภาพ
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['course_image']['tmp_name'];
        $file_name = $_FILES['course_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed)) {
            $new_filename = uniqid('course_', true) . '.' . $file_ext;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            if (move_uploaded_file($file_tmp, 'uploads/' . $new_filename)) {
                $image_name = $new_filename;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO courses (title, description, image, event_date, max_seats) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $description, $image_name, $event_date, $max_seats);
    
    if ($stmt->execute()) {
        header("Location: courses.php");
        exit;
    } else {
        $message = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
        $msg_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มกิจกรรมการอบรม - Training System</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Prompt', sans-serif; margin: 0; padding: 0; }
        body { background-color: #0f172a; color: #f1f5f9; min-height: 100vh; }
        .navbar { background-color: #1e293b; border-bottom: 1px solid #334155; padding: 16px 40px; display: flex; justify-content: space-between; align-items: center; }
        .nav-brand { font-size: 1.3rem; font-weight: 600; color: #38bdf8; }
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .content-card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 30px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; color: #94a3b8; font-size: 0.95rem; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #334155; background: #0f172a; color: #f8fafc; border-radius: 8px; outline: none; }
        .form-control:focus { border-color: #38bdf8; }
        .btn-submit { background: #0284c7; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; width: 100%; font-size: 1rem; margin-top: 10px; }
        .btn-submit:hover { background: #0369a1; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #94a3b8; text-decoration: none; }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="nav-brand">Training System (Admin)</div>
    </header>
    <main class="container">
        <div class="content-card">
            <h2 style="margin-bottom: 20px;">เพิ่มกิจกรรมการอบรมใหม่</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>ชื่อหลักสูตร:</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>รายละเอียด:</label>
                    <textarea name="description" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>รูปภาพกิจกรรม (Upload):</label>
                    <input type="file" name="course_image" accept="image/*" class="form-control">
                </div>
                <div class="form-group">
                    <label>วันที่จัดอบรม:</label>
                    <input type="date" name="event_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>จำนวนที่นั่ง (โควตา):</label>
                    <input type="number" name="max_seats" min="1" class="form-control" required>
                </div>
                <button type="submit" class="btn-submit">บันทึกกิจกรรม</button>
                <a href="courses.php" class="back-link">ยกเลิกและย้อนกลับ</a>
            </form>
        </div>
    </main>
</body>
</html>