<?php
require_once "../../connect/connect.php";

// ดึงข้อมูล Customer, Copy_backup, Employees
$customers = $conn->query("SELECT customer_id, first_name, last_name FROM Customer");
$copies = $conn->query("
    SELECT Copy_backup.copy_id, Title.series_name
    FROM Copy_backup
    JOIN Title ON Copy_backup.title_id = Title.title_id
");
$employees = $conn->query("SELECT employee_id, first_name, last_name FROM Employees");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = (int)$_POST['customer_id'];
    $copy_id = (int)$_POST['copy_id'];
    $checked_out_employee_id = (int)$_POST['checked_out_employee_id'];
    $checked_in_employee_id = $_POST['checked_in_employee_id'] ? (int)$_POST['checked_in_employee_id'] : NULL;
    $loan_date = $_POST['loan_date'];
    $return_date = $_POST['return_date'] ?: NULL;
    $fine_fee = isset($_POST['fine_fee']) ? (float)$_POST['fine_fee'] : 0;

    $stmt = $conn->prepare("INSERT INTO Loan 
        (customer_id, copy_id, checked_out_employee_id, checked_in_employee_id, loan_date, return_date, fine_fee)
        VALUES (?,?,?,?,?,?,?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iiiisdd", $customer_id, $copy_id, $checked_out_employee_id, $checked_in_employee_id, $loan_date, $return_date, $fine_fee);
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
<title>เพิ่มรายการยืม (Loan)</title>
<style>
* { box-sizing: border-box; margin:0; padding:0; }
body { font-family:"Segoe UI", Arial, sans-serif; background:#f5f7fa; padding:30px; color:#2c3e50; }
.container { max-width:750px; margin:auto; background:#fff; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); padding:30px 40px; }
.toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
.title { font-size:24px; font-weight:600; }
.btn { display:inline-block; background:#3498db; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; transition:0.2s; }
.btn:hover { background:#2980b9; }
form label { display:block; margin-bottom:6px; font-weight:600; }
form select, form input { width:100%; padding:10px 12px; margin-bottom:20px; border:1px solid #ccc; border-radius:6px; font-size:14px; background:#fafafa; transition:0.2s; }
form select:focus, form input:focus { border-color:#3498db; outline:none; background:#fff; }
form input[type="submit"] { background:#3498db; color:#fff; border:none; cursor:pointer; padding:12px; font-weight:600; border-radius:8px; }
form input[type="submit"]:hover { background:#2980b9; }
</style>
</head>
<body>
<div class="container">
    <div class="toolbar">
        <div class="title">เพิ่มรายการยืม (Loan)</div>
        <a href="list.php" class="btn">กลับรายการยืม</a>
    </div>

    <form method="POST">
        <label for="customer_id">ลูกค้า</label>
        <select name="customer_id" id="customer_id" required>
            <option value="">-- เลือกลูกค้า --</option>
            <?php while($c = $customers->fetch_assoc()): ?>
                <option value="<?= $c['customer_id'] ?>"><?= $c['first_name'] ?> <?= $c['last_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <label for="copy_id">สำเนา / ชื่อหนังสือ</label>
        <select name="copy_id" id="copy_id" required>
            <option value="">-- เลือกสำเนา --</option>
            <?php while($co = $copies->fetch_assoc()): ?>
                <option value="<?= $co['copy_id'] ?>"><?= $co['copy_id'] ?> - <?= htmlspecialchars($co['series_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="checked_out_employee_id">พนักงานที่ทำรายการยืม</label>
        <select name="checked_out_employee_id" id="checked_out_employee_id" required>
            <option value="">-- เลือกพนักงาน --</option>
            <?php $employees->data_seek(0); while($e = $employees->fetch_assoc()): ?>
                <option value="<?= $e['employee_id'] ?>"><?= $e['first_name'] ?> <?= $e['last_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <label for="checked_in_employee_id">พนักงานที่รับคืน</label>
        <select name="checked_in_employee_id" id="checked_in_employee_id">
            <option value="">-- ยังไม่คืน --</option>
            <?php $employees->data_seek(0); while($e = $employees->fetch_assoc()): ?>
                <option value="<?= $e['employee_id'] ?>"><?= $e['first_name'] ?> <?= $e['last_name'] ?></option>
            <?php endwhile; ?>
        </select>

        <label for="loan_date">วันที่ยืม</label>
        <input type="date" name="loan_date" id="loan_date" required>

        <label for="return_date">วันที่คืนจริง</label>
        <input type="date" name="return_date" id="return_date">

        <label for="fine_fee">ค่าปรับ (บาท)</label>
        <input type="number" step="0.01" name="fine_fee" id="fine_fee" value="0">

        <input type="submit" name="submit" value="เพิ่มรายการยืม">
    </form>
</div>
</body>
</html>
