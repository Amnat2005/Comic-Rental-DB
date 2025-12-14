<?php
require_once "../../connect/connect.php";

// ดึงข้อมูลลูกค้า, Copy_backup (พร้อมชื่อหนังสือ)
$customers = $conn->query("SELECT customer_id, first_name, last_name FROM Customer");
$copies = $conn->query("
    SELECT Copy_backup.copy_id, Title.series_name, Copy_backup.title_id
    FROM Copy_backup
    JOIN Title ON Copy_backup.title_id = Title.title_id
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = (int)$_POST['customer_id'];
    $copy_id = (int)$_POST['copy_id'];
    $reserve_date = $_POST['reserve_date'];
    $expire_date = $_POST['expire_date'];
    $reservation_status = $_POST['reservation_status'];

    // ดึง title_id จาก copy_id ใน Copy_backup
    $res_title = $conn->query("SELECT title_id FROM Copy_backup WHERE copy_id = $copy_id");
    $row_title = $res_title->fetch_assoc();
    $title_id = $row_title['title_id'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO Reservation (customer_id, title_id, copy_id, reserve_date, expire_date, reservation_status) VALUES (?,?,?,?,?,?)");
    if (!$stmt) die("Prepare failed: " . $conn->error);

    $stmt->bind_param("iiisss", $customer_id, $title_id, $copy_id, $reserve_date, $expire_date, $reservation_status);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: list.php");
        exit;
    } else {
        die("Error inserting reservation: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มการจอง (Reservation)</title>
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 30px; }
        .container { max-width: 600px; margin: auto; background: #fff; border-radius: 10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); padding:30px; }
        .toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
        .title { font-size:24px; font-weight:600; color:#2c3e50; }
        .btn { display:inline-block; padding:8px 16px; border-radius:6px; font-size:14px; text-decoration:none; color:white; background-color:#3498db; transition:0.2s; }
        .btn:hover { background-color:#2980b9; }
        form label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
        form select, form input[type="date"] { width:100%; padding:10px 12px; margin-bottom:20px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
        form input[type="submit"] { background-color:#3498db; color:white; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-size:14px; }
        form input[type="submit"]:hover { background-color:#2980b9; }
    </style>
</head>

<body>

    <div class="container">
        <div class="toolbar">
            <div class="title">เพิ่มการจอง (Reservation)</div>
            <a href="list.php" class="btn">กลับรายการการจอง</a>
        </div>

        <form method="post">
            <label for="customer_id">ลูกค้า</label>
            <select name="customer_id" id="customer_id" required>
                <option value="">-- เลือกลูกค้า --</option>
                <?php while ($c = $customers->fetch_assoc()) { ?>
                    <option value="<?= $c['customer_id'] ?>">
                        <?= htmlspecialchars($c['customer_id'] . " - " . $c['first_name'] . " " . $c['last_name']) ?>
                    </option>
                <?php } ?>
            </select>

            <label for="copy_id">สำเนา / ชื่อหนังสือ</label>
            <select name="copy_id" id="copy_id" required>
                <option value="">-- เลือกสำเนา --</option>
                <?php while ($co = $copies->fetch_assoc()): ?>
                    <option value="<?= $co['copy_id'] ?>">
                        <?= $co['copy_id'] ?> - <?= htmlspecialchars($co['series_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="reserve_date">วันที่จอง</label>
            <input type="date" name="reserve_date" id="reserve_date" required>

            <label for="expire_date">วันที่หมดอายุ</label>
            <input type="date" name="expire_date" id="expire_date" required>

            <label for="reservation_status">สถานะ</label>
            <select id="reservation_status" name="reservation_status" required>
                <option value="active">active</option>
                <option value="expired">expired</option>
            </select>

            <input type="submit" value="เพิ่มการจอง">
        </form>
    </div>

</body>

</html>
