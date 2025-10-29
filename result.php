<?php
require 'db.php';

session_start();
$student_name = $_SESSION['student_name'] ?? 'نامشخص';
$topics_selected = implode(',', $_SESSION['topics'] ?? []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'] ?? [];

    if (empty($answers)) {
        die("❌ هیچ پاسخی ارسال نشده است!");
    }

    // استخراج کلیدهای سؤالات
    $question_ids = array_keys($answers);

    // آماده‌سازی لیست شناسه‌ها برای کوئری IN
    $placeholders = implode(',', array_fill(0, count($question_ids), '?'));
    $types = str_repeat('i', count($question_ids));

    // دریافت اطلاعات سؤالات مربوط به آزمون
    $sql = "SELECT id, question, code_snippet, option_a, option_b, option_c, option_d, correct_option
            FROM questions
            WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$question_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    // شمارنده‌ها
    $score = 0;
    $wrong = [];

    // بررسی پاسخ‌ها
    while ($row = $result->fetch_assoc()) {
        $qid = $row['id'];
        $user_answer = $answers[$qid];
        $correct = $row['correct_option'];

        if ($user_answer === $correct) {
            $score++;
        } else {
            $wrong[] = [
                'question' => $row['question'],
                'code_snippet' => $row['code_snippet'],
                'options' => [
                    'A' => $row['option_a'],
                    'B' => $row['option_b'],
                    'C' => $row['option_c'],
                    'D' => $row['option_d']
                ],
                'user_answer' => $user_answer,
                'correct_answer' => $correct
            ];
        }
    }

    $total = count($answers);
    $percentage = round(($score / $total) * 100, 2);

    // ذخیره نتیجه در جدول quiz_results
$insert = $conn->prepare("INSERT INTO quiz_results 
    (student_name, topics_selected, total_questions, correct_answers, score_percent)
    VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("ssiid", $student_name, $topics_selected, $total, $score, $percentage);
$insert->execute();

}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<title>نتیجه آزمون</title>
<style>
body {
    direction: rtl;
    font-family: sans-serif;
    background-color: #f5f5f5;
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
h2, h3 {
    text-align: center;
    color: #333;
}
.result-box {
    background: #e9f7ef;
    border: 1px solid #a5d6a7;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}
.result-box strong {
    color: #1b5e20;
}
.wrong {
    background: #fff3cd;
    border: 1px solid #ffeeba;
    padding: 15px;
    margin-top: 15px;
    border-radius: 10px;
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
}
.correct {
    color: green;
    font-weight: bold;
}
.incorrect {
    color: red;
    font-weight: bold;
}
a.button {
    display: inline-block;
    margin: 20px auto;
    padding: 12px 25px;
    background: #0073aa;
    color: white;
    text-decoration: none;
    border-radius: 8px;
}
a.button:hover {
    background: #005f87;
}
</style>
</head>

<body>
<div class="container">
    <h2>📊 نتیجه آزمون شما</h2>

    <div class="result-box">
        <p><strong>تعداد کل سؤالات:</strong> <?= $total ?></p>
        <p><strong>تعداد پاسخ‌های صحیح:</strong> <?= $score ?></p>
        <p><strong>تعداد پاسخ‌های غلط:</strong> <?= $total - $score ?></p>
        <p><strong>درصد نهایی:</strong> <?= $percentage ?>%</p>
    </div>

    <?php if (!empty($wrong)): ?>
        <h3>❌ سؤالاتی که اشتباه پاسخ داده‌اید:</h3>
        <?php foreach ($wrong as $index => $item): ?>
            <div class="wrong">
                <strong><?= $index + 1 ?>. <?= htmlspecialchars($item['question']) ?></strong>

                <?php if (!empty($item['code_snippet'])): ?>
                    <pre class="code-block"><?= htmlspecialchars($item['code_snippet']) ?></pre>
                <?php endif; ?>

                <ul>
                    <li>A) <?= htmlspecialchars($item['options']['A']) ?></li>
                    <li>B) <?= htmlspecialchars($item['options']['B']) ?></li>
                    <li>C) <?= htmlspecialchars($item['options']['C']) ?></li>
                    <li>D) <?= htmlspecialchars($item['options']['D']) ?></li>
                </ul>

                <p>پاسخ شما: <span class="incorrect"><?= htmlspecialchars($item['user_answer']) ?></span></p>
                <p>پاسخ صحیح: <span class="correct"><?= htmlspecialchars($item['correct_answer']) ?></span></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h3 style="color:green; text-align:center;">👏 تبریک! همه پاسخ‌ها صحیح بود.</h3>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="quiz.php" class="button">آزمون مجدد 🔁</a>
        <a href="index.php" class="button">بازگشت به صفحه اصلی 🏠</a>
    </div>
</div>
</body>
</html>
