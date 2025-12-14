<?php
require_once "../../connect/connect.php";

// ดึง Customer เพื่อเลือก
$customers = $conn->query("SELECT * FROM Customer");

if(isset($_POST['submit'])){
    $customer_id = $_POST['customer_id'];
    $join_date = $_POST['join_date'];
    $expiry_date = $_POST['expiry_date'];
    $member_type = $_POST['member_type'];
    $member_status = $_POST['member_status'];

    $stmt = $conn->prepare("INSERT INTO Member (customer_id, join_date, expiry_date, member_type, member_status) VALUES (?,?,?,?,?)");
    $stmt->bind_param("issss",$customer_id, $join_date, $expiry_date, $member_type, $member_status);
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
    <title>เพิ่มสมาชิก (Member)</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 700px;
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
        .btn:hover {
            background-color: #2980b9;
        }
        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #2c3e50;
        }
        form input, form select {
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
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <div class="title">เพิ่มสมาชิก (Member)</div>
        <a href="list.php" class="btn">กลับรายการสมาชิก</a>
    </div>

    <form method="post">
        <label for="customer_id">ลูกค้า</label>
        <select name="customer_id" id="customer_id" required>
            <?php while($c=$customers->fetch_assoc()): ?>
                <option value="<?= $c['customer_id'] ?>"><?= $c['first_name']." ".$c['last_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <label for="join_date">วันที่เข้าร่วม</label>
        <input type="date" name="join_date" id="join_date" required>

        <label for="expiry_date">วันที่หมดอายุ</label>
        <input type="date" name="expiry_date" id="expiry_date" required>

        <label for="member_type">ประเภทสมาชิก</label>
        <input type="text" name="member_type" id="member_type" required>

        <label for="member_status">สถานะ</label>
        <select name="member_status" id="member_status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <input type="submit" name="submit" value="เพิ่มสมาชิก">
    </form>
</div>

</body>
</html>
