<?php
require 'db.php';
session_start();

// دریافت اطلاعات از فرم start_quiz.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['student_id'] = (int)$_POST['student_id'];
    $_SESSION['topics'] = $_POST['topics'] ?? [];
}

// بررسی وجود student_id و topics
if (!isset($_SESSION['student_id']) || empty($_SESSION['topics'])) {
    header("Location: start_quiz.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$topics = $_SESSION['topics'];

// دریافت اطلاعات دانش‌آموز
$student_stmt = $conn->prepare("SELECT full_name, academic_year FROM students WHERE id = ?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();

if ($student_result->num_rows === 0) {
    die("❌ دانش‌آموزی با این شناسه پیدا نشد.");
}

$student = $student_result->fetch_assoc();
$student_name = $student['full_name'];

// آماده‌سازی placeholders برای IN(...)
$topic_placeholders = implode(',', array_fill(0, count($topics), '?'));
$types = str_repeat('i', count($topics));
$question_limit = 20;

// انتخاب تصادفی سوالات
$sql = "SELECT id, topic_id, question, code_snippet, option_a, option_b, option_c, option_d, correct_option
        FROM questions
        WHERE topic_id IN ($topic_placeholders)
        ORDER BY RAND()
        LIMIT ?";
$stmt = $conn->prepare($sql);
if (!$stmt) die("خطا در آماده‌سازی پرس‌وجو: " . $conn->error);

$params = array_merge($topics, [$question_limit]);
$stmt->bind_param($types . 'i', ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ هیچ سؤالی برای موضوعات انتخاب شده یافت نشد!");
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<title>آزمون آنلاین PHP</title>
<style>
body {
    direction: rtl;
    font-family: sans-serif;
    background-color: #f8f9fa;
    padding: 30px;
}
.container {
    max-width: 900px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    color: #333;
}
.student-info {
    background: #e7f3ff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-right: 4px solid #0073aa;
}
.question {
    margin-bottom: 25px;
    padding: 15px;
    border-bottom: 1px solid #ddd;
}
.code-block {
    direction: ltr;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    font-family: monospace;
    color: #222;
    margin: 10px 0;
    white-space: pre-wrap;
    overflow-x: auto;
}
label {
    display: block;
    padding: 8px;
    border-radius: 6px;
    direction: rtl;
    margin: 5px 0;
    cursor: pointer;
}
label:hover {
    background: #f0f8ff;
}
input[type="radio"] {
    margin-left: 10px;
}
button {
    background: #0073aa;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    display: block;
    margin: 30px auto;
}
button:hover {
    background: #005f87;
}
</style>
</head>

<body>
<div class="container">
    <h2>🧠 آزمون آنلاین PHP</h2>

    <div class="student-info">
        <p><strong>👨‍🎓 دانش‌آموز:</strong> <?= htmlspecialchars($student_name) ?></p>
        <p><strong>📚 موضوعات انتخاب شده:</strong> 
            <?php
            $topic_names = $conn->query("SELECT name FROM topics WHERE id IN (" . implode(',', $topics) . ")");
            $names = [];
            while($t = $topic_names->fetch_assoc()) {
                $names[] = $t['name'];
            }
            echo htmlspecialchars(implode('، ', $names));
            ?>
        </p>
        <p><strong>📊 تعداد سوالات:</strong> <?= $result->num_rows ?> سؤال</p>
    </div>

    <form action="result.php" method="post">
        <?php
        $qNumber = 1;
        while ($row = $result->fetch_assoc()):
        ?>
        <div class="question">
            <strong><?= $qNumber ?>. <?= htmlspecialchars($row['question']) ?></strong>

            <?php if (!empty($row['code_snippet'])): ?>
                <pre class="code-block"><?= htmlspecialchars($row['code_snippet']) ?></pre>
            <?php endif; ?>

            <label>
                <input type="radio" name="answers[<?= $row['id'] ?>]" value="A" required>
                <strong>A)</strong> <?= htmlspecialchars($row['option_a']) ?>
            </label>

            <label>
                <input type="radio" name="answers[<?= $row['id'] ?>]" value="B">
                <strong>B)</strong> <?= htmlspecialchars($row['option_b']) ?>
            </label>

            <label>
                <input type="radio" name="answers[<?= $row['id'] ?>]" value="C">
                <strong>C)</strong> <?= htmlspecialchars($row['option_c']) ?>
            </label>

            <label>
                <input type="radio" name="answers[<?= $row['id'] ?>]" value="D">
                <strong>D)</strong> <?= htmlspecialchars($row['option_d']) ?>
            </label>
        </div>
        <?php 
            $qNumber++;
        endwhile;
        ?>

        <button type="submit">ارسال پاسخ‌ها ✉️</button>
    </form>
</div>
</body>
</html>