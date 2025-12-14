<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Shelf ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูล Shelf
$shelf = $conn->query("SELECT * FROM Shelf WHERE shelf_id=$id")->fetch_assoc();
if (!$shelf) {
    die("Error: Shelf not found.");
}

// ดึง Zone สำหรับ select
$zones = $conn->query("SELECT * FROM Zone");

$message = "";
if (isset($_POST['submit'])) {
    $zone_id = $_POST['zone_id'];
    $shelf_number = $_POST['shelf_number'];
    $capacity = $_POST['capacity'];

    $stmt = $conn->prepare("UPDATE Shelf SET zone_id=?, shelf_number=?, capacity=? WHERE shelf_id=?");
    $stmt->bind_param("isii", $zone_id, $shelf_number, $capacity, $id);

    if ($stmt->execute()) {
        $message = "✅ แก้ไขข้อมูล Shelf สำเร็จแล้ว";
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
    <title>แก้ไข Shelf</title>
    <style>
        body { font-family:"Segoe UI", Arial, sans-serif; background-color:#f5f7fa; margin:0; padding:30px; }
        .container { max-width:600px; margin:auto; background:#fff; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); padding:30px; }
        .toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
        .title { font-size:24px; font-weight:600; color:#2c3e50; }
        .btn { display:inline-block; padding:8px 16px; border-radius:6px; font-size:14px; text-decoration:none; color:white; background-color:#3498db; transition:0.2s; }
        .btn:hover { background-color:#2980b9; }
        form label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
        form select, form input[type="text"], form input[type="number"] { width:100%; padding:10px 12px; margin-bottom:20px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
        form input[type="submit"] { background-color:#3498db; color:white; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-size:14px; }
        form input[type="submit"]:hover { background-color:#2980b9; }
        .message { margin-bottom:20px; padding:12px 16px; border-radius:6px; background-color:#eef4fb; color:#2c3e50; font-weight:500; }
    </style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <div class="title">แก้ไข Shelf</div>
        <a href="list.php" class="btn">กลับหน้ารายการ Shelf</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="zone_id">โซน</label>
        <select id="zone_id" name="zone_id" required>
            <?php while($z = $zones->fetch_assoc()): ?>
                <option value="<?= $z['zone_id'] ?>" <?= $z['zone_id']==$shelf['zone_id']?'selected':'' ?>>
                    <?= htmlspecialchars($z['zone_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="shelf_number">หมายเลขชั้น</label>
        <input type="text" id="shelf_number" name="shelf_number" value="<?= htmlspecialchars($shelf['shelf_number']) ?>" required>

        <label for="capacity">ความจุชั้น (เล่ม)</label>
        <input type="number" id="capacity" name="capacity" value="<?= $shelf['capacity'] ?>" required>

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
