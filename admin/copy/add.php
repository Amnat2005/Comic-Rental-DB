<?php
require_once "../../connect/connect.php";

// ดึง Title และ Shelf
$titles = $conn->query("SELECT * FROM Title");
$shelves = $conn->query("SELECT * FROM Shelf");

// ตรวจสอบว่ามีการส่ง form
if(isset($_POST['submit'])){
    $title_id = (int)$_POST['title_id'];
    $shelf_id = (int)$_POST['shelf_id'];
    $book_condition = trim($_POST['book_condition']);
    $copy_status = $_POST['copy_status'];
    $acquired_date = $_POST['acquired_date'];
    $purchase_price = (float)$_POST['purchase_price'];

    $stmt = $conn->prepare("
        INSERT INTO copy_backup (title_id, shelf_id, book_condition, copy_status, acquired_date, purchase_price) 
        VALUES (?,?,?,?,?,?)
    ");

    if(!$stmt){
        die("Prepare failed: ".$conn->error);
    }

    $stmt->bind_param("iisssd", $title_id, $shelf_id, $book_condition, $copy_status, $acquired_date, $purchase_price);
    $stmt->execute();
    $stmt->close();

    header("Location: list.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เพิ่มสำเนาหนังสือ</title>
<style>
body { font-family:"Segoe UI", Arial, sans-serif; background:#f5f7fa; margin:0; padding:30px; }
.container { max-width:900px; margin:auto; background:#fff; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); padding:30px; }
.toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
.title { font-size:24px; font-weight:600; color:#2c3e50; }
.btn { display:inline-block; padding:8px 16px; border-radius:6px; font-size:14px; text-decoration:none; color:white; background:#3498db; transition:0.2s; }
.btn:hover { background:#2980b9; }
form label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
form input, form select { width:100%; padding:10px 12px; margin-bottom:20px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
form input[type="submit"] { background:#3498db; color:#fff; border:none; cursor:pointer; }
form input[type="submit"]:hover { background:#2980b9; }
</style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <div class="title">เพิ่มสำเนาหนังสือ (Copy)</div>
        <a href="list.php" class="btn">กลับรายการ Copy</a>
    </div>

    <form method="post">
        <label for="title_id">ชื่อหนังสือ</label>
        <select name="title_id" id="title_id" required>
            <option value="" disabled selected>-- เลือกหนังสือ --</option>
            <?php while($t = $titles->fetch_assoc()): ?>
                <option value="<?= $t['title_id'] ?>"><?= htmlspecialchars($t['series_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="shelf_id">ชั้นวาง</label>
        <select name="shelf_id" id="shelf_id" required>
            <option value="" disabled selected>-- เลือกชั้นวาง --</option>
            <?php while($s = $shelves->fetch_assoc()): ?>
                <option value="<?= $s['shelf_id'] ?>"><?= htmlspecialchars($s['shelf_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="book_condition">สภาพหนังสือ</label>
        <input type="text" name="book_condition" id="book_condition" required>

        <label for="copy_status">สถานะ</label>
        <select name="copy_status" id="copy_status">
            <option value="available">Available</option>
            <option value="borrowed">Borrowed</option>
            <option value="damaged">Damaged</option>
            <option value="lost">Lost</option>
        </select>

        <label for="acquired_date">วันที่ได้รับ</label>
        <input type="date" name="acquired_date" id="acquired_date" required>

        <label for="purchase_price">ราคา</label>
        <input type="number" step="0.01" name="purchase_price" id="purchase_price" required>

        <input type="submit" name="submit" value="เพิ่ม Copy">
    </form>
</div>

</body>
</html>
