<?php
require 'db.php';

// اضافه کردن موضوع جدید
if (isset($_POST['add_topic'])) {
    $name = trim($_POST['topic_name']);
    if ($name) {
        $stmt = $conn->prepare("INSERT INTO topics (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        header("Location: manage_topics.php");
        exit;
    }
}

// ویرایش موضوع
if (isset($_POST['edit_topic'])) {
    $id = (int)$_POST['topic_id'];
    $name = trim($_POST['topic_name']);
    if ($name && $id) {
        $stmt = $conn->prepare("UPDATE topics SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        header("Location: manage_topics.php");
        exit;
    }
}

// حذف موضوع
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: manage_topics.php");
        exit;
    }
}

// دریافت تمام موضوعات
$topics = $conn->query("SELECT * FROM topics ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>مدیریت موضوعات</title>
    <style>
        body {
            direction: rtl;
            font-family: sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type=text] {
            width: 80%;
            padding: 8px;
            margin-right: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background: #0073aa;
            color: white;
        }

        button:hover {
            background: #005f87;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #0073aa;
            color: white;
        }

        a.delete {
            color: red;
            text-decoration: none;
        }

        a.delete:hover {
            text-decoration: underline;
        }

        a.back {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
        }

        a.back:hover {
            background: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>📚 مدیریت موضوعات</h2>

        <!-- فرم اضافه کردن موضوع -->
        <form method="post">
            <input type="text" name="topic_name" placeholder="نام موضوع جدید" required>
            <button type="submit" name="add_topic">➕ اضافه کردن</button>
        </form>

        <!-- لیست موضوعات موجود -->
        <table>
            <tr>
                <th>#</th>
                <th>نام موضوع</th>
                <th>عملیات</th>
            </tr>
            <?php $i = 1;
            while ($t = $topics->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($t['name']) ?></td>
                    <td>
                        <!-- فرم ویرایش -->
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="topic_id" value="<?= $t['id'] ?>">
                            <input type="text" name="topic_name" value="<?= htmlspecialchars($t['name']) ?>" required>
                            <button type="submit" name="edit_topic">ویرایش</button>
                        </form>
                        <a class="delete" href="?delete=<?= $t['id'] ?>" onclick="return confirm('آیا مطمئن هستید حذف شود؟');">حذف</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <a href="add_question.php" class="back">بازگشت به ثبت سوال</a>
        <a href="manage_questions.php" class="back">بازگشت به مدیریت سوال</a>
        <a href="index.php" class="back">صفحه اصلی</a>
    </div>
</body>

</html>