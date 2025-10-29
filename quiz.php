<?php
require 'db.php';
session_start();

// اگر این صفحه از start_quiz.php فراخوانی شده، session پر شده است.
// ولی برای اطمینان اگر فرم مستقیم ارسال شد نیز مدیریت میکنیم.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // نام دانش‌آموز و موضوعات از فرم می‌آیند (start_quiz.php)
    if (isset($_POST['student_name'])) {
        $_SESSION['student_name'] = trim($_POST['student_name']);
    }
    if (isset($_POST['topics'])) {
        // اطمینان حاصل کن که مقادیر عددی هستند
        $_SESSION['topics'] = array_map('intval', $_POST['topics']);
    }
}

// گرفتن موضوعات از سشن
$topics = $_SESSION['topics'] ?? [];

// بررسی انتخاب موضوع
if (empty($topics) || !is_array($topics)) {
    die("❌ لطفاً حداقل یک موضوع را انتخاب کنید. (به صفحهٔ شروع آزمون بازگردید)");
}

// پاکسازی و ساختن لیست امن برای IN(...)
$topics = array_values(array_map('intval', $topics)); // اعداد صحیح
$in_list = implode(',', $topics); // الآن مانند: "1,3,5"

// تعداد سؤالات در هر آزمون
$question_limit = 20;

// کوئری: توجه کن که topic_id ها مستقیم وارد شده‌اند اما قبلاً sanitize شدند با intval.
// فقط برای LIMIT از پارامتر استفاده می‌کنیم تا از prepared statement بهره ببریم.
$sql = "
    SELECT id, topic_id, question, code_snippet, option_a, option_b, option_c, option_d, correct_option
    FROM questions
    WHERE topic_id IN ($in_list)
    ORDER BY RAND()
    LIMIT ?
";

// آماده‌سازی و اجرا
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("خطا در آماده‌سازی پرس‌وجو: " . $conn->error);
}
$stmt->bind_param("i", $question_limit);
$stmt->execute();
$result = $stmt->get_result();

// بررسی تعداد سؤالات
if ($result->num_rows === 0) {
    die("❌ هیچ سؤالی در پایگاه داده برای موضوعات انتخاب شده یافت نشد!");
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
    direction: rtl;
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
.student-info {
    text-align: center;
    margin-bottom: 20px;
    color: #555;
}
</style>
</head>

<body>
<div class="container">
    <h2>🧠 آزمون آنلاین PHP</h2>

    <?php if (!empty($_SESSION['student_name'])): ?>
        <div class="student-info">
            دانش‌آموز: <strong><?= htmlspecialchars($_SESSION['student_name']) ?></strong>
        </div>
    <?php endif; ?>

    <form action="result.php" method="post">
        <?php
        $qNumber = 1;
        while ($row = $result->fetch_assoc()):
            // نمایش سوالات
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
