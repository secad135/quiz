<?php
require 'db.php';

// دریافت موضوعات و سطوح سختی برای منوی انتخابی
$topics = $conn->query("SELECT * FROM topics");
$levels = $conn->query("SELECT * FROM levels");

// بررسی اینکه آیا ID ارسال شده است
if (!isset($_GET['id'])) {
    die("❌ شناسه سؤال ارسال نشده است.");
}

$id = (int)$_GET['id'];

// دریافت اطلاعات سؤال از پایگاه داده
$stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ سؤالی با این شناسه پیدا نشد.");
}

$question = $result->fetch_assoc();

// در صورت ارسال فرم برای ویرایش
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("
        UPDATE questions 
        SET topic_id=?, question=?, code_snippet=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_option=?, level_id=? 
        WHERE id=?
    ");
    $stmt->bind_param(
        "issssssssi",
        $_POST['topic_id'],
        $_POST['question'],
        $_POST['code_snippet'],
        $_POST['option_a'],
        $_POST['option_b'],
        $_POST['option_c'],
        $_POST['option_d'],
        $_POST['correct_option'],
        $_POST['level_id'],
        $id
    );

    if ($stmt->execute()) {
        header("Location: manage_questions.php");
        exit;
    } else {
        echo "❌ خطا در ویرایش سؤال: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>ویرایش سؤال</title>
    <style>
        body {
            direction: rtl;
            font-family: sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 8px #ccc;
            width: 700px;
            margin: auto;
        }

        textarea,
        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            background: #0073aa;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background: #005f87;
        }

        .code-area {
            direction: ltr;
            text-align: left;
            background: #f5f5f5;
            font-family: monospace;
            height: 150px;
        }
        .ltr{
            direction: ltr;
        }
    </style>
</head>

<body>
    <h2 style="text-align:center;">✏️ ویرایش سؤال</h2>

    <form method="post">

        <label>موضوع:</label>
        <select name="topic_id" required>
            <?php while ($t = $topics->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>" <?= ($t['id'] == $question['topic_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>متن سؤال:</label>
        <textarea name="question" required><?= htmlspecialchars($question['question']) ?></textarea>

        <label>کد مربوط به سؤال (در صورت نیاز):</label>
        <textarea name="code_snippet" class="code-area" placeholder="کد PHP یا HTML یا Python..."><?= htmlspecialchars($question['code_snippet']) ?></textarea>
        <div class="ltr">
            <label>گزینه A:</label>
            <input type="text" name="option_a" value="<?= htmlspecialchars($question['option_a']) ?>" required>

            <label>گزینه B:</label>
            <input type="text" name="option_b" value="<?= htmlspecialchars($question['option_b']) ?>" required>

            <label>گزینه C:</label>
            <input type="text" name="option_c" value="<?= htmlspecialchars($question['option_c']) ?>" required>

            <label>گزینه D:</label>
            <input type="text" name="option_d" value="<?= htmlspecialchars($question['option_d']) ?>" required>
        </div>
        <label>گزینه صحیح:</label>
        <select name="correct_option" required>
            <option value="A" <?= ($question['correct_option'] == 'A') ? 'selected' : '' ?>>A</option>
            <option value="B" <?= ($question['correct_option'] == 'B') ? 'selected' : '' ?>>B</option>
            <option value="C" <?= ($question['correct_option'] == 'C') ? 'selected' : '' ?>>C</option>
            <option value="D" <?= ($question['correct_option'] == 'D') ? 'selected' : '' ?>>D</option>
        </select>

        <label>سطح سختی:</label>
        <select name="level_id" required>
            <?php
            // مجدداً کوئری می‌زنیم چون $levels در بالا قبلاً پیمایش شده
            $levels2 = $conn->query("SELECT * FROM levels");
            while ($l = $levels2->fetch_assoc()):
            ?>
                <option value="<?= $l['id'] ?>" <?= ($l['id'] == $question['level_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($l['level_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">💾 ذخیره تغییرات</button>
        <a href="manage_questions.php" style="margin-right:10px; text-decoration:none; color:#0073aa;">بازگشت به مدیریت</a>
    </form>
</body>

</html>