<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("❌ Error: ไม่พบรหัสประเภทหนังสือที่ต้องการแก้ไข");
}

$id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM Genre WHERE genre_id=$id");
$row = $result->fetch_assoc();

if (!$row) {
    die("❌ Error: ไม่พบข้อมูลประเภทหนังสือ");
}

$message = "";
if (isset($_POST['submit'])) {
    $genre_name = $_POST['genre_name'];

    $stmt = $conn->prepare("UPDATE Genre SET genre_name=? WHERE genre_id=?");
    $stmt->bind_param("si", $genre_name, $id);

    if ($stmt->execute()) {
        $message = "✅ แก้ไขข้อมูลประเภทหนังสือสำเร็จแล้ว";
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
    <title>แก้ไขประเภทหนังสือ (Genre)</title>
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
        <div class="title">แก้ไขประเภทหนังสือ (Genre)</div>
        <a href="list.php" class="btn">กลับรายการประเภทหนังสือ</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="genre_name">ชื่อประเภทหนังสือ</label>
        <input type="text" id="genre_name" name="genre_name" value="<?= htmlspecialchars($row['genre_name']) ?>" required>

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
