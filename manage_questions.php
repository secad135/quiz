<?php
require 'db.php';

// حذف سوال اگر id ارسال شده باشد
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM questions WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_questions.php");
    exit;
}

// دریافت تمام سوالات به همراه نام موضوع و سطح سختی
$sql = "
SELECT q.*, t.name AS topic_name, l.level_name
FROM questions q
JOIN topics t ON q.topic_id = t.id
JOIN levels l ON q.level_id = l.id
ORDER BY q.id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>مدیریت سوالات</title>
    <style>
        body {
            direction: rtl;
            font-family: sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: right;
            vertical-align: top;
        }

        th {
            background: #0073aa;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        a.button {
            padding: 5px 10px;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            margin: 2px;
        }

        a.edit {
            background: #28a745;
        }

        a.delete {
            background: #dc3545;
        }

        .rtl-text {
            direction: rtl;
            text-align: right;
        }

        .ltr-text {
            direction: ltr;
            text-align: left;
        }

        pre {
            direction: ltr;
            background: #f5f5f5;
            padding: 5px;
            border-radius: 6px;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <h2>مدیریت سؤالات بانک</h2>
    <a href="add_question.php" class="button edit">➕ افزودن سؤال جدید</a>
    <a href="index.php" class="button edit">بازگشت به صفحه اصلی</a>
    <a href="manage_topics.php" class="button edit">ویرایش موضوعات</a>
    <br><br>
    <table>
        <tr>
            <th>#</th>
            <th>سؤال</th>
            <th>کد مربوطه</th>
            <th>موضوع</th>
            <th>سطح</th>
            <th>گزینه A</th>
            <th>گزینه B</th>
            <th>گزینه C</th>
            <th>گزینه D</th>
            <th>پاسخ صحیح</th>
            <th>عملیات</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td class="rtl-text"><?= htmlspecialchars($row['question']) ?></td>
                <td class="ltr-text">
                    <?php if (!empty($row['code_snippet'])): ?>
                        <pre><?= htmlspecialchars(substr($row['code_snippet'], 0, 100)) ?></pre>
                    <?php else: ?>
                        <em > </em>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['topic_name']) ?></td>
                <td><?= htmlspecialchars($row['level_name']) ?></td>
                <td class="ltr-text"><?= htmlspecialchars($row['option_a']) ?></td>
                <td class="ltr-text"><?= htmlspecialchars($row['option_b']) ?></td>
                <td class="ltr-text"><?= htmlspecialchars($row['option_c']) ?></td>
                <td class="ltr-text"><?= htmlspecialchars($row['option_d']) ?></td>
                <td><?= $row['correct_option'] ?></td>
                <td>
                    <a href="edit_question.php?id=<?= $row['id'] ?>" class="button edit">ویرایش</a>
                    <a href="manage_questions.php?delete=<?= $row['id'] ?>" class="button delete" onclick="return confirm('آیا مطمئن هستید؟');">حذف</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>