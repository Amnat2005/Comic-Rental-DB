<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Copy ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูล Copy, Title และ Shelf
$copy = $conn->query("SELECT * FROM Copy WHERE copy_id=$id")->fetch_assoc();
$titles = $conn->query("SELECT * FROM Title");
$shelves = $conn->query("SELECT * FROM Shelf");

if (!$copy) {
    die("Error: Copy not found.");
}

$message = "";
if (isset($_POST['submit'])) {
    $stmt = $conn->prepare("UPDATE Copy SET title_id=?, shelf_id=?, book_condition=?, copy_status=?, acquired_date=?, purchase_price=? WHERE copy_id=?");
    $stmt->bind_param(
        "iisssdi",
        $_POST['title_id'],
        $_POST['shelf_id'],
        $_POST['book_condition'],
        $_POST['copy_status'],
        $_POST['acquired_date'],
        $_POST['purchase_price'],
        $id
    );

    if ($stmt->execute()) {
        $message = "✅ แก้ไขข้อมูล Copy สำเร็จแล้ว";
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
    <title>แก้ไขสำเนาหนังสือ (Copy)</title>
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
        form select, form input[type="text"], form input[type="date"], form input[type="number"] {
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
        <div class="title">แก้ไขสำเนาหนังสือ (Copy)</div>
        <a href="list.php" class="btn">กลับรายการ Copy</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="title_id">ชื่อเรื่อง</label>
        <select name="title_id" id="title_id" required>
            <?php while($t=$titles->fetch_assoc()): ?>
                <option value="<?= $t['title_id'] ?>" <?= $t['title_id']==$copy['title_id']?'selected':'' ?>>
                    <?= htmlspecialchars($t['series_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="shelf_id">ชั้นวาง</label>
        <select name="shelf_id" id="shelf_id" required>
            <?php while($s=$shelves->fetch_assoc()): ?>
                <option value="<?= $s['shelf_id'] ?>" <?= $s['shelf_id']==$copy['shelf_id']?'selected':'' ?>>
                    <?= htmlspecialchars($s['shelf_number']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="book_condition">สภาพหนังสือ</label>
        <input type="text" name="book_condition" id="book_condition" value="<?= htmlspecialchars($copy['book_condition']) ?>" required>

        <label for="copy_status">สถานะ</label>
        <select name="copy_status" id="copy_status">
            <option value="available" <?= $copy['copy_status']=='available'?'selected':'' ?>>Available</option>
            <option value="borrowed" <?= $copy['copy_status']=='borrowed'?'selected':'' ?>>Borrowed</option>
            <option value="damaged" <?= $copy['copy_status']=='damaged'?'selected':'' ?>>Damaged</option>
            <option value="lost" <?= $copy['copy_status']=='lost'?'selected':'' ?>>Lost</option>
        </select>

        <label for="acquired_date">วันที่ได้รับ</label>
        <input type="date" name="acquired_date" id="acquired_date" value="<?= $copy['acquired_date'] ?>" required>

        <label for="purchase_price">ราคา</label>
        <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="<?= $copy['purchase_price'] ?>" required>

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
