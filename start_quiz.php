<?php
require 'db.php';
session_start();

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

// دریافت موضوعات
$topics = $conn->query("SELECT id, name FROM topics ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<title>شروع آزمون جدید</title>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
body {
    direction: rtl;
    font-family: "Vazir", sans-serif;
    background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
    margin: 0;
    padding: 0;
}
.container {
    max-width: 700px;
    margin: 40px auto;
    background: #fff;
    padding: 30px 35px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}
h2 {
    text-align: center;
    color: #0073aa;
    margin-bottom: 25px;
}
label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}
select, input[type="number"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 15px;
}
.checkbox-list {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 10px;
    background: #f9f9f9;
    margin-bottom: 20px;
    max-height: 200px;
    overflow-y: auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.checkbox-list label {
    display: flex;
    align-items: center;
    padding: 8px 10px;
    margin: 0;
    background: white;
    border-radius: 6px;
    border: 1px solid #eee;
    transition: background 0.3s ease;
    font-size: 14px;
    cursor: pointer;
}
.checkbox-list label:hover { 
    background: #eaf6ff; 
    border-color: #0073aa;
}
.checkbox-list input[type="checkbox"] {
    margin-left: 8px;
    transform: scale(1.1);
    cursor: pointer;
}

button {
    background: linear-gradient(135deg, #0073aa, #005f87);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    font-weight: bold;
    transition: all 0.3s ease;
}
button:hover { background: linear-gradient(135deg, #006194, #004f70); }

.links {
    text-align: center;
    margin-top: 25px;
}
.links a {
    display: inline-block;
    margin: 6px;
    padding: 10px 15px;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    transition: background 0.3s ease;
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

@media (max-width: 600px) {
    .container {
        padding: 20px;
        margin: 20px;
    }
    .checkbox-list {
        grid-template-columns: repeat(2, 1fr);
    }
    button {
        font-size: 15px;
    }
}

@media (max-width: 400px) {
    .checkbox-list {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<div class="container">
    <h2>🧠 شروع آزمون جدید</h2>

    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="post" onsubmit="return validateForm()">
        <label>📅 فیلتر سال تحصیلی:</label>
        <select id="year-filter">
            <option value="">همه سال‌ها</option>
            <?php
            $years = $conn->query("SELECT DISTINCT academic_year FROM students ORDER BY academic_year DESC");
            while ($y = $years->fetch_assoc()) {
                echo '<option value="' . $y['academic_year'] . '">' . $y['academic_year'] . '</option>';
            }
            ?>
        </select>

        <label>👤 جستجوی دانش‌آموز:</label>
        <select id="student-select" name="student_id" required></select>

        <label>📚 انتخاب موضوعات آزمون (حداقل یک موضوع):</label>
        <div class="checkbox-list">
            <?php while ($t = $topics->fetch_assoc()): ?>
                <label>
                    <input type="checkbox" name="topics[]" value="<?= $t['id'] ?>"> <?= htmlspecialchars($t['name']) ?>
                </label>
            <?php endwhile; ?>
        </div>

        <label>⏱ مدت آزمون (دقیقه):</label>
        <input type="number" name="duration" min="1" max="99" value="10" required>

        <button type="submit">🚀 شروع آزمون</button>
    </form>

    <div class="links">
        <a href="register_student.php">➕ ثبت‌نام دانش‌آموز</a>
        <a href="manage_students.php">👥 مدیریت دانش‌آموزان</a>
        <a href="index.php">🏠 صفحه اصلی</a>
    </div>
</div>

<script>
// فعال‌سازی Select2 با جستجو و فیلتر سال
$('#student-select').select2({
    placeholder: "جستجوی دانش‌آموز...",
    ajax: {
        url: 'search_students.php',
        dataType: 'json',
        delay: 250,
        data: params => ({
            term: params.term || '',
            year: $('#year-filter').val()
        }),
        processResults: data => ({ results: data }),
        cache: true
    },
    minimumInputLength: 1
});

// تغییر فیلتر سال → ریست انتخاب
$('#year-filter').on('change', function() {
    $('#student-select').val(null).trigger('change');
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