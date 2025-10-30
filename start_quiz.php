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
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

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
select, input[type="number"], input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 15px;
    box-sizing: border-box;
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

/* استایل‌های جدید برای جستجو */
.search-container {
    position: relative;
    margin-bottom: 20px;
}

.search-input {
    padding-right: 45px !important;
    padding-left: 45px !important;
    background: #fff;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #0073aa;
    box-shadow: 0 0 0 3px rgba(0, 115, 170, 0.2);
    outline: none;
}

.search-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #0073aa;
    font-size: 18px;
    z-index: 2;
}

.clear-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    cursor: pointer;
    font-size: 16px;
    z-index: 2;
    display: none;
}

.clear-icon:hover {
    color: #dc3545;
}

.results-container {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #0073aa;
    border-radius: 8px;
    border-top: none;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.result-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.result-item:hover {
    background: #eaf6ff;
    transform: translateX(-5px);
}

.result-item:last-child {
    border-bottom: none;
}

.student-name {
    font-weight: bold;
    color: #333;
}

.student-year {
    font-size: 12px;
    color: #666;
    background: #f8f9fa;
    padding: 2px 8px;
    border-radius: 12px;
}

.no-results {
    padding: 15px;
    text-align: center;
    color: #666;
    font-style: italic;
}

.year-filter-container {
    position: relative;
    margin-bottom: 20px;
}

.year-filter-container::before {
    content: "📅";
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    font-size: 16px;
}

.year-filter-container select {
    padding-left: 40px;
}

.selected-student {
    background: #e8f5e8;
    border: 1px solid #28a745;
    border-radius: 8px;
    padding: 10px 15px;
    margin-bottom: 20px;
    display: none;
}

.selected-student .student-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.selected-student .remove-btn {
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.selected-student .remove-btn:hover {
    background: #c82333;
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
        <div class="year-filter-container">
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
        </div>

        <div class="search-container">
            <label>👤 جستجوی دانش‌آموز:</label>
            <input type="text" id="student-search" class="search-input" placeholder="نام دانش‌آموز را تایپ کنید..." autocomplete="off">
            <div class="search-icon">🔍</div>
            <div class="clear-icon" id="clear-search">✕</div>
            <div class="results-container" id="search-results"></div>
        </div>

        <input type="hidden" id="student_id" name="student_id" required>

        <div class="selected-student" id="selected-student">
            <div class="student-info">
                <span id="selected-name"></span>
                <button type="button" class="remove-btn" onclick="clearStudent()">✕</button>
            </div>
        </div>

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
let searchTimeout;

// مدیریت جستجو
$('#student-search').on('input', function() {
    const searchTerm = $(this).val().trim();
    const yearFilter = $('#year-filter').val();
    
    // پاک کردن تایموت قبلی
    clearTimeout(searchTimeout);
    
    // اگر عبارت جستجو خالی است
    if (searchTerm === '') {
        $('#search-results').hide();
        $('#clear-search').hide();
        return;
    }
    
    $('#clear-search').show();
    
    // تاخیر برای جلوگیری از درخواست‌های مکرر
    searchTimeout = setTimeout(() => {
        searchStudents(searchTerm, yearFilter);
    }, 300);
});

// مدیریت فوکوس
$('#student-search').on('focus', function() {
    const searchTerm = $(this).val().trim();
    if (searchTerm !== '') {
        $('#search-results').show();
    }
});

// پاک کردن جستجو
$('#clear-search').on('click', function() {
    $('#student-search').val('').focus();
    $('#search-results').hide();
    $(this).hide();
});

// تغییر فیلتر سال
$('#year-filter').on('change', function() {
    const searchTerm = $('#student-search').val().trim();
    if (searchTerm !== '') {
        searchStudents(searchTerm, $(this).val());
    }
});

// تابع جستجوی دانش‌آموزان
function searchStudents(term, year) {
    $.ajax({
        url: 'search_students.php',
        method: 'GET',
        data: {
            term: term,
            year: year
        },
        dataType: 'json',
        success: function(data) {
            displayResults(data);
        },
        error: function() {
            $('#search-results').html('<div class="no-results">خطا در بارگذاری نتایج</div>').show();
        }
    });
}

// نمایش نتایج
function displayResults(students) {
    const resultsContainer = $('#search-results');
    
    if (students.length === 0) {
        resultsContainer.html('<div class="no-results">دانش‌آموزی یافت نشد</div>').show();
        return;
    }
    
    let html = '';
    students.forEach(student => {
        html += `
            <div class="result-item" data-id="${student.id}" data-name="${student.text}">
                <span class="student-name">${student.text}</span>
                ${student.academic_year ? `<span class="student-year">${student.academic_year}</span>` : ''}
            </div>
        `;
    });
    
    resultsContainer.html(html).show();
    
    // مدیریت کلیک روی نتیجه
    $('.result-item').on('click', function() {
        const studentId = $(this).data('id');
        const studentName = $(this).data('name');
        const studentYear = $(this).find('.student-year').text();
        
        selectStudent(studentId, studentName, studentYear);
    });
}

// انتخاب دانش‌آموز
function selectStudent(id, name, year) {
    $('#student_id').val(id);
    $('#selected-name').text(name + (year ? ` (${year})` : ''));
    $('#selected-student').show();
    $('#student-search').val('');
    $('#search-results').hide();
    $('#clear-search').hide();
}

// پاک کردن دانش‌آموز انتخاب شده
function clearStudent() {
    $('#student_id').val('');
    $('#selected-student').hide();
    $('#student-search').focus();
}

// کلیک خارج از جستجو برای بستن نتایج
$(document).on('click', function(e) {
    if (!$(e.target).closest('.search-container').length) {
        $('#search-results').hide();
    }
});

// بررسی حداقل انتخاب موضوع
function validateForm() {
    const studentId = $('#student_id').val();
    const checkboxes = document.querySelectorAll('input[name="topics[]"]:checked');
    
    if (!studentId) {
        alert('لطفاً یک دانش‌آموز انتخاب کنید.');
        $('#student-search').focus();
        return false;
    }
    
    if (checkboxes.length === 0) {
        alert('لطفاً حداقل یک موضوع را انتخاب کنید.');
        return false;
    }
    
    return true;
}
</script>
</body>
</html>