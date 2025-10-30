<?php
require 'db.php';

session_start();
require 'db.php';

// اگر فرم ارسال شده
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student_id']) && !empty($_POST['topics'])) {
        $_SESSION['student_id'] = $_POST['student_id'];
        $_SESSION['topics'] = $_POST['topics'];
        $_SESSION['duration'] = intval($_POST['duration']);
        header("Location: quiz.php");
        exit;
    } else {
        $error = "لطفاً دانش‌آموز و حداقل یک موضوع را انتخاب کنید.";
    }
}

// دریافت موضوعات برای فرم
$topics = $conn->query("SELECT id, name FROM topics ORDER BY name ASC");

// دریافت موضوعات
$topics = $conn->query("SELECT id, name FROM topics ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<title>شروع آزمون جدید</title>

<!-- لینک‌های Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
body {
    direction: rtl;
    font-family: sans-serif;
    background: #f2f2f2;
    padding: 30px;
}
.container {
    max-width: 700px;
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
.select2-container--default .select2-selection--single {
    height: 40px;
    padding: 5px 10px;
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
.checkbox-list label:hover { background: #f0f8ff; }
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
button:hover { background: #005f87; }
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
.links a:hover { background: #218838; }
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

    <form method="post" onsubmit="return validateForm()">
        <label>فیلتر سال تحصیلی:</label>
        <select id="year-filter" style="width:100%">
            <option value="">همه سال‌ها</option>
            <?php
            $years = $conn->query("SELECT DISTINCT academic_year FROM students ORDER BY academic_year DESC");
            while ($y = $years->fetch_assoc()) {
                echo '<option value="' . $y['academic_year'] . '">' . $y['academic_year'] . '</option>';
            }
            ?>
        </select>

        <label>جستجوی دانش‌آموز:</label>
        <select id="student-select" name="student_id" style="width:100%" required></select>

        <label>انتخاب موضوعات آزمون (حداقل یک موضوع):</label>
        <div class="checkbox-list">
            <?php
            $topics->data_seek(0);
            while ($t = $topics->fetch_assoc()):
            ?>
            <label>
                <input type="checkbox" name="topics[]" value="<?= $t['id'] ?>">
                <?= htmlspecialchars($t['name']) ?>
            </label>
            <?php endwhile; ?>
        </div>

        <!-- 🕒 محدوده زمانی آزمون -->
        <label>⏱ مدت آزمون (دقیقه):</label>
        <input type="number" name="duration" min="1" max="99" value="10" required
               style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; margin-bottom:15px;">

        <button type="submit">شروع آزمون 🚀</button>
    </form>

    <div class="links">
        <a href="register_student.php">➕ ثبت‌نام دانش‌آموز جدید</a>
        <a href="manage_students.php">👥 مدیریت دانش‌آموزان</a>
        <a href="index.php">🏠 صفحه اصلی</a>
    </div>
</div>

<script>
// فعال کردن Select2 با AJAX
$('#student-select').select2({
    placeholder: "جستجوی دانش‌آموز...",
    ajax: {
        url: 'search_students.php',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                term: params.term || '',
                year: $('#year-filter').val()
            };
        },
        processResults: function(data) {
            return { results: data };
        },
        cache: true
    },
    minimumInputLength: 1
});

// تغییر سال فیلتر
$('#year-filter').on('change', function() {
    $('#student-select').val(null).trigger('change'); // پاک کردن انتخاب فعلی
});

// بررسی حداقل انتخاب موضوع
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