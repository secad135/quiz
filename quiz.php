<?php
require 'db.php';
session_start();

// بررسی اینکه اطلاعات لازم از جلسه وجود دارد
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

// دریافت سؤالات بر اساس موضوعات انتخاب‌شده
$topic_ids = implode(',', array_map('intval', $topics));
$sql = "SELECT * FROM questions WHERE topic_id IN ($topic_ids) ORDER BY RAND() LIMIT 20";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("<h3 style='text-align:center; color:red;'>❌ سؤالی برای موضوعات انتخاب‌شده وجود ندارد.</h3>");
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
            margin: 0;
            height: 100vh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
        }

        /* نوار بالایی ثابت */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #0073aa;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-sizing: border-box;
            font-size: 16px;
            font-weight: bold;
            z-index: 1000;
            border-bottom: 3px solid #005f87;
        }

        .topbar button {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .topbar button:hover {
            background: #218838;
        }

        .container {
            height: 100vh;
            overflow-y: auto;
            scroll-snap-type: y mandatory;
        }

        form {
            margin-top: 80px; /* فاصله برای نوار بالا */
        }

        .question {
            scroll-snap-align: start;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            border-bottom: 1px solid #ddd;
            box-sizing: border-box;
            background: #fff;
            max-width: 800px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        pre {
            font-size: large;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            direction: ltr;
            text-align: left;
        }

        .ltr {
            direction: ltr;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <!-- 🔹 نوار بالایی شامل نام دانش‌آموز، زمان و دکمه ثبت -->
    <div class="topbar">
        <div>👤 <?= htmlspecialchars($student_name) ?></div>
        <div id="timer">⏱ زمان باقیمانده: --:--</div>
        <button type="submit" form="quizForm">ثبت آزمون ✅</button>
    </div>

    <!-- 🔹 بدنه آزمون -->
    <div class="container">
        <form id="quizForm" action="result.php" method="post">
            <?php
            $qnum = 1;
            while ($row = $result->fetch_assoc()):
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
                        <pre><?= htmlspecialchars($row['code_snippet']) ?></pre>
                    <?php endif; ?>

                    <?php foreach ($shuffled as $key): ?>
                        <label>
                            <div class="ltr">
                                <input type="radio" name="answers[<?= $row['id'] ?>]" value="<?= $key ?>" required>
                                <?= htmlspecialchars($options[$key]) ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endwhile; ?>
        </form>
    </div>

    <!-- 🔹 تایمر -->
    <script>
        let duration = <?= $duration ?> * 60;

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
