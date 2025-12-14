<?php
require_once "../../connect/connect.php";

if (!isset($_GET['loan_id'])) {
    die("Error: Loan ID not specified.");
}

$loan_id = (int)$_GET['loan_id'];

// ดึงข้อมูลการยืม
$result = $conn->query("SELECT * FROM Loan WHERE loan_id=$loan_id");
$row = $result->fetch_assoc();

if (!$row) {
    die("Error: Loan not found.");
}

$message = "";
if (isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'];
    $copy_id = $_POST['copy_id'];
    $checked_out_employee_id = $_POST['checked_out_employee_id'];
    $checked_in_employee_id = $_POST['checked_in_employee_id'];
    $checkout_date = $_POST['checkout_date'];
    $due_date = $_POST['due_date'];
    $return_date = $_POST['return_date'];
    $fine_fee = $_POST['fine_fee'];

    $stmt = $conn->prepare("UPDATE Loan SET customer_id=?, copy_id=?, checked_out_employee_id=?, checked_in_employee_id=?, checkout_date=?, due_date=?, return_date=?, fine_fee=? WHERE loan_id=?");
    $stmt->bind_param("iiiisssdi", $customer_id, $copy_id, $checked_out_employee_id, $checked_in_employee_id, $checkout_date, $due_date, $return_date, $fine_fee, $loan_id);
    
    if ($stmt->execute()) {
        $message = "✅ แก้ไขข้อมูลการยืมสำเร็จแล้ว";
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
    <title>แก้ไขการยืมหนังสือ (Loan)</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 650px;
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
        form input[type="number"],
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
        <div class="title">แก้ไขการยืมหนังสือ (Loan)</div>
        <a href="list.php" class="btn">กลับไปหน้ารายการยืม</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="customer_id">รหัสลูกค้า (Customer ID)</label>
        <input type="number" id="customer_id" name="customer_id" value="<?= htmlspecialchars($row['customer_id']) ?>" required>

        <label for="copy_id">รหัสสำเนา (Copy ID)</label>
        <input type="number" id="copy_id" name="copy_id" value="<?= htmlspecialchars($row['copy_id']) ?>" required>

        <label for="checked_out_employee_id">รหัสพนักงานผู้ให้ยืม</label>
        <input type="number" id="checked_out_employee_id" name="checked_out_employee_id" value="<?= htmlspecialchars($row['checked_out_employee_id']) ?>">

        <label for="checked_in_employee_id">รหัสพนักงานผู้รับคืน</label>
        <input type="number" id="checked_in_employee_id" name="checked_in_employee_id" value="<?= htmlspecialchars($row['checked_in_employee_id']) ?>">

        <label for="checkout_date">วันที่ยืม</label>
        <input type="date" id="checkout_date" name="checkout_date" value="<?= htmlspecialchars($row['checkout_date']) ?>">

        <label for="due_date">กำหนดคืน</label>
        <input type="date" id="due_date" name="due_date" value="<?= htmlspecialchars($row['due_date']) ?>">

        <label for="return_date">วันที่คืน</label>
        <input type="date" id="return_date" name="return_date" value="<?= htmlspecialchars($row['return_date']) ?>">

        <label for="fine_fee">ค่าปรับ (บาท)</label>
        <input type="number" step="0.01" id="fine_fee" name="fine_fee" value="<?= htmlspecialchars($row['fine_fee']) ?>">

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
