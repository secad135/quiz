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
// تغییر: محدود کردن سوالات به 20 مورد
$sql = "SELECT * FROM questions WHERE topic_id IN ($topic_ids) ORDER BY RAND() LIMIT 20";
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            direction: rtl;
            font-family: sans-serif;
            background-color: #f4f6f8;
            height: 100vh;
            overflow: hidden;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #0073aa;
            color: white;
            padding: 15px 20px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .student-info {
            font-weight: bold;
            font-size: 16px;
        }

        #timer {
            font-size: 18px;
            font-weight: bold;
        }

        .progress-container {
            position: fixed;
            top: 70px;
            left: 20px;
            right: 20px;
            background: #e0e0e0;
            border-radius: 10px;
            height: 8px;
            z-index: 1000;
        }

        .progress-bar {
            height: 100%;
            background: #28a745;
            border-radius: 10px;
            width: 0%;
            transition: width 0.3s ease;
        }

        .questions-container {
            height: 100vh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
            padding-top: 120px;
            padding-bottom: 80px;
        }

        .question-section {
            height: 100vh;
            scroll-snap-align: start;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 20px;
            background: white;
            margin: 10px 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }

        .question-number {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #0073aa;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .question-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }

        .question-text {
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #333;
        }

        .code-snippet {
            direction: ltr;
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.4;
            overflow-x: auto;
            white-space: pre-wrap;
        }

        .options-container {
            margin-top: 20px;
        }

        .option {
            direction: ltr;
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .option:hover {
            background: #e9ecef;
            border-color: #0073aa;
        }

        .option input[type="radio"] {
            margin-left: 10px;
            transform: scale(1.2);
        }

        .option label {
            cursor: pointer;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 15px 20px;
            border-top: 2px solid #e9ecef;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navigation-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .prev-btn {
            background: #6c757d;
            color: white;
        }

        .prev-btn:hover {
            background: #5a6268;
        }

        .next-btn {
            background: #0073aa;
            color: white;
        }

        .next-btn:hover {
            background: #005f87;
        }

        .submit-btn {
            background: #28a745;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #218838;
        }

        .current-question {
            font-weight: bold;
            color: #0073aa;
        }

        /* مخفی کردن اسکرول بار اما فعال نگه داشتن اسکرول */
        .questions-container::-webkit-scrollbar {
            display: none;
        }

        .questions-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body>
    <!-- هدر ثابت -->
    <div class="header">
        <div class="student-info">👤 دانش‌آموز: <?= htmlspecialchars($student_name) ?></div>
        <div id="timer">⏱ زمان باقیمانده: 00:00</div>
    </div>

    <!-- نوار پیشرفت -->
    <div class="progress-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <!-- کانتینر سوالات با اسکرول اسنپ -->
    <div class="questions-container" id="questionsContainer">
        <form id="quizForm" action="result.php" method="post">
            <?php
            $qnum = 1;
            $total_questions = $result->num_rows;
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
                <div class="question-section" id="question-<?= $qnum ?>">
                    <div class="question-number"><?= $qnum ?></div>
                    <div class="question-content">
                        <div class="question-text">
                            <strong>سوال <?= $qnum ?>:</strong> <?= htmlspecialchars($row['question']) ?>
                        </div>
                        
                        <?php if (!empty($row['code_snippet'])): ?>
                            <pre class="code-snippet"><?= htmlspecialchars($row['code_snippet']) ?></pre>
                        <?php endif; ?>

                        <div class="options-container">
                            <?php foreach ($shuffled as $key): ?>
                                <div class="option">
                                    <label>
                                        <input type="radio" name="answers[<?= $row['id'] ?>]" value="<?= $key ?>">
                                        <strong><?= $key ?>)</strong> <?= htmlspecialchars($options[$key]) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php 
                $qnum++;
            endwhile; 
            ?>
        </form>
    </div>

    <!-- فوتر ثابت با دکمه‌های ناوبری -->
    <div class="footer">
        <div class="navigation-buttons">
            <button class="nav-btn prev-btn" onclick="scrollToPreviousQuestion()">⬅ سوال قبلی</button>
            <button class="nav-btn next-btn" onclick="scrollToNextQuestion()">سوال بعدی ➡</button>
        </div>
        
        <div class="current-question" id="currentQuestionInfo">
            سوال 1 از <?= $total_questions ?>
        </div>
        
        <button type="button" class="submit-btn" onclick="submitQuiz()">ثبت آزمون ✅</button>
    </div>

    <script>
        let duration = <?= $duration ?> * 60; // تبدیل دقیقه به ثانیه
        const totalQuestions = <?= $total_questions ?>;
        let currentQuestion = 1;

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

        function updateProgress() {
            const progress = ((currentQuestion - 1) / totalQuestions) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }

        function updateCurrentQuestionInfo() {
            document.getElementById('currentQuestionInfo').textContent = 
                `سوال ${currentQuestion} از ${totalQuestions}`;
        }

        function scrollToQuestion(questionNumber) {
            const questionElement = document.getElementById(`question-${questionNumber}`);
            if (questionElement) {
                questionElement.scrollIntoView({ behavior: 'smooth' });
                currentQuestion = questionNumber;
                updateProgress();
                updateCurrentQuestionInfo();
            }
        }

        function scrollToNextQuestion() {
            if (currentQuestion < totalQuestions) {
                scrollToQuestion(currentQuestion + 1);
            }
        }

        function scrollToPreviousQuestion() {
            if (currentQuestion > 1) {
                scrollToQuestion(currentQuestion - 1);
            }
        }

        function submitQuiz() {
            if (confirm('آیا از ثبت پاسخ‌های خود اطمینان دارید؟')) {
                document.getElementById('quizForm').submit();
            }
        }

        // مدیریت اسکرول با کیبورد
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowDown' || event.key === ' ') {
                event.preventDefault();
                scrollToNextQuestion();
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                scrollToPreviousQuestion();
            }
        });

        // تشخیص اسکرول کاربر و آپدیت سوال جاری
        const questionsContainer = document.getElementById('questionsContainer');
        questionsContainer.addEventListener('scroll', function() {
            const questionElements = document.querySelectorAll('.question-section');
            let closestQuestion = 1;
            let closestDistance = Infinity;

            questionElements.forEach((element, index) => {
                const rect = element.getBoundingClientRect();
                const distance = Math.abs(rect.top);
                if (distance < closestDistance) {
                    closestDistance = distance;
                    closestQuestion = index + 1;
                }
            });

            if (closestQuestion !== currentQuestion) {
                currentQuestion = closestQuestion;
                updateProgress();
                updateCurrentQuestionInfo();
            }
        });

        // شروع تایمر و تنظیمات اولیه
        let timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
        updateProgress();
        updateCurrentQuestionInfo();

        // اسکرول به اولین سوال هنگام لود صفحه
        window.onload = function() {
            scrollToQuestion(1);
        };
    </script>

</body>

</html>
[file content end]