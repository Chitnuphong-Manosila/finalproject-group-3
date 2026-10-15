<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$msg_type = '';

// บันทึกการลงทะเบียน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = (int)$_POST['course_id'];

    $check = $conn->prepare("SELECT id FROM registrations WHERE user_id = ? AND course_id = ?");
    $check->bind_param("ii", $user_id, $course_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $message = "คุณได้ลงทะเบียนกิจกรรมนี้ไปแล้ว";
        $msg_type = "warning";
    } else {
        $seat_check = $conn->prepare("SELECT max_seats, (SELECT COUNT(id) FROM registrations WHERE course_id = ?) AS total_registered FROM courses WHERE id = ?");
        $seat_check->bind_param("ii", $course_id, $course_id);
        $seat_check->execute();
        $seat_res = $seat_check->get_result()->fetch_assoc();

        if ($seat_res && $seat_res['total_registered'] < $seat_res['max_seats']) {
            $insert = $conn->prepare("INSERT INTO registrations (user_id, course_id) VALUES (?, ?)");
            $insert->bind_param("ii", $user_id, $course_id);
            if ($insert->execute()) {
                $message = "ลงทะเบียนเข้าร่วมอบรมสำเร็จเรียบร้อย!";
                $msg_type = "success";
            }
        } else {
            $message = "ขออภัย ที่นั่งสำหรับหลักสูตรนี้เต็มแล้ว";
            $msg_type = "danger";
        }
    }
}

// ตรวจสอบหลักสูตรที่ลงทะเบียนไปแล้ว
$user_regs = [];
$reg_query = $conn->prepare("SELECT course_id FROM registrations WHERE user_id = ?");
$reg_query->bind_param("i", $user_id);
$reg_query->execute();
$reg_res = $reg_query->get_result();
while ($r = $reg_res->fetch_assoc()) {
    $user_regs[] = $r['course_id'];
}

// ค้นหาข้อมูลกิจกรรม
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT c.*, COUNT(r.id) AS total_registered 
        FROM courses c 
        LEFT JOIN registrations r ON c.id = r.course_id ";

