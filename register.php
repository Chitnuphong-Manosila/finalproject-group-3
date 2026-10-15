<?php
require_once 'config.php';

$course_id = intval($_GET['course_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    die("ไม่พบข้อมูลหลักสูตรที่เลือก");
}

$msg = '';
$is_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // ตรวจสอบที่นั่งว่างก่อนบันทึก
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE course_id = ?");
    $check_stmt->execute([$course_id]);
    $current_count = $check_stmt->fetchColumn();

    if ($current_count >= $course['max_seats']) {
        $msg = "ขออภัย ที่นั่งสำหรับหลักสูตรนี้เต็มแล้ว";
    } else {
        $ins = $pdo->prepare("INSERT INTO registrations (course_id, participant_name, email, phone) VALUES (?, ?, ?, ?)");
        if ($ins->execute([$course_id, $name, $email, $phone])) {
            $msg = "ลงทะเบียนเรียบร้อยแล้ว!";
            $is_success = true;
        } else {
            $msg = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ลงทะเบียนอบรม</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="card-title mb-1">ลงทะเบียนเข้าร่วมอบรม</h4>
            <p class="text-muted mb-4"><?= htmlspecialchars($course['title']) ?></p>

            <?php if ($msg): ?>
                <div class="alert <?= $is_success ? 'alert-success' : 'alert-danger' ?>">
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>

            <?php if (!$is_success): ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">ชื่อ - นามสกุล</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">อีเมล</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">เบอร์โทรศัพท์</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">ยืนยันการลงทะเบียน</button>
                </form>
            <?php endif; ?>
            <div class="text-center mt-3">
                <a href="courses.php" class="text-secondary text-decoration-none">กลับหน้ารายการอบรม</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>