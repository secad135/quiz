<?php
require 'db.php';

// موضوع انتخاب شده
$selected_topic_id = isset($_GET['topic_id']) ? (int)$_GET['topic_id'] : 0;

// دریافت تمام موضوعات برای لیست کشویی
$topics = $conn->query("SELECT * FROM topics ORDER BY name ASC");

// دریافت سوالات بر اساس موضوع انتخاب شده
if ($selected_topic_id) {
    $stmt = $conn->prepare("
        SELECT q.*, t.name AS topic_name, l.level_name
        FROM questions q
        JOIN topics t ON q.topic_id = t.id
        JOIN levels l ON q.level_id = l.id
        WHERE q.topic_id = ?
        ORDER BY q.id DESC
    ");
    $stmt->bind_param("i", $selected_topic_id);
    $stmt->execute();
    $questions = $stmt->get_result();
} else {
    $questions = $conn->query("
        SELECT q.*, t.name AS topic_name, l.level_name
        FROM questions q
        JOIN topics t ON q.topic_id = t.id
        JOIN levels l ON q.level_id = l.id
        ORDER BY q.id DESC
    ");
}
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>فیلتر سوالات بر اساس موضوع</title>
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

        select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-left: 10px;
        }

        form {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>📄 فیلتر سوالات بر اساس موضوع</h2>

    <form method="get">
        <label>انتخاب موضوع:</label>
        <select name="topic_id" onchange="this.form.submit()">
            <option value="0">-- همه موضوعات --</option>
            <?php while ($t = $topics->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>" <?= $selected_topic_id == $t['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <noscript><button type="submit">فیلتر</button></noscript>
    </form>

    <a href="add_question.php" class="button edit">➕ افزودن سؤال جدید</a>
    <a href="manage_questions.php" class="button edit">مدیریت همه سوالات</a>
    <a href="manage_topics.php" class="button edit">ویرایش موضوعات</a>

    <br><br>

    <?php if ($questions->num_rows > 0): ?>
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
            <?php $i = 1;
            while ($row = $questions->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td class="rtl-text"><?= htmlspecialchars($row['question']) ?></td>
                    <td class="ltr-text">
                        <?php if (!empty($row['code_snippet'])): ?>
                            <pre><?= htmlspecialchars(substr($row['code_snippet'], 0, 100)) ?></pre>
                        <?php else: ?>
                            <em> </em>
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
                        <a href="filter_questions.php?topic_id=<?= $selected_topic_id ?>&delete=<?= $row['id'] ?>" class="button delete" onclick="return confirm('آیا مطمئن هستید؟');">حذف</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center; padding:20px; color:#666;">❌ هیچ سوالی برای این موضوع ثبت نشده است.</p>
    <?php endif; ?>
</body>

</html>