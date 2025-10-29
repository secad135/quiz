<?php
require 'db.php';

// دریافت موضوعات و سطوح سختی
$topics = $conn->query("SELECT * FROM topics");
$levels = $conn->query("SELECT * FROM levels");

// ثبت سؤال جدید
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("
        INSERT INTO questions (topic_id, question, code_snippet, option_a, option_b, option_c, option_d, correct_option, level_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssssssi",
        $_POST['topic_id'],
        $_POST['question'],
        $_POST['code_snippet'],
        $_POST['option_a'],
        $_POST['option_b'],
        $_POST['option_c'],
        $_POST['option_d'],
        $_POST['correct_option'],
        $_POST['level_id']
    );

    if($stmt->execute()){
        header("Location: manage_questions.php");
        exit;
    } else {
        echo "❌ خطا در ثبت سؤال: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<title>افزودن سؤال جدید</title>
<style>
body { direction: rtl; font-family: sans-serif; background: #f8f9fa; padding: 20px; }
form { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 0 8px #ccc; width: 700px; margin: auto; }
textarea, input, select { width: 100%; padding: 6px; margin-bottom: 12px; border-radius: 6px; border: 1px solid #ccc; }
button { background: #0073aa; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
button:hover { background: #005f87; }
.code-area { direction: ltr; text-align: left; background: #f5f5f5; font-family: monospace; height: 150px; }
</style>
</head>
<body>
<h2 style="text-align:center;">افزودن سؤال جدید</h2>
<form method="post">
  <label>موضوع:</label>
  <select name="topic_id" required>
    <?php while($t=$topics->fetch_assoc()): ?>
      <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
    <?php endwhile; ?>
  </select>

  <label>متن سؤال:</label>
  <textarea name="question" required></textarea>

  <label>کد مربوط به سؤال (در صورت نیاز):</label>
  <textarea name="code_snippet" class="code-area" placeholder="مثلاً کد PHP یا Python..."></textarea>

  <label>گزینه A:</label>
  <input type="text" name="option_a" required>

  <label>گزینه B:</label>
  <input type="text" name="option_b" required>

  <label>گزینه C:</label>
  <input type="text" name="option_c" required>

  <label>گزینه D:</label>
  <input type="text" name="option_d" required>

  <label>گزینه صحیح:</label>
  <select name="correct_option" required>
    <option value="A">A</option>
    <option value="B">B</option>
    <option value="C">C</option>
    <option value="D">D</option>
  </select>

  <label>سطح سختی:</label>
  <select name="level_id" required>
    <?php while($l=$levels->fetch_assoc()): ?>
      <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['level_name']) ?></option>
    <?php endwhile; ?>
  </select>

  <button type="submit">ثبت سؤال</button>
</form>
</body>
</html>
