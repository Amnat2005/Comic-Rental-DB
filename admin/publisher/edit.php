<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Publisher ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูลสำนักพิมพ์
$publisher = $conn->query("SELECT * FROM Publisher WHERE publisher_id=$id")->fetch_assoc();
if (!$publisher) {
    die("Error: Publisher not found.");
}

$message = "";
if (isset($_POST['submit'])) {
    $publisher_name = $_POST['publisher_name'];

    $stmt = $conn->prepare("UPDATE Publisher SET publisher_name=? WHERE publisher_id=?");
    $stmt->bind_param("si", $publisher_name, $id);

    if ($stmt->execute()) {
        $message = "✅ แก้ไขข้อมูลสำนักพิมพ์สำเร็จแล้ว";
    } else {
        $message = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลสำนักพิมพ์ (Publisher)</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
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
        <div class="title">แก้ไขข้อมูลสำนักพิมพ์ (Publisher)</div>
        <a href="list.php" class="btn">กลับหน้ารายการสำนักพิมพ์</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="publisher_name">ชื่อสำนักพิมพ์</label>
        <input type="text" id="publisher_name" name="publisher_name" value="<?= htmlspecialchars($publisher['publisher_name']) ?>" required>

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