if (!empty($search)) {
    $sql .= " WHERE c.title LIKE ? OR c.description LIKE ? ";
}
$sql .= " GROUP BY c.id ORDER BY c.id ASC";

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $stmt->bind_param("ss", $search_param, $search_param);
}
$stmt->execute();
$courses = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการกิจกรรมอบรม - Training System</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Prompt', sans-serif; margin: 0; padding: 0; }
        body { background-color: #0f172a; color: #f1f5f9; min-height: 100vh; }
        .navbar { background-color: #1e293b; border-bottom: 1px solid #334155; padding: 16px 40px; display: flex; justify-content: space-between; align-items: center; }
        .nav-brand { font-size: 1.3rem; font-weight: 600; color: #38bdf8; }
        .nav-menu { display: flex; gap: 16px; }
        .nav-menu a { text-decoration: none; color: #94a3b8; font-weight: 500; padding: 8px 16px; border-radius: 8px; transition: all 0.2s; }
        .nav-menu a:hover, .nav-menu a.active { color: #ffffff; background-color: #334155; }
        .nav-menu a.active { color: #38bdf8; }
        .user-status { font-size: 0.95rem; color: #94a3b8; }
        .user-status strong { color: #f8fafc; }
        .logout-btn { color: #f87171 !important; margin-left: 15px; text-decoration: none; padding: 6px 12px; border-radius: 6px; background-color: rgba(239, 68, 68, 0.1); }
        .logout-btn:hover { background-color: rgba(239, 68, 68, 0.25); }

        .container { max-width: 1100px; margin: 35px auto; padding: 0 20px; }
        .content-card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 28px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3); }
        
        .section-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
        .section-header h2 { font-size: 1.35rem; color: #f8fafc; font-weight: 600; }
        
        .search-form { display: flex; gap: 8px; }
        .search-input { padding: 8px 14px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #f8fafc; outline: none; font-size: 0.9rem; }
        .search-input:focus { border-color: #38bdf8; }
        .search-btn { background: #0284c7; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; }
        .reset-btn { background: #475569; color: white; border: none; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; }

        .alert { padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 0.95rem; }
        .alert-success { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
        .alert-warning { background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.3); }
        .alert-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead th { background-color: #0f172a; color: #94a3b8; font-weight: 500; padding: 14px 16px; border-bottom: 1px solid #334155; font-size: 0.95rem; }
        tbody td { padding: 16px; border-bottom: 1px solid #334155; vertical-align: middle; font-size: 0.95rem; color: #cbd5e1; }
        tbody tr:hover { background-color: rgba(51, 65, 85, 0.4); }

        .course-img { width: 80px; height: 55px; object-fit: cover; border-radius: 6px; border: 1px solid #334155; margin-right: 14px; flex-shrink: 0; background-color: #0f172a; }
        .course-flex { display: flex; align-items: center; }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.82rem; font-weight: 500; }
        .badge-seats { background-color: rgba(56, 189, 248, 0.15); color: #38bdf8; }
        .badge-full { background-color: rgba(239, 68, 68, 0.15); color: #f87171; }
        .btn-register { background-color: #0284c7; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 0.9rem; cursor: pointer; }
        .btn-register:hover { background-color: #0369a1; }
        .text-done { color: #4ade80; font-weight: 500; }

        @media (max-width: 768px) {
            .navbar { flex-direction: column; align-items: flex-start; padding: 16px 20px; gap: 12px; }
            .nav-menu { width: 100%; display: flex; flex-wrap: wrap; gap: 8px; }
            .user-status { width: 100%; display: flex; justify-content: space-between; border-top: 1px solid #334155; padding-top: 10px; }
            .container { margin: 20px auto; padding: 0 12px; }
            .search-form { width: 100%; }
            .search-input { flex: 1; }
            table th, table td { padding: 10px; font-size: 0.85rem; white-space: nowrap; }
        }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="nav-brand">Training System</div>
        <nav class="nav-menu">
            <a href="courses.php" class="active">รายการอบรม</a>
            <a href="participants.php">ตรวจสอบรายชื่อ</a>
            <a href="summary.php">สรุปผล</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="add_course.php" style="color: #38bdf8;">+ เพิ่มกิจกรรม</a>
            <?php endif; ?>
        </nav>
        <div class="user-status">
            <span>ยินดีต้อนรับ: <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong> (<?= $_SESSION['role'] ?>)</span>
            <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
        </div>
    </header>

    <main class="container">
        <div class="content-card">
            <div class="section-header">
                <h2>กิจกรรมอบรมที่เปิดรับสมัคร</h2>
                <form method="GET" class="search-form">
                    <input type="text" name="search" class="search-input" placeholder="ค้นหาชื่อหลักสูตร..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="search-btn">ค้นหา</button>
                    <?php if (!empty($search)): ?>
                        <a href="courses.php" class="reset-btn">ล้าง</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $msg_type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>หลักสูตรอบรม</th>
                            <th>วันที่จัดอบรม</th>
                            <th>จำนวนที่รับ</th>
                            <th>ลงทะเบียนแล้ว</th>
                            <th>สถานะการสมัคร</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($courses && $courses->num_rows > 0): ?>
                            <?php while ($row = $courses->fetch_assoc()): 
                                $is_registered = in_array($row['id'], $user_regs);
                                $is_full = ($row['total_registered'] >= $row['max_seats']);

                                if (!empty($row['image']) && filter_var($row['image'], FILTER_VALIDATE_URL)) {
                                    $img_src = $row['image'];
                                } elseif (!empty($row['image']) && file_exists('uploads/' . $row['image'])) {
                                    $img_src = 'uploads/' . $row['image'];
                                } else {
                                    $img_src = 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500&auto=format&fit=crop&q=60';
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="course-flex">
                                        <img src="<?= htmlspecialchars($img_src) ?>" alt="Course Image" class="course-img">
                                        <div>
                                            <strong style="color: #f8fafc;"><?= htmlspecialchars($row['title']) ?></strong>
                                            <?php if (!empty($row['description'])): ?>
                                                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 4px;">
                                                    <?= htmlspecialchars($row['description']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['event_date'])) ?></td>
                                <td><span class="badge badge-seats"><?= $row['max_seats'] ?> ที่นั่ง</span></td>
                                <td><?= $row['total_registered'] ?> คน</td>
                                <td>
                                    <?php if ($is_registered): ?>
                                        <span class="text-done">&#10003; ลงทะเบียนแล้ว</span>
                                    <?php elseif ($is_full): ?>
                                        <span class="badge badge-full">ที่นั่งเต็ม</span>
                                    <?php else: ?>
                                        <form method="POST" style="margin: 0;">
                                            <input type="hidden" name="course_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn-register">ลงทะเบียน</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 30px;">
                                    ไม่พบข้อมูลหลักสูตรการอบรม
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>