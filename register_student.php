<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $academic_year = (int)$_POST['academic_year'];

    // اعتبارسنجی سال تحصیلی (بین 1300 تا 1500)
    if (empty($full_name) || $academic_year < 1300 || $academic_year > 1500) {
        $error = "لطفاً تمام فیلدها را به درستی پر کنید. سال تحصیلی باید یک عدد چهار رقمی باشد.";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (full_name, academic_year) VALUES (?, ?)");
        $stmt->bind_param("si", $full_name, $academic_year);

        if ($stmt->execute()) {
            $success = "✅ دانش‌آموز با موفقیت ثبت‌نام شد.";
        } else {
            $error = "❌ خطا در ثبت‌نام: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>ثبت‌نام دانش‌آموز جدید</title>
    <style>
        body {
            direction: rtl;
            font-family: sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }

        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 8px #ccc;
            width: 500px;
            margin: 30px auto;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background: #218838;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #0073aa;
            text-decoration: none;
        }

        .year-input {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2 style="text-align:center;">👨‍🎓 ثبت‌نام دانش‌آموز جدید</h2>

        <?php if (isset($success)): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <label>نام و نام خانوادگی:</label>
            <input type="text" name="full_name" required >

            <label>سال تحصیلی (عدد چهار رقمی):</label>
            <input type="number" name="academic_year" class="year-input" required
                min="1300" max="1500" placeholder="1404"
                value="1404">

            <button type="submit">ثبت‌نام دانش‌آموز</button>
        </form>

        <a href="index.php" class="back-link">بازگشت به صفحه اصلی</a>
    </div>

</body>

</html>