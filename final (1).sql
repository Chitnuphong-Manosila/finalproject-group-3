-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 15, 2026 at 03:20 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'default_course.jpg',
  `event_date` date NOT NULL,
  `max_seats` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `image`, `event_date`, `max_seats`) VALUES
(1, 'การพัฒนาเว็บแอปพลิเคชันด้วย PHP และ MySQL', 'เรียนรู้การสร้างเว็บแอปพลิเคชันพื้นฐานจนถึงระดับใช้งานจริง', 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=500&auto=format&fit=crop&q=60', '2026-11-05', 30),
(2, 'พื้นฐานการวิเคราะห์ข้อมูลด้วย Python', 'ปูพื้นฐานการจัดการข้อมูลและการสร้างโมเดล Machine Learning', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500&auto=format&fit=crop&q=60', '2026-11-12', 25),
(3, 'เทคนิคการออกแบบ UI/UX สำหรับระบบสารสนเทศ', 'หลักการจัดวางหน้าตาเว็บไซต์และการสร้างประสบการณ์ที่ดีแก่ผู้ใช้', 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=500&auto=format&fit=crop&q=60', '2026-11-20', 20),
(4, 'พัฒนาเว็บแอปพลิเคชันด้วย Full-Stack PHP & MySQL', 'เรียนรู้การวางโครงสร้างฐานข้อมูลและพัฒนาเว็บแอปพลิเคชันตั้งแต่เริ่มต้นจนถึงระดับนำไปใช้งานจริง', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=500&auto=format&fit=crop&q=60', '2026-11-05', 30),
(5, 'การวิเคราะห์ข้อมูลและการเขียนโปรแกรมด้วย Python', 'ปูพื้นฐานการจัดการข้อมูล การใช้ไลบรารี Pandas, NumPy และการสร้าง Data Visualization', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=500&auto=format&fit=crop&q=60', '2026-11-10', 25),
(6, 'ออกแบบ UX/UI Design สำหรับระบบสารสนเทศ', 'หลักการคิดเชิงออกแบบ (Design Thinking) การทำ Wireframe และ Prototype ด้วย Figma', 'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?w=500&auto=format&fit=crop&q=60', '2026-11-15', 20),
(7, 'การประยุกต์ใช้ IoT และระบบสมาร์ตเซนเซอร์', 'เรียนรู้การเชื่อมต่อฮาร์ดแวร์ Microcontroller, โมดูลเซนเซอร์ และการส่งข้อมูลขึ้น Cloud', 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=500&auto=format&fit=crop&q=60', '2026-11-20', 20),
(8, 'ความมั่นคงปลอดภัยทางไซเบอร์เบื้องต้น (Cybersecurity)', 'ทำความเข้าใจภัยคุกคามทางไซเบอร์ การป้องกันการโจมตีระบบเว็บ และพื้นฐานความปลอดภัยเครือข่าย', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=500&auto=format&fit=crop&q=60', '2026-11-25', 35),
(9, 'Machine Learning & AI สำหรับธุรกิจ', 'ศึกษาแนวคิดการจำแนกประเภทข้อมูล K-Means, K-NN และโมเดลทำนายพฤติกรรมลูกค้า', 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?w=500&auto=format&fit=crop&q=60', '2026-11-28', 25),
(10, 'การจัดการและเพิ่มประสิทธิภาพฐานข้อมูลขั้นสูง (SQL Tuning)', 'เทคนิคการออกแบบ Index, การจัดทำ Normalization และการเขียน Query ให้มีประสิทธิภาพสูง', 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=500&auto=format&fit=crop&q=60', '2026-12-02', 30),
(11, 'การใช้งาน Git & GitHub สำหรับการทำงานเป็นทีม', 'ควบคุมเวอร์ชันของซอฟต์แวร์ การแตก Branch, Merge Conflict และการส่งงานร่วมกันในโปรเจกต์', 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?w=500&auto=format&fit=crop&q=60', '2026-12-05', 40),
(12, 'การพัฒนาแอปพลิเคชันบนมือถือด้วย Flutter', 'สร้างแอปพลิเคชันแบบ Cross-platform รองรับทั้ง iOS และ Android ด้วยโค้ดชุดเดียว', 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=500&auto=format&fit=crop&q=60', '2026-12-10', 20),
(13, 'ทักษะการนำเสนองานและการสื่อสารในสายงานไอที', 'การเตรียมสไลด์ การนำเสนอผลงานระบบสารสนเทศ และการสื่อสารกับผู้ใช้งานอย่างมืออาชีพ', 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=500&auto=format&fit=crop&q=60', '2026-12-15', 30);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `user_id`, `course_id`, `registered_at`) VALUES
(1, 3, 1, '2026-10-15 14:07:35'),
(2, 3, 2, '2026-10-15 14:07:37'),
(3, 3, 3, '2026-10-15 14:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fullname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `role`) VALUES
(3, 'user02', '1234', 'จิราภรณ์ แสนดี', 'user'),
(4, 'admin', '1234', 'ผู้ดูแลระบบ', 'admin'),
(8, 'user03', '1234', 'ชานนท์ พิทักษ์ไทย', 'user'),
(9, 'user04', '1234', 'ณัฐวุฒิ บุญช่วย', 'user'),
(10, 'user05', '1234', 'ดนัย สมบูรณ์ทรัพย์', 'user'),
(11, 'user06', '1234', 'ทัศนีย์ วงศ์สว่าง', 'user'),
(12, 'user07', '1234', 'ธีรภัทร เอกชัย', 'user'),
(13, 'user08', '1234', 'นภัสสร รัตนกุล', 'user'),
(14, 'user09', '1234', 'บวร ปัญญาวงศ์', 'user'),
(15, 'user10', '1234', 'ปรียานุช ทองสุข', 'user'),
(16, 'user11', '1234', 'พรเทพ ศรีสวัสดิ์', 'user'),
(17, 'user12', '1234', 'มงคล ชัยเจริญ', 'user'),
(18, 'user13', '1234', 'ยิ่งศักดิ์ บุญเลิศ', 'user'),
(19, 'user14', '1234', 'รุ่งโรจน์ มั่งมี', 'user'),
(20, 'user15', '1234', 'ลลิตา เกียรติสกุล', 'user'),
(21, 'user16', '1234', 'วรวุฒิ ชัยสิทธิ์', 'user'),
(22, 'user17', '1234', 'ศศิธร เจริญผล', 'user'),
(23, 'user18', '1234', 'สมเกียรติ สิทธิพร', 'user'),
(24, 'user19', '1234', 'หทัยรัตน์ พวงแก้ว', 'user'),
(25, 'user20', '1234', 'อภิสิทธิ์ ธนะโชติ', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_course` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
