<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<title>تاریخچه نتایج آزمون‌ها</title>
<style>
body {
    direction: rtl;
    font-family: sans-serif;
    background-color: #f4f6f8;
    padding: 30px;
}
.container {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 20px 25px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    color: #333;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}
th {
    background: #0073aa;
    color: white;
}
tr:nth-child(even) {
    background: #f9f9f9;
}
.badge {
    padding: 5px 10px;
    border-radius: 8px;
    color: white;
    font-weight: bold;
}
.good { background: #28a745; }
.bad { background: #dc3545; }
.medium { background: #ffc107; color: #222; }
a.back {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    background: #0073aa;
    color: white;
    padding: 10px 18px;
    border-radius: 8px;
}
a.back:hover {
    background: #005f87;
}
</style>
</head>

<body>
<div class="container">
    <h2>📊 تاریخچه نتایج آزمون‌ها</h2>

    <table>
        <tr>
            <th>#</th>
            <th>نام دانش‌آموز</th>
            <th>موضوعات</th>
            <th>تعداد سؤالات</th>
            <th>پاسخ صحیح</th>
            <th>پاسخ غلط</th>
            <th>نمره (%)</th>
            <th>تاریخ آزمون</th>
        </tr>

        <?php
        $sql = "SELECT * FROM quiz_results ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0):
            $count = 1;
            while ($row = $result->fetch_assoc()):
                $badgeClass = $row['score'] >= 80 ? 'good' : ($row['score'] >= 50 ? 'medium' : 'bad');
        ?>
            <tr>
                <td><?= $count++ ?></td>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['topics']) ?></td>
                <td><?= $row['total_questions'] ?></td>
                <td><?= $row['correct_answers'] ?></td>
                <td><?= $row['wrong_answers'] ?></td>
                <td><span class="badge <?= $badgeClass ?>"><?= $row['score'] ?>%</span></td>
                <td><?= date("Y-m-d H:i", strtotime($row['created_at'])) ?></td>
            </tr>
        <?php
            endwhile;
        else:
            echo "<tr><td colspan='8'>هنوز هیچ نتیجه‌ای ثبت نشده است.</td></tr>";
        endif;
        ?>
    </table>

    <div style="text-align:center;">
        <a href="index.php" class="back">بازگشت به صفحه اصلی</a>
    </div>
</div>
</body>
</html>
