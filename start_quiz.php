<?php
require 'db.php';

// دریافت موضوعات از جدول topics
$topics = $conn->query("SELECT id, name FROM topics ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<title>شروع آزمون جدید</title>
<style>
body {
    direction: rtl;
    font-family: sans-serif;
    background: #f2f2f2;
    padding: 30px;
}
.container {
    max-width: 600px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
label {
    display: block;
    margin-bottom: 10px;
}
input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
}
button {
    background: #0073aa;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
}
button:hover {
    background: #005f87;
}
.checkbox-list {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 10px;
    background: #f9f9f9;
}
</style>
</head>

<body>
<div class="container">
    <h2>🧠 شروع آزمون جدید</h2>

    <form action="quiz.php" method="post">
        <label>نام دانش‌آموز:</label>
        <input type="text" name="student_name" required>

        <label>انتخاب موضوعات:</label>
        <div class="checkbox-list">
            <?php while($t = $topics->fetch_assoc()): ?>
                <label>
                    <input type="checkbox" name="topics[]" value="<?= $t['id'] ?>">
                    <?= htmlspecialchars($t['name']) ?>
                </label>
            <?php endwhile; ?>
        </div>

        <br>
        <button type="submit">شروع آزمون 🚀</button>
    </form>
</div>
</body>
</html>
