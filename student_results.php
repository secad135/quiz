<?php
require 'db.php';

if (!isset($_GET['student_id'])) {
    die("❌ شناسه دانش‌آموز ارسال نشده است.");
}

$student_id = (int)$_GET['student_id'];

// دریافت اطلاعات دانش‌آموز
$student_stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();

if ($student_result->num_rows === 0) {
    die("❌ دانش‌آموزی با این شناسه پیدا نشد.");
}

$student = $student_result->fetch_assoc();

// دریافت نتایج دانش‌آموز
$results_stmt = $conn->prepare("
    SELECT * FROM quiz_results 
    WHERE student_id = ? 
    ORDER BY created_at DESC
");
$results_stmt->bind_param("i", $student_id);
$results_stmt->execute();
$results = $results_stmt->get_result();

// تابع برای دریافت نام موضوعات
function getTopicNames($conn, $topic_ids_string) {
    if (empty($topic_ids_string)) return '';
    $ids = array_map('intval', explode(',', $topic_ids_string));
    $placeholders = implode(',', $ids);
    $res = $conn->query("SELECT name FROM topics WHERE id IN ($placeholders)");
    $names = [];
    while($row = $res->fetch_assoc()) {
        $names[] = $row['name'];
    }
    return implode(', ', $names);
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<title>نتایج آزمون‌های <?= htmlspecialchars($student['full_name']) ?></title>
<style>
body {
    direction: rtl;
    font-family: sans-serif;
    background-color: #f4f6f8;
    padding: 30px;
}
.container {
    max-width: 1000px;
    margin: auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.student-info {
    background: #e7f3ff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    border-right: 5px solid #0073aa;
}
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 10px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
}
th {
    background: #0073aa;
    color: white;
}
tr:nth-child(even) {
    background: #f9f9f9;
}
.badge {
    padding: 6px 12px;
    border-radius: 8px;
    color: white;
    font-weight: bold;
}
.excellent { background: #28a745; }
.good { background: #20c997; }
.medium { background: #ffc107; color: #222; }
.weak { background: #fd7e14; }
.poor { background: #dc3545; }
.no-results {
    text-align: center;
    padding: 40px;
    color: #666;
    font-size: 18px;
}
.links {
    text-align: center;
    margin-top: 25px;
}
.links a {
    display: inline-block;
    margin: 5px;
    padding: 10px 20px;
    background: #0073aa;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}
.links a:hover {
    background: #005f87;
}
</style>
</head>

<body>
<div class="container">
    <h2>📊 نتایج آزمون‌های دانش‌آموز</h2>
    
    <div class="student-info">
        <h3>👨‍🎓 <?= htmlspecialchars($student['full_name']) ?></h3>
        <p><strong>سال تحصیلی:</strong> <?= htmlspecialchars($student['academic_year']) ?></p>
        <p><strong>تعداد آزمون‌های انجام شده:</strong> <?= $results->num_rows ?></p>
    </div>

    <?php if ($results->num_rows > 0): ?>
        <table>
            <tr>
                <th>#</th>
                <th>موضوعات</th>
                <th>تعداد سؤالات</th>
                <th>پاسخ صحیح</th>
                <th>پاسخ غلط</th>
                <th>نمره (%)</th>
                <th>وضعیت</th>
                <th>تاریخ آزمون</th>
            </tr>

            <?php
            $count = 1;
            while ($row = $results->fetch_assoc()):
                $score = $row['score'];
                if ($score >= 90) $badgeClass = 'excellent';
                elseif ($score >= 75) $badgeClass = 'good';
                elseif ($score >= 50) $badgeClass = 'medium';
                elseif ($score >= 30) $badgeClass = 'weak';
                else $badgeClass = 'poor';
            ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars(getTopicNames($conn, $row['topics'])) ?></td>
                    <td><?= $row['total_questions'] ?></td>
                    <td><?= $row['correct_answers'] ?></td>
                    <td><?= $row['wrong_answers'] ?></td>
                    <td><?= $row['score'] ?>%</td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $row['score'] ?>%</span></td>
                    <td><?= date("Y/m/d - H:i", strtotime($row['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="no-results">
            📝 این دانش‌آموز هنوز هیچ آزمونی انجام نداده است.
        </div>
    <?php endif; ?>

    <div class="links">
        <a href="manage_students.php">👥 مدیریت دانش‌آموزان</a>
        <a href="start_quiz.php">📝 شروع آزمون جدید</a>
        <a href="index.php">🏠 صفحه اصلی</a>
    </div>
</div>
</body>
</html>