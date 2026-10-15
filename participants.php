<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$courses = $conn->query("SELECT id, title FROM courses ORDER BY event_date ASC");

$selected_course = null;
if ($course_id > 0) {
    $c_stmt = $conn->prepare("SELECT title, event_date, max_seats FROM courses WHERE id = ?");
    $c_stmt->bind_param("i", $course_id);
    $c_stmt->execute();
    $selected_course = $c_stmt->get_result()->fetch_assoc();
}

$participants = null;
if ($course_id > 0) {
    $stmt = $conn->prepare("SELECT u.fullname, u.username, r.registered_at 
                            FROM registrations r 
                            JOIN users u ON r.user_id = u.id 
                            WHERE r.course_id = ? 
                            ORDER BY r.registered_at ASC");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $participants = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบรายชื่อผู้เข้าอบรม - Training System</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Prompt', sans-serif; margin: 0; padding: 0; }
        body {
            background-color: #0f172a;
            color: #f1f5f9;
            min-height: 100vh;
        }
        /* Navbar */
        .navbar {
            background-color: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-brand { font-size: 1.3rem; font-weight: 600; color: #38bdf8; }
        .nav-menu { display: flex; gap: 16px; }
        .nav-menu a {
            text-decoration: none;
            color: #94a3b8;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .nav-menu a:hover, .nav-menu a.active { color: #ffffff; background-color: #334155; }
        .nav-menu a.active { color: #38bdf8; }
        .user-status { font-size: 0.95rem; color: #94a3b8; }
        .user-status strong { color: #f8fafc; }
        .logout-btn {
            color: #f87171 !important;
            margin-left: 15px;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            background-color: rgba(239, 68, 68, 0.1);
        }
        .logout-btn:hover { background-color: rgba(239, 68, 68, 0.25); }

        /* Container */
        .container {
            max-width: 1000px;
            margin: 35px auto;
            padding: 0 20px;
        }
        .content-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            margin-bottom: 24px;
        }
        .section-header h2 {
            font-size: 1.35rem;
            color: #f8fafc;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Filter Form */
        .filter-group {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .filter-group label {
            font-size: 0.95rem;
            color: #94a3b8;
        }
        .select-box {
            flex: 1;
            min-width: 280px;
            padding: 12px 14px;
            border: 1px solid #334155;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #f8fafc;
            background-color: #0f172a;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .select-box:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        /* Info Bar */
        .course-info-bar {
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .course-info-bar .title { font-weight: 600; color: #f8fafc; }
        .course-info-bar .badge-count {
            background-color: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Table */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead th {
            background-color: #0f172a;
            color: #94a3b8;
            font-weight: 500;
            padding: 14px 16px;
            border-bottom: 1px solid #334155;
            font-size: 0.95rem;
        }
        tbody td {
            padding: 16px;
            border-bottom: 1px solid #334155;
            vertical-align: middle;
            font-size: 0.95rem;
            color: #cbd5e1;
        }
        tbody tr:hover { background-color: rgba(51, 65, 85, 0.4); }

        .empty-placeholder {
            text-align: center;
            color: #64748b;
            padding: 40px 20px;
            font-size: 0.95rem;
        }

        /* Responsive สำหรับมือถือ */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 20px;
                gap: 12px;
            }
            .nav-menu {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            .nav-menu a {
                padding: 6px 12px;
                font-size: 0.9rem;
            }
            .user-status {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 10px;
                border-top: 1px solid #334155;
            }
            .container {
                margin: 20px auto;
                padding: 0 12px;
            }
            .content-card {
                padding: 18px 14px;
            }
            .select-box {
                min-width: 100%;
            }
            table th, table td {
                padding: 12px 10px;
                font-size: 0.85rem;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="nav-brand">Training System</div>
        <nav class="nav-menu">
            <a href="courses.php">รายการอบรม</a>
            <a href="participants.php" class="active">ตรวจสอบรายชื่อ</a>
            <a href="summary.php">สรุปผล</a>
        </nav>
        <div class="user-status">
            <span>ยินดีต้อนรับ: <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong></span>
            <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
        </div>
    </header>

    <main class="container">
        <div class="content-card">
            <div class="section-header">
                <h2>ตรวจสอบรายชื่อตามกิจกรรม</h2>
            </div>
            <form method="GET">
                <div class="filter-group">
                    <label for="course_id">เลือกกิจกรรมการอบรม:</label>
                    <select name="course_id" id="course_id" class="select-box" onchange="this.form.submit()">
                        <option value="">-- กรุณาเลือกหลักสูตรที่ต้องการดูรายชื่อ --</option>
                        <?php while ($c = $courses->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>" <?= $course_id === $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['title']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if ($course_id > 0): ?>
            <div class="content-card">
                <?php if ($selected_course): ?>
                    <div class="course-info-bar">
                        <div class="title"><?= htmlspecialchars($selected_course['title']) ?></div>
                        <div>
                            <span class="badge-count">
                                ลงทะเบียนแล้ว <?= $participants ? $participants->num_rows : 0 ?> / <?= $selected_course['max_seats'] ?> คน
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center;">ลำดับ</th>
                                <th>ชื่อ - นามสกุล</th>
                                <th>Username</th>
                                <th>วันที่และเวลาลงทะเบียน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($participants && $participants->num_rows > 0): ?>
                                <?php $i = 1; while ($p = $participants->fetch_assoc()): ?>
                                <tr>
                                    <td style="text-align: center; font-weight: 500; color: #94a3b8;"><?= $i++ ?></td>
                                    <td><strong style="color: #f8fafc;"><?= htmlspecialchars($p['fullname']) ?></strong></td>
                                    <td style="color: #94a3b8;"><?= htmlspecialchars($p['username']) ?></td>
                                    <td><?= date('d/m/Y H:i น.', strtotime($p['registered_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="empty-placeholder">
                                        ยังไม่มีผู้ลงทะเบียนในกิจกรรมนี้
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="content-card empty-placeholder">
                กรุณาเลือกหลักสูตรจากช่องด้านบนเพื่อแสดงรายชื่อผู้เข้าร่วมอบรม
            </div>
        <?php endif; ?>
    </main>

</body>
</html>