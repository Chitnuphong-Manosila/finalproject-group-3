<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// สถิติภาพรวม
$stats_sql = "SELECT 
                COUNT(c.id) AS total_courses,
                COALESCE(SUM(c.max_seats), 0) AS total_capacity,
                (SELECT COUNT(id) FROM registrations) AS total_registered
              FROM courses c";
$stats_res = $conn->query($stats_sql);
$stats = $stats_res->fetch_assoc();

// รายละเอียดแต่ละหลักสูตร
$sql = "SELECT c.id, c.title, c.event_date, c.max_seats, 
               COUNT(r.id) AS total_joined,
               (c.max_seats - COUNT(r.id)) AS remaining_seats
        FROM courses c
        LEFT JOIN registrations r ON c.id = r.course_id
        GROUP BY c.id
        ORDER BY c.event_date ASC";
$summary = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สรุปภาพรวมการอบรม - Training System</title>
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
            max-width: 1050px;
            margin: 35px auto;
            padding: 0 20px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .stat-title { font-size: 0.9rem; color: #94a3b8; font-weight: 500; }
        .stat-value { font-size: 1.85rem; font-weight: 600; color: #f8fafc; }

        /* Content Card */
        .content-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .section-header h2 {
            font-size: 1.35rem;
            color: #f8fafc;
            font-weight: 600;
            margin-bottom: 24px;
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

        /* Progress Bar */
        .progress-container {
            width: 100%;
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 9999px;
            height: 8px;
            margin-top: 6px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background-color: #38bdf8;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }
        .progress-bar.full { background-color: #f87171; }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .badge-available { background-color: rgba(34, 197, 94, 0.15); color: #4ade80; }
        .badge-full { background-color: rgba(239, 68, 68, 0.15); color: #f87171; }

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
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
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
            <a href="participants.php">ตรวจสอบรายชื่อ</a>
            <a href="summary.php" class="active">สรุปผล</a>
        </nav>
        <div class="user-status">
            <span>ยินดีต้อนรับ: <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong></span>
            <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
        </div>
    </header>

    <main class="container">
        <!-- Overview Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-title">หลักสูตรทั้งหมด</span>
                <span class="stat-value"><?= number_format($stats['total_courses']) ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-title">โควตารวมทั้งหมด</span>
                <span class="stat-value"><?= number_format($stats['total_capacity']) ?> <span style="font-size: 1rem; font-weight: normal; color: #94a3b8;">ที่นั่ง</span></span>
            </div>
            <div class="stat-card">
                <span class="stat-title">ผู้ลงทะเบียนแล้วทั้งหมด</span>
                <span class="stat-value" style="color: #38bdf8;"><?= number_format($stats['total_registered']) ?> <span style="font-size: 1rem; font-weight: normal; color: #94a3b8;">คน</span></span>
            </div>
        </div>

        <div class="content-card">
            <div class="section-header">
                <h2>รายงานสรุปจำนวนผู้เข้าร่วมอบรม</h2>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>หลักสูตรอบรม</th>
                            <th>วันที่จัดอบรม</th>
                            <th style="text-align: center;">โควตาทั้งหมด</th>
                            <th style="width: 220px;">จำนวนที่ลงทะเบียน</th>
                            <th style="text-align: center;">ที่นั่งคงเหลือ</th>
                            <th style="text-align: center;">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($summary && $summary->num_rows > 0): ?>
                            <?php while ($row = $summary->fetch_assoc()): 
                                $percent = $row['max_seats'] > 0 ? round(($row['total_joined'] / $row['max_seats']) * 100) : 0;
                                $is_full = ($row['remaining_seats'] <= 0);
                            ?>
                            <tr>
                                <td><strong style="color: #f8fafc;"><?= htmlspecialchars($row['title']) ?></strong></td>
                                <td style="color: #94a3b8;"><?= date('d/m/Y', strtotime($row['event_date'])) ?></td>
                                <td style="text-align: center;"><?= $row['max_seats'] ?></td>
                                <td>
                                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #94a3b8;">
                                        <span><?= $row['total_joined'] ?> คน</span>
                                        <span><?= $percent ?>%</span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress-bar <?= $is_full ? 'full' : '' ?>" style="width: <?= min(100, $percent) ?>%;"></div>
                                    </div>
                                </td>
                                <td style="text-align: center; font-weight: 600; color: <?= $is_full ? '#f87171' : '#4ade80' ?>;">
                                    <?= max(0, $row['remaining_seats']) ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($is_full): ?>
                                        <span class="badge badge-full">ที่นั่งเต็ม</span>
                                    <?php else: ?>
                                        <span class="badge badge-available">เปิดรับสมัคร</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #64748b; padding: 30px;">
                                    ยังไม่มีข้อมูลหลักสูตรการอบรม
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