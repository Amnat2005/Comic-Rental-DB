<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Author ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูลผู้แต่ง
$author = $conn->query("SELECT * FROM Author WHERE author_id=$id")->fetch_assoc();

if (!$author) {
    die("Error: Author not found.");
}

$message = "";
if (isset($_POST['submit'])) {
    $stmt = $conn->prepare("UPDATE Author SET author_name=? WHERE author_id=?");
    $stmt->bind_param("si", $_POST['author_name'], $id);

    if ($stmt->execute()) {
        $message = "✅ แก้ไขข้อมูลผู้แต่งสำเร็จแล้ว";
    } else {
        $message = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขผู้แต่ง (Author)</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            transition: background-color 0.2s;
        }
        .btn:hover { background-color: #2980b9; }

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #2c3e50;
        }
        form input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        form input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        form input[type="submit"]:hover {
            background-color: #2980b9;
        }
        .message {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 6px;
            background-color: #eef4fb;
            color: #2c3e50;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <div class="title">แก้ไขผู้แต่ง (Author)</div>
        <a href="list.php" class="btn">กลับรายการผู้แต่ง</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="author_name">ชื่อผู้แต่ง</label>
        <input type="text" id="author_name" name="author_name" value="<?= htmlspecialchars($author['author_name']) ?>" required>

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
