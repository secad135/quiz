<?php
require 'db.php';
session_start();

// گرفتن نام دانش‌آموز از فرم قبلی یا session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['student_name'] = trim($_POST['student_name']);
    $_SESSION['topics'] = $_POST['topics'] ?? [];
}

// بررسی انتخاب موضوعات
$topics = $_SESSION['topics'] ?? [];
if (empty($topics)) {
    die("❌ لطفاً حداقل یک موضوع را انتخاب کنید.");
}

// آماده‌سازی placeholders برای IN(...)
$topic_placeholders = implode(',', array_fill(0, count($topics), '?'));
$types = str_repeat('i', count($topics));
$question_limit = 20;

// انتخاب تصادفی سوالات از موضوعات انتخاب‌شده
$sql = "SELECT id, topic_id, question, code_snippet, option_a, option_b, option_c, option_d, correct_option
        FROM questions
        WHERE topic_id IN ($topic_placeholders)
        ORDER BY RAND()
        LIMIT ?";
$stmt = $conn->prepare($sql);
if (!$stmt) die("خطا در آماده‌سازی پرس‌وجو: " . $conn->error);

// bind_param نیاز دارد که آرایه topic_ids به صورت جداگانه + limit باشد
$params = array_merge($topics, [$question_limit]);
$stmt->bind_param($types . 'i', ...$params);
$stmt->execute();
$result = $stmt->get_result();

// بررسی تعداد سوالات
if ($result->num_rows === 0) {
    die("❌ هیچ سؤالی در پایگاه داده یافت نشد!");
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
    padding: 6px;
    border-radius: 6px;
    direction: ltr;
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
                <?= htmlspecialchars($row['option_a']) ?>
            </label>

            <label>
                <input type="radio" name="answers[<?= $row['id'] ?>]" value="B">
                <?= htmlspecialchars($row['option_b']) ?>
            </label>

            <label>
                <input type="radio" name="answers[<?= $row['id'] ?>]" value="C">
                <?= htmlspecialchars($row['option_c']) ?>
            </label>

            <label>
                <input type="radio" name="answers[<?= $row['id'] ?>]" value="D">
                <?= htmlspecialchars($row['option_d']) ?>
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
