-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 30, 2025 at 06:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `level_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`id`, `level_name`) VALUES
(1, 'آسان'),
(2, 'متوسط'),
(3, 'سخت');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `code_snippet` text DEFAULT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL,
  `level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `topic_id`, `question`, `code_snippet`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `level_id`) VALUES
(1, 1, 'کدام گزینه برای نمایش متن در PHP استفاده می‌شود؟', NULL, 'show', 'display', 'echo', 'print_text', 'C', 1),
(2, 1, 'کدهای PHP باید بین چه تگ‌هایی نوشته شوند؟', NULL, '&lt;php&gt;', '&lt;?php ?&gt;', '&lt;? ?&gt;', '&lt;script&gt;', 'B', 1),
(3, 1, 'در PHP دستور پایان هر خط کد چیست؟', NULL, 'نقطه ویرگول ;', 'دونقطه :', 'خط جدید', 'هیچکدام', 'A', 1),
(4, 1, 'کدام گزینه شروع فایل PHP را مشخص می‌کند؟', NULL, '&lt;php&gt;', '&lt;?php', '&lt;?=', '&lt;?php/&gt;', 'B', 1),
(5, 1, 'برای نوشتن توضیحات یک خطی در PHP از چه علامتی استفاده می‌شود؟', NULL, '//', '/* */', '#', 'هر دو // و #', 'D', 1),
(6, 1, 'تابع phpinfo() چه کاری انجام می‌دهد؟', NULL, 'نمایش اطلاعات PHP', 'نمایش زمان', 'نمایش تنظیمات Apache', 'خروج از برنامه', 'A', 2),
(7, 1, 'کدام گزینه درست است؟', NULL, 'PHP حساس به حروف است', 'PHP غیر حساس به حروف است', 'PHP فقط برای HTML است', 'هیچکدام', 'B', 1),
(8, 1, 'دستور require برای چیست؟', NULL, 'برای وارد کردن فایل', 'برای حذف فایل', 'برای تعریف متغیر', 'برای حلقه', 'A', 2),
(9, 2, 'متغیرها در PHP با چه علامتی شروع می‌شوند؟', NULL, '#', '$', '%', '&', 'B', 1),
(10, 2, 'کدام گزینه نام متغیر معتبر است؟', NULL, '$2num', '$num_2', '$num-2', '$ num', 'B', 1),
(11, 2, 'برای تعریف مقدار متغیر از چه عملگری استفاده می‌شود؟', NULL, ':', '=', '-', '==', 'B', 1),
(12, 2, 'کدام گزینه صحیح است؟', NULL, '$a=5; $b=$a;', '$a==5; $b==$a;', '$a=5; $b==a;', '$a:$b=5;', 'A', 1),
(13, 2, 'متغیرهای PHP از چه نوع هستند؟', NULL, 'ضعیف نوع (loosely typed)', 'قوی نوع (strongly typed)', 'ثابت نوع', 'بدون نوع', 'A', 2),
(14, 2, 'چگونه مقدار یک متغیر را حذف می‌کنیم؟', NULL, 'delete($x);', 'remove($x);', 'unset($x);', 'drop($x);', 'C', 2),
(15, 2, 'کدام گزینه مقدار متغیر را چاپ می‌کند؟', NULL, 'echo $x;', 'print($x);', 'printf($x);', 'همه موارد', 'D', 1),
(16, 2, 'چگونه می‌توان نوع متغیر را فهمید؟', NULL, 'type($x);', 'gettype($x);', 'typeof($x);', 'isset($x);', 'B', 2),
(17, 3, 'کدام یک نوع داده در PHP است؟', NULL, 'integer', 'float', 'string', 'همه موارد', 'D', 1),
(18, 3, 'تابع gettype() چه کاری انجام می‌دهد؟', NULL, 'تعیین نوع داده', 'تبدیل داده', 'حذف متغیر', 'بررسی خالی بودن', 'A', 2),
(19, 3, 'برای بررسی تهی بودن متغیر از چه تابعی استفاده می‌شود؟', NULL, 'empty()', 'isset()', 'unset()', 'is_null()', 'A', 2),
(20, 3, 'کدام مقدار نشان‌دهنده مقدار تهی است؟', NULL, '0', 'false', 'null', '\"\"', 'C', 1),
(21, 3, 'برای تبدیل عدد به رشته از چه تابعی استفاده می‌شود؟', NULL, 'strval()', 'intval()', 'floatval()', 'settype()', 'A', 2),
(22, 3, 'کدام گزینه نوع داده بولی است؟', NULL, 'True یا False', '1 یا 0', 'بله یا خیر', 'A و B هر دو', 'D', 1),
(23, 3, 'تابع var_dump() چه کاری انجام می‌دهد؟', NULL, 'نمایش مقدار و نوع داده', 'فقط مقدار را نشان می‌دهد', 'فقط نوع داده را نشان می‌دهد', 'هیچکدام', 'A', 2),
(24, 3, 'تابع is_numeric() برای چیست؟', NULL, 'بررسی رشته', 'بررسی عددی بودن', 'تبدیل داده', 'نمایش مقدار', 'B', 2),
(25, 4, 'کدام گزینه عملگر الحاق رشته‌هاست؟', NULL, '+', '.', ',', '&', 'B', 1),
(26, 4, 'کدام گزینه عملگر مقایسه‌ای است؟', NULL, '=', '==', '+', '-', 'B', 1),
(27, 4, 'نتیجه 5 == \"5\" در PHP چیست؟', NULL, 'true', 'false', 'خطا', 'null', 'A', 2),
(28, 4, 'نتیجه 5 === \"5\" در PHP چیست؟', NULL, 'true', 'false', 'null', 'خطا', 'B', 2),
(29, 4, 'عملگر افزایشی ++x چه می‌کند؟', NULL, 'قبل از استفاده مقدار را زیاد می‌کند', 'بعد از استفاده مقدار را زیاد می‌کند', 'هیچ تفاوتی ندارد', 'مقدار را نصف می‌کند', 'A', 2),
(30, 4, 'عملگر ترکیبی += چه می‌کند؟', NULL, 'جمع و انتساب', 'تفریق و انتساب', 'تقسیم و انتساب', 'افزایش مضاعف', 'A', 1),
(31, 4, 'برای بررسی همزمان دو شرط از چه عملگری استفاده می‌شود؟', NULL, '||', '&&', '&', '%', 'B', 2),
(32, 4, 'کدام گزینه خروجی (10 % 3) است؟', NULL, '1', '3', '0', '10', 'A', 1),
(33, 5, 'ساختار شرطی درست کدام است؟', NULL, 'if [x>0]', 'if (x>0)', 'if x>0', 'if {x>0}', 'B', 1),
(34, 5, 'کدام گزینه برای شرط چندحالتی استفاده می‌شود؟', NULL, 'switch', 'if', 'for', 'while', 'A', 1),
(35, 5, 'در switch برای پایان هر case از چه دستور استفاده می‌شود؟', NULL, 'exit', 'stop', 'break', 'continue', 'C', 1),
(36, 5, 'ساختار else if برای چیست؟', NULL, 'شرط جایگزین', 'حلقه تکرار', 'تعریف متغیر', 'پایان برنامه', 'A', 2),
(37, 5, 'اگر شرط if برقرار نباشد چه اتفاقی می‌افتد؟', NULL, 'برنامه متوقف می‌شود', 'قسمت else اجرا می‌شود', 'خطا می‌دهد', 'هیچ کاری نمی‌کند', 'B', 1),
(38, 5, 'دستور ternary ? : برای چیست؟', NULL, 'شرط کوتاه', 'حلقه کوتاه', 'چاپ سریع', 'تعریف متغیر', 'A', 2),
(39, 5, 'در nested if چه معنایی دارد؟', NULL, 'if درون if دیگر', 'تکرار if', 'خطای نحوی', 'همه موارد', 'A', 2),
(40, 5, 'تابع isset() چه کاری انجام می‌دهد؟', NULL, 'بررسی تعریف متغیر', 'حذف متغیر', 'تبدیل نوع', 'توقف برنامه', 'A', 2),
(41, 6, 'کدام گزینه حلقه در PHP است؟', NULL, 'for', 'while', 'do-while', 'همه موارد', 'D', 1),
(42, 6, 'حلقه for چند قسمت دارد؟', NULL, 'دو', 'سه', 'چهار', 'یک', 'B', 1),
(43, 6, 'کدام گزینه حلقه بی‌نهایت ایجاد می‌کند؟', NULL, 'for(;;)', 'while(1)', 'while(true)', 'همه موارد', 'D', 2),
(44, 6, 'کدام گزینه شرط حلقه while را نشان می‌دهد؟', NULL, 'در ابتدا', 'در انتها', 'در وسط', 'ندارد', 'A', 1),
(45, 6, 'در do...while شرط چه زمانی بررسی می‌شود؟', NULL, 'قبل از اجرا', 'بعد از اجرا', 'اصلاً بررسی نمی‌شود', 'در بین دستورات', 'B', 2),
(46, 6, 'دستور break در حلقه چه کاری می‌کند؟', NULL, 'خروج از حلقه', 'ادامه حلقه', 'شروع دوباره حلقه', 'حذف شرط', 'A', 1),
(47, 6, 'دستور continue چه کاری انجام می‌دهد؟', NULL, 'خروج کامل از حلقه', 'رفتن به تکرار بعدی', 'شروع حلقه جدید', 'هیچ', 'B', 2),
(48, 6, 'کدام گزینه شمارنده حلقه است؟', NULL, 'i++', 'i--', 'i+=1', 'همه موارد', 'D', 1),
(49, 6, 'در حلقه for چه چیزی ابتدا بررسی می‌شود؟', NULL, 'شرط', 'شمارنده', 'افزایش', 'کد داخلی', 'B', 2),
(50, 6, 'کدام گزینه خروجی درست دارد؟', NULL, 'for($i=1;$i&lt;=3;$i++) echo $i;', 'for(i=1;i&lt;3;i++) echo $i;', 'for($i=1;$i&lt;=3;i++) echo $i;', 'همه خطا دارند', 'A', 2),
(51, 6, 'کدام دستور حلقه برای تکرار نامعلوم استفاده می شود؟', NULL, 'for', 'switch', 'while', 'foreach', 'C', 1),
(52, 4, 'در قطعه کد زیر چه اشتباهی وجود دارد؟', 'if ( $x = $y ){\r\n  echo \"mosaavi hastand\" ;\r\n} else {\r\n  echo \"mosavi nistand\" ;\r\n}', 'خطا در متفیر ها', 'خطا در ساختار شرط', 'خطا در عملگر مقایسه ای', 'همه موارد', 'C', 2);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `topics` text DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `correct_answers` int(11) DEFAULT NULL,
  `wrong_answers` int(11) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_results`
--

INSERT INTO `quiz_results` (`id`, `student_id`, `student_name`, `topics`, `total_questions`, `correct_answers`, `wrong_answers`, `score`, `created_at`) VALUES
(1, NULL, 'سجاد اسماعیلی5', '5,1', 16, 15, 1, 93.75, '2025-10-29 20:25:59'),
(2, NULL, 'سجاد اسماعیلی6', '3,6,5,1,4,2', 20, 6, 14, 30.00, '2025-10-29 20:33:34');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `academic_year` int(4) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `full_name`, `academic_year`, `created_at`) VALUES
(1, 'علی آسائی', 1404, '2025-10-29 21:11:17'),
(2, 'رضا پور محمودی مقدم', 1404, '2025-10-29 21:11:54'),
(3, 'رضا پور محمودی مقدم', 1404, '2025-10-29 21:13:25'),
(4, 'رضا پور محمودی مقدم', 1404, '2025-10-29 21:14:04'),
(5, 'رضا پور محمودی مقدم', 1404, '2025-10-29 21:14:33'),
(6, 'محمدامین تاجداری', 1404, '2025-10-29 21:15:05'),
(7, 'پیروز حیدری', 1404, '2025-10-29 21:15:20');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `name`) VALUES
(1, 'سینتکس'),
(2, 'متغیرها'),
(3, 'انواع داده'),
(4, 'عملگرها'),
(5, 'ساختار شرطی'),
(6, 'حلقه‌ها');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `level_id` (`level_id`);

--
-- Indexes for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
