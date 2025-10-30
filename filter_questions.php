<?php
require 'db.php';

// دریافت لیست موضوعات برای فیلتر
$topics = $conn->query("SELECT id, name FROM topics ORDER BY name ASC");

// دریافت موضوع انتخاب شده (در صورت ارسال)
$selected_topic = isset($_GET['topic_id']) ? (int)$_GET['topic_id'] : 0;

// دریافت سوالات بر اساس موضوع انتخابی یا همه
if ($selected_topic > 0) {
    $stmt = $conn->prepare("
        SELECT q.*, t.name AS topic_name, l.level_name
        FROM questions q
        JOIN topics t ON q.topic_id = t.id
        JOIN levels l ON q.level_id = l.id
        WHERE q.topic_id = ?
        ORDER BY q.id DESC
    ");
    $stmt->bind_param("i", $selected_topic);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
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
    <title>فیلتر سؤالات بانک</title>
    <style>
        body {
            direction: rtl;
            font-family: sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        select,
        button {
            padding: 8px 12px;
            font-size: 16px;
            margin-left: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
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
            position: sticky;
            /* چسباندن header */
            top: 0;
            z-index: 2;
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

    <h2>📋 فیلتر سؤالات بانک</h2>

    <form method="get">
        <label>انتخاب موضوع:</label>
        <select name="topic_id" onchange="this.form.submit()">
            <option value="0">-- همه موضوعات --</option>
            <?php while ($t = $topics->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>" <?= $selected_topic == $t['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <noscript><button type="submit">اعمال فیلتر</button></noscript>
    </form>

    <a href="add_question.php" class="button edit">➕ افزودن سؤال جدید</a>
    <a href="manage_questions.php" class="button edit">مدیریت همه سؤالات</a>
    <a href="manage_topics.php" class="button edit">ویرایش موضوعات</a>
    <br><br>

    <table>
        <thead>
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
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td class="rtl-text"><?= htmlspecialchars($row['question']) ?></td>
                    <td class="ltr-text">
                        <?php if (!empty($row['code_snippet'])): ?>
                            <pre><?= htmlspecialchars(substr($row['code_snippet'], 0, 100)) ?></pre>
                        <?php else: ?><em>–</em><?php endif; ?>
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
        </tbody>
    </table>

</body>

</html>