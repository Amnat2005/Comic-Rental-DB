<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Member ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูลสมาชิก
$member = $conn->query("SELECT * FROM Member WHERE member_id=$id")->fetch_assoc();
if (!$member) {
    die("Error: Member not found.");
}

// ดึงข้อมูลลูกค้าสำหรับเลือก
$customers = $conn->query("SELECT * FROM Customer");

$message = "";
if (isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'];
    $join_date = $_POST['join_date'];
    $expiry_date = $_POST['expiry_date'];
    $member_type = $_POST['member_type'];
    $member_status = $_POST['member_status'];

    $stmt = $conn->prepare("UPDATE Member SET customer_id=?, join_date=?, expiry_date=?, member_type=?, member_status=? WHERE member_id=?");
    $stmt->bind_param("issssi", $customer_id, $join_date, $expiry_date, $member_type, $member_status, $id);

    if ($stmt->execute()) {
        $message = "✅ แก้ไขข้อมูลสมาชิกสำเร็จแล้ว";
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
    <title>แก้ไขข้อมูลสมาชิก (Member)</title>
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
        form input[type="text"],
        form input[type="date"],
        form select {
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
        <div class="title">แก้ไขข้อมูลสมาชิก (Member)</div>
        <a href="list.php" class="btn">กลับหน้ารายการสมาชิก</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="customer_id">ลูกค้า</label>
        <select id="customer_id" name="customer_id" required>
            <?php while($c = $customers->fetch_assoc()): ?>
                <option value="<?= $c['customer_id'] ?>" <?= $c['customer_id']==$member['customer_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['first_name'] . " " . $c['last_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="join_date">วันที่สมัคร</label>
        <input type="date" id="join_date" name="join_date" value="<?= $member['join_date'] ?>" required>

        <label for="expiry_date">วันหมดอายุ</label>
        <input type="date" id="expiry_date" name="expiry_date" value="<?= $member['expiry_date'] ?>" required>

        <label for="member_type">ประเภทสมาชิก</label>
        <input type="text" id="member_type" name="member_type" value="<?= htmlspecialchars($member['member_type']) ?>" required>

        <label for="member_status">สถานะ</label>
        <select id="member_status" name="member_status">
            <option value="active" <?= $member['member_status']=='active'?'selected':'' ?>>ใช้งานอยู่</option>
            <option value="inactive" <?= $member['member_status']=='inactive'?'selected':'' ?>>หมดอายุ</option>
        </select>

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
