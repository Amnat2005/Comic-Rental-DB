<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Customer ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูลลูกค้า
$result = $conn->query("SELECT * FROM Customer WHERE customer_id=$id");
$row = $result->fetch_assoc();

if (!$row) {
    die("Error: Customer not found.");
}

$message = "";
if (isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE Customer SET first_name=?, last_name=?, phone=?, email=?, address=? WHERE customer_id=?");
    $stmt->bind_param("sssssi", $first_name, $last_name, $phone, $email, $address, $id);
    
    if ($stmt->execute()) {
        $message = "✅ แก้ไขข้อมูลลูกค้าสำเร็จแล้ว";
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
    <title>แก้ไขลูกค้า (Customer)</title>
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
        form input[type="email"] {
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
        <div class="title">แก้ไขลูกค้า (Customer)</div>
        <a href="list.php" class="btn">กลับรายการลูกค้า</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="first_name">ชื่อ</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>" required>

        <label for="last_name">นามสกุล</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>" required>

        <label for="phone">โทรศัพท์</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($row['phone']) ?>" required>

        <label for="email">อีเมล</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>

        <label for="address">ที่อยู่</label>
        <input type="text" id="address" name="address" value="<?= htmlspecialchars($row['address']) ?>" required>

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
