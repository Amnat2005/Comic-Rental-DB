<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Reservation ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูลการจอง
$res = $conn->query("SELECT * FROM Reservation WHERE reservation_id=$id")->fetch_assoc();
if (!$res) {
    die("Error: Reservation not found.");
}

// กำหนดค่า default ให้ copy_id กัน warning
$res['copy_id'] = $res['copy_id'] ?? '';

// ดึงลูกค้าและ Copy (พร้อมชื่อหนังสือ)
$customers = $conn->query("SELECT customer_id, first_name, last_name FROM Customer");
$copies = $conn->query("
    SELECT Copy.copy_id, Title.series_name
    FROM Copy
    JOIN Title ON Copy.title_id = Title.title_id
");

$message = "";
if (isset($_POST['submit'])) {
    $customer_id = (int)$_POST['customer_id'];
    $copy_id = (int)$_POST['copy_id'];
    $reserve_date = $_POST['reserve_date'];
    $expire_date = $_POST['expire_date'];
    $reservation_status = $_POST['reservation_status'];

    // ดึง title_id จาก copy_id
    $res_title = $conn->query("SELECT title_id FROM Copy WHERE copy_id = $copy_id");
    $row_title = $res_title->fetch_assoc();
    $title_id = $row_title['title_id'] ?? 0;

    $stmt = $conn->prepare("UPDATE Reservation SET customer_id=?, title_id=?, copy_id=?, reserve_date=?, expire_date=?, reservation_status=? WHERE reservation_id=?");
    $stmt->bind_param("iiisssi", $customer_id, $title_id, $copy_id, $reserve_date, $expire_date, $reservation_status, $id);

    if ($stmt->execute()) {
        $message = "✅ แก้ไขการจองสำเร็จแล้ว";
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
    <title>แก้ไขการจอง (Reservation)</title>
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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
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
            transition: 0.2s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #2c3e50;
        }

        form select,
        form input[type="date"] {
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
            <div class="title">แก้ไขการจอง (Reservation)</div>
            <a href="list.php" class="btn">กลับหน้ารายการการจอง</a>
        </div>

        <?php if ($message) { ?>
            <div class="message"><?= $message ?></div>
        <?php } ?>

        <form method="post">
            <label for="customer_id">ลูกค้า</label>
            <select id="customer_id" name="customer_id" required>
                <option value="">-- เลือกลูกค้า --</option>
                <?php while ($c = $customers->fetch_assoc()): ?>
                    <option value="<?= $c['customer_id'] ?>" <?= $c['customer_id'] == $res['customer_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['customer_id'] . " - " . $c['first_name'] . " " . $c['last_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="copy_id">สำเนา / ชื่อหนังสือ</label>
            <select name="copy_id" id="copy_id" required>
                <option value="">-- เลือกสำเนา --</option>
                <?php while ($co = $copies->fetch_assoc()): ?>
                    <option value="<?= $co['copy_id'] ?>" <?= ($co['copy_id'] == $res['copy_id']) ? 'selected' : '' ?>>
                        <?= $co['copy_id'] ?> - <?= htmlspecialchars($co['series_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="reserve_date">วันที่จอง</label>
            <input type="date" id="reserve_date" name="reserve_date" value="<?= $res['reserve_date'] ?>" required>

            <label for="expire_date">วันหมดอายุ</label>
            <input type="date" id="expire_date" name="expire_date" value="<?= $res['expire_date'] ?>" required>

            <label for="reservation_status">สถานะ</label>
            <select id="reservation_status" name="reservation_status">
                <option value="active" <?= $res['reservation_status'] == 'active' ? 'selected' : '' ?>>active</option>
                <option value="expired" <?= $res['reservation_status'] == 'expired' ? 'selected' : '' ?>>expired</option>
            </select>

            <input type="submit" name="submit" value="บันทึกการแก้ไข">
        </form>
    </div>

</body>

</html>