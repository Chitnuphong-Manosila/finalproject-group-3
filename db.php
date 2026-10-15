<?php
// ข้อมูลการเชื่อมต่อฐานข้อมูลบน InfinityFree
$host = "sql108.infinityfree.com";        // หมายเลข 1 (MYSQL HOSTNAME)
$user = "if0_43185235";                   // หมายเลข 2 (MYSQL USERNAME)
$pass = "w9HJGmBc0WIlS";                   // หมายเลข 3 (MYSQL PASSWORD)
$dbname = "if0_43185235_final";     // หมายเลข 4 (DATABASE NAME)

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// กำหนด charset รองรับภาษาไทย
$conn->set_charset("utf8mb4");
?>