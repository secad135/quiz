<?php
require 'db.php';
session_start();

// دریافت اطلاعات از session
$student_id = $_SESSION['student_id'] ?? null;
$student_name_from_session = $_SESSION['student_name'] ?? 'نامشخص';
$topics_selected = $_SESSION['topics'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'] ?? [];

    if (empty($answers)) {
        die("❌ هیچ پاسخی ارسال نشده است!");
    }

    // استخراج شناسه‌های سوالات
    $question_ids = array_keys($answers);

    // ساخت placeholders برای IN(...)
    $placeholders = implode(',', array_fill(0, count($question_ids), '?'));
    $types = str_repeat('i', count($question_ids));

    // دریافت سوالات مربوطه
    $sql = "SELECT id, question, code_snippet, option_a, option_b, option_c, option_d, correct_option
            FROM questions
            WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    if(!$stmt) die("خطا در آماده‌سازی پرس‌وجو: " . $conn->error);
    $stmt->bind_param($types, ...$question_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    // شمارنده‌ها
    $score = 0;
    $wrong = [];

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
    $wrong_count = $total - $score;
    $percentage = round(($score / $total) * 100, 2);

    // دریافت نام دانش‌آموز از دیتابیس اگر student_id موجود باشد
    $student_name = $student_name_from_session;
    if ($student_id) {
        $student_stmt = $conn->prepare("SELECT full_name FROM students WHERE id = ?");
        $student_stmt->bind_param("i", $student_id);
        $student_stmt->execute();
        $student_result = $student_stmt->get_result();
        if ($student_result->num_rows > 0) {
            $student_data = $student_result->fetch_assoc();
            $student_name = $student_data['full_name'];
        }
    }

    // ذخیره نتیجه در جدول quiz_results
    $topics_list = implode(',', $topics_selected);
    $insert = $conn->prepare("INSERT INTO quiz_results 
        (student_id, student_name, topics, total_questions, correct_answers, wrong_answers, score)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("issiiid", $student_id, $student_name, $topics_list, $total, $score, $wrong_count, $percentage);
    
    if ($insert->execute()) {
        // موفقیت آمیز
    } else {
        echo "❌ خطا در ذخیره نتیجه: " . $insert->error;
    }
} else {
    die("❌ دسترسی غیرمجاز!");
}

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

// دریافت نام موضوعات برای نمایش
$topics_names = getTopicNames($conn, $topics_list);
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
    margin: 0;
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
    text-align:center; 
    color:#333;
    margin-bottom: 20px;
}
.result-box { 
    background:#e9f7ef; 
    border:1px solid #a5d6a7; 
    padding:20px; 
    border-radius:10px; 
    margin-bottom:25px;
    line-height: 1.8;
}
.result-box strong { 
    color:#1b5e20;
}
.wrong { 
    background:#fff3cd; 
    border:1px solid #ffeeba; 
    padding:20px; 
    margin-top:20px; 
    border-radius:10px;
    margin-bottom: 20px;
}
.code-block { 
    direction:ltr; 
    background:#f5f5f5; 
    border:1px solid #ddd; 
    border-radius:8px; 
    padding:15px; 
    font-family:monospace; 
    color:#222; 
    margin:15px 0; 
    white-space: pre-wrap; 
    overflow-x:auto;
    font-size: 14px;
}
.correct { 
    color:green; 
    font-weight:bold;
    font-size: 16px;
}
.incorrect { 
    color:red; 
    font-weight:bold;
    font-size: 16px;
}
a.button { 
    display:inline-block; 
    margin:10px; 
    padding:12px 25px; 
    background:#0073aa; 
    color:white; 
    text-decoration:none; 
    border-radius:8px;
    transition: 0.3s;
}
a.button:hover { 
    background:#005f87;
}
.buttons-container {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}
.student-info {
    background: #e7f3ff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-right: 4px solid #0073aa;
}
.score-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 18px;
    margin: 10px 0;
}
.score-excellent { background: #28a745; color: white; }
.score-good { background: #20c997; color: white; }
.score-medium { background: #ffc107; color: #212529; }
.score-weak { background: #fd7e14; color: white; }
.score-poor { background: #dc3545; color: white; }
ul {
    list-style-type: none;
    padding: 0;
    margin: 15px 0;
}
li {
    padding: 8px;
    margin: 5px 0;
    background: #f8f9fa;
    border-radius: 6px;
    border-right: 3px solid #0073aa;
}
</style>
</head>

<body>
<div class="container">
    <h2>📊 نتیجه آزمون شما</h2>

    <div class="student-info">
        <p><strong>👨‍🎓 دانش‌آموز:</strong> <?= htmlspecialchars($student_name) ?></p>
        <?php if ($student_id): ?>
            <p><strong>🔢 کد دانش‌آموزی:</strong> <?= $student_id ?></p>
        <?php endif; ?>
        <p><strong>📚 موضوعات انتخاب شده:</strong> <?= htmlspecialchars($topics_names) ?></p>
    </div>

    <div class="result-box">
        <p><strong>📋 تعداد کل سؤالات:</strong> <?= $total ?></p>
        <p><strong>✅ تعداد پاسخ‌های صحیح:</strong> <?= $score ?></p>
        <p><strong>❌ تعداد پاسخ‌های غلط:</strong> <?= $wrong_count ?></p>
        
        <?php
        $score_class = 'score-medium';
        if ($percentage >= 90) $score_class = 'score-excellent';
        elseif ($percentage >= 75) $score_class = 'score-good';
        elseif ($percentage >= 50) $score_class = 'score-medium';
        elseif ($percentage >= 30) $score_class = 'score-weak';
        else $score_class = 'score-poor';
        ?>
        
        <p><strong>🎯 درصد نهایی:</strong> 
            <span class="score-badge <?= $score_class ?>"><?= $percentage ?>%</span>
        </p>
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
                    <li><strong>A)</strong> <?= htmlspecialchars($item['options']['A']) ?></li>
                    <li><strong>B)</strong> <?= htmlspecialchars($item['options']['B']) ?></li>
                    <li><strong>C)</strong> <?= htmlspecialchars($item['options']['C']) ?></li>
                    <li><strong>D)</strong> <?= htmlspecialchars($item['options']['D']) ?></li>
                </ul>

                <p>📝 پاسخ شما: <span class="incorrect"><?= htmlspecialchars($item['user_answer']) ?></span></p>
                <p>✅ پاسخ صحیح: <span class="correct"><?= htmlspecialchars($item['correct_answer']) ?></span></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h3 style="color:green; text-align:center;">🎉 👏 تبریک! همه پاسخ‌ها صحیح بود.</h3>
    <?php endif; ?>

    <div class="buttons-container">
        <a href="start_quiz.php" class="button">آزمون مجدد 🔁</a>
        <?php if ($student_id): ?>
            <a href="student_results.php?student_id=<?= $student_id ?>" class="button">مشاهده تمام نتایج 📈</a>
        <?php endif; ?>
        <a href="manage_students.php" class="button">مدیریت دانش‌آموزان 👥</a>
        <a href="index.php" class="button">بازگشت به صفحه اصلی 🏠</a>
    </div>
</div>
</body>
</html>