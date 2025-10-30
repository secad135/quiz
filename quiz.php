<?php
require 'db.php';
session_start();

if (!isset($_SESSION['student_id'], $_SESSION['topics'], $_SESSION['duration'])) {
    header("Location: start_quiz.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$topics = $_SESSION['topics'];
$duration = $_SESSION['duration'];

// دریافت نام دانش‌آموز
$student_stmt = $conn->prepare("SELECT full_name FROM students WHERE id = ?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student = $student_result->fetch_assoc();
$student_name = $student ? $student['full_name'] : 'نامشخص';

$topic_ids = implode(',', array_map('intval', $topics));
$sql = "SELECT * FROM questions WHERE topic_id IN ($topic_ids) ORDER BY RAND()";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("<h3>❌ سؤالی برای موضوعات انتخاب‌شده وجود ندارد.</h3>");
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>آزمون در حال اجرا</title>
    <style>
        body {
            direction: rtl;
            font-family: sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .question {
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .ltr {
            direction: ltr;
            text-align: left;
        }

        button {
            background: #0073aa;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #005f87;
        }
        #timer {
            position: sticky;
            top: 0;
            background: #0073aa;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            z-index: 1000;
            border-bottom: 3px solid #005f87;
        }
        pre{
            font-size: large;
            background-color: lightgray;
            padding: 5px;
            font-weight: 600;
        }
        .student-info {
            text-align: center;
            background: #e8f5e8;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-right: 4px solid #28a745;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>🧠 آزمون فعال</h2>
        
        <!-- نمایش نام دانش‌آموز -->
        <div class="student-info">
            👤 دانش‌آموز: <?= htmlspecialchars($student_name) ?>
        </div>
        
        <div id="timer"></div>

        <form id="quizForm" action="result.php" method="post">
            <?php
            $qnum = 1;
            while ($row = $result->fetch_assoc()):
                // گزینه‌ها را تصادفی کن
                $options = [
                    'A' => $row['option_a'],
                    'B' => $row['option_b'],
                    'C' => $row['option_c'],
                    'D' => $row['option_d']
                ];
                $shuffled = array_keys($options);
                shuffle($shuffled);
            ?>
                <div class="question">
                    <p><strong><?= $qnum++ ?>.</strong> <?= htmlspecialchars($row['question']) ?></p>
                    <?php if (!empty($row['code_snippet'])): ?>
                        <pre class="ltr"><?= htmlspecialchars($row['code_snippet']) ?></pre>
                    <?php endif; ?>

                    <?php foreach ($shuffled as $key): ?>
                        <label>
                            <div class="ltr">
                            <input type="radio" name="answers[<?= $row['id'] ?>]" value="<?= $key ?>">
                            <?= htmlspecialchars($options[$key]) ?>
                            </div>
                        </label><br>
                    <?php endforeach; ?>
                </div>
            <?php endwhile; ?>

            <div style="text-align:center;">
                <button type="submit">ثبت آزمون ✅</button>
            </div>
        </form>
    </div>

    <script>
        let duration = <?= $duration ?> * 60; // تبدیل دقیقه به ثانیه

        function updateTimer() {
            let minutes = Math.floor(duration / 60);
            let seconds = duration % 60;
            document.getElementById('timer').textContent =
                `⏱ زمان باقیمانده: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            if (duration <= 0) {
                clearInterval(timerInterval);
                alert("⏰ زمان آزمون به پایان رسید!");
                document.getElementById('quizForm').submit();
            }
            duration--;
        }
        let timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    </script>

</body>

</html>