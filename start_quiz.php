<?php
require 'db.php';

// دریافت دانش‌آموزان و موضوعات
$students = $conn->query("SELECT id, full_name, academic_year FROM students ORDER BY full_name ASC");
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .checkbox-list {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            background: #f9f9f9;
            margin-bottom: 15px;
            max-height: 200px;
            overflow-y: auto;
        }
        .checkbox-list label {
            display: block;
            padding: 8px;
            margin: 5px 0;
            background: white;
            border-radius: 6px;
            border: 1px solid #eee;
        }
        .checkbox-list label:hover {
            background: #f0f8ff;
        }
        button {
            background: #0073aa;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #005f87;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .links a:hover {
            background: #218838;
        }
        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8d7da;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 style="text-align:center;">🧠 شروع آزمون جدید</h2>

        <?php if ($students->num_rows === 0): ?>
            <div class="error">
                ❌ هیچ دانش‌آموزی ثبت‌نام نشده است. لطفاً ابتدا دانش‌آموز ثبت‌نام کنید.
            </div>
        <?php endif; ?>

        <form action="quiz.php" method="post" onsubmit="return validateForm()">
            <label>انتخاب دانش‌آموز:</label>
            <select name="student_id" required <?= $students->num_rows === 0 ? 'disabled' : '' ?>>
                <option value="">-- انتخاب دانش‌آموز --</option>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['full_name']) ?> - سال <?= htmlspecialchars($s['academic_year']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>انتخاب موضوعات آزمون (حداقل یک موضوع):</label>
            <div class="checkbox-list">
                <?php
                $topics->data_seek(0); // Reset pointer
                while ($t = $topics->fetch_assoc()):
                ?>
                    <label>
                        <input type="checkbox" name="topics[]" value="<?= $t['id'] ?>">
                        <?= htmlspecialchars($t['name']) ?>
                    </label>
                <?php endwhile; ?>
            </div>

            <button type="submit" <?= $students->num_rows === 0 ? 'disabled' : '' ?>>شروع آزمون 🚀</button>
        </form>

        <div class="links">
            <a href="register_student.php">➕ ثبت‌نام دانش‌آموز جدید</a>
            <a href="manage_students.php">👥 مدیریت دانش‌آموزان</a>
            <a href="index.php">🏠 صفحه اصلی</a>
        </div>
    </div>

    <script>
        function validateForm() {
            const checkboxes = document.querySelectorAll('input[name="topics[]"]:checked');
            if (checkboxes.length === 0) {
                alert('لطفاً حداقل یک موضوع را انتخاب کنید.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>