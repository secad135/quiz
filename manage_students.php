<?php
require 'db.php';

// حذف دانش‌آموز
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    if ($stmt->bind_param("i", $id) && $stmt->execute()) {
        $success = "✅ دانش‌آموز با موفقیت حذف شد.";
    } else {
        $error = "❌ خطا در حذف دانش‌آموز: " . $stmt->error;
    }
}

// دریافت تمام دانش‌آموزان
$students = $conn->query("SELECT * FROM students ORDER BY id");
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>مدیریت دانش‌آموزان</title>
    <style>
        body {
            direction: rtl;
            font-family: sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background: #0073aa;
            color: #fff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #f0f8ff;
        }
        .button {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            margin: 2px;
            display: inline-block;
            font-size: 14px;
            transition: 0.3s;
        }
        .button-results { 
            background: #17a2b8; 
        }
        .button-results:hover { 
            background: #138496; 
        }
        .button-delete { 
            background: #dc3545; 
        }
        .button-delete:hover { 
            background: #c82333; 
        }
        .button-add { 
            background: #28a745; 
            padding: 10px 20px;
            font-size: 16px;
        }
        .button-add:hover { 
            background: #218838; 
        }
        .button-home { 
            background: #6c757d; 
        }
        .button-home:hover { 
            background: #5a6268; 
        }
        .button-quiz { 
            background: #0073aa; 
        }
        .button-quiz:hover { 
            background: #005f87; 
        }
        .header-links { 
            margin-bottom: 25px; 
            text-align: center;
        }
        .header-links .button {
            margin: 5px;
        }
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .student-count {
            background: #e7f3ff;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            border-right: 4px solid #0073aa;
            font-weight: bold;
        }
        .no-students {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
            background: #f9f9f9;
            border-radius: 8px;
            margin-top: 20px;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        .academic-year {
            font-weight: bold;
            color: #0073aa;
            background: #e7f3ff;
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>👥 مدیریت دانش‌آموزان</h2>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="header-links">
        <a href="register_student.php" class="button button-add">➕ ثبت‌نام دانش‌آموز جدید</a>
        <a href="start_quiz.php" class="button button-quiz">📝 شروع آزمون جدید</a>
        <a href="results_history.php" class="button button-results">📊 تاریخچه نتایج</a>
        <a href="index.php" class="button button-home">🏠 صفحه اصلی</a>
    </div>

    <?php if ($students->num_rows > 0): ?>
        <div class="student-count">
            📊 تعداد کل دانش‌آموزان: <?= $students->num_rows ?> نفر
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>نام و نام خانوادگی</th>
                    <th>سال تحصیلی</th>
                    <th>تاریخ ثبت‌نام</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                while ($row = $students->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td style="font-weight: bold;"><?= htmlspecialchars($row['full_name']) ?></td>
                        <td>
                            <span class="academic-year"><?= htmlspecialchars($row['academic_year']) ?></span>
                        </td>
                        <td><?= date("Y/m/d - H:i", strtotime($row['created_at'])) ?></td>
                        <td class="actions">
                            <a href="student_results.php?student_id=<?= $row['id'] ?>" class="button button-results" title="مشاهده نتایج">
                                📊 نتایج
                            </a>
                            <a href="manage_students.php?delete=<?= $row['id'] ?>" 
                               class="button button-delete" 
                               title="حذف دانش‌آموز"
                               onclick="return confirm('آیا از حذف «<?= htmlspecialchars($row['full_name']) ?>» مطمئن هستید؟ این عمل غیرقابل بازگشت است!')">
                                🗑️ حذف
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-students">
            👨‍🎓 هیچ دانش‌آموزی ثبت‌نام نشده است.
            <br><br>
            <a href="register_student.php" class="button button-add">ثبت‌نام اولین دانش‌آموز</a>
        </div>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
        <p style="color: #666;">سیستم مدیریت آزمون PHP - تعداد دانش‌آموزان: <strong><?= $students->num_rows ?></strong> نفر</p>
    </div>
</div>

<script>
// تأیید مجدد برای حذف
document.addEventListener('DOMContentLoaded', function() {
    const deleteLinks = document.querySelectorAll('.button-delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('آیا مطمئن هستید که می‌خواهید این دانش‌آموز را حذف کنید؟\nاین عمل تمام نتایج مرتبط با این دانش‌آموز را نیز حذف خواهد کرد!')) {
                e.preventDefault();
            }
        });
    });
});
</script>

</body>
</html>