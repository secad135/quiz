<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $topics_selected = $_POST['topics'] ?? [];

    if ($student_id === 0 || empty($topics_selected)) {
        die("❌ دانش‌آموز یا موضوعات انتخاب نشده‌اند!");
    }

    $_SESSION['student_id'] = $student_id;
    $_SESSION['topics'] = $topics_selected;

    // انتخاب سوالات مربوط به موضوعات انتخاب شده
    $placeholders = implode(',', array_fill(0, count($topics_selected), '?'));
    $types = str_repeat('i', count($topics_selected));

    $sql = "SELECT id, question, code_snippet, option_a, option_b, option_c, option_d, correct_option
            FROM questions
            WHERE topic_id IN ($placeholders)
            ORDER BY RAND()"; // سوالات رندم
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$topics_selected);
    $stmt->execute();
    $questions = $stmt->get_result();
} else {
    die("❌ دسترسی غیرمجاز!");
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>📝 آزمون</title>
    <style>
        body {
            font-family: sans-serif;
            direction: rtl;
            background: #f2f2f2;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .question {
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 10px;
            background: #f9f9f9;
        }

        .code-block {
            background: #333333ff;
            color: lime;
            padding: 10px;
            border-radius: 6px;
            font-family: monospace;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
            font-size: large;
        }

        ul {
            list-style: none;
            padding: 0;
            direction: ltr;
            text-align: left;
        }

        li {
            margin: 5px 0;
            direction: ltr;
            text-align: left;
        }

        button {
            background: #0073aa;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background: #005f87;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>📝 آزمون شما</h2>
        <form method="post" action="result.php">
            <?php while ($row = $questions->fetch_assoc()): ?>

                <?php
                // ساخت آرایه گزینه‌ها
                $options = [
                    'A' => $row['option_a'],
                    'B' => $row['option_b'],
                    'C' => $row['option_c'],
                    'D' => $row['option_d']
                ];

                // شافل گزینه‌ها
                $shuffled_options = [];
                foreach ($options as $key => $value) {
                    $shuffled_options[] = ['key' => $key, 'value' => $value];
                }
                shuffle($shuffled_options);
                ?>

                <div class="question">
                    <strong><?= htmlspecialchars($row['question']) ?></strong>
                    <?php if (!empty($row['code_snippet'])): ?>
                        <pre class="code-block"><?= htmlspecialchars($row['code_snippet']) ?></pre>
                    <?php endif; ?>

                    <ul>
                        <?php foreach ($shuffled_options as $opt): ?>
                            <li>
                                <label>
                                    <input type="radio" name="answers[<?= $row['id'] ?>]" value="<?= $opt['key'] ?>" required>
                                    <?= htmlspecialchars($opt['value']) ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            <?php endwhile; ?>

            <input type="hidden" name="student_id" value="<?= $_SESSION['student_id'] ?>">
            <button type="submit">ارسال پاسخ‌ها ✅</button>
        </form>
    </div>
</body>

</html>