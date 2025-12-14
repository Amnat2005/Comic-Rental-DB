<?php
require_once "../../connect/connect.php";

$positions = $conn->query("SELECT * FROM Job_position");
$managers = $conn->query("SELECT * FROM Employees");

if(isset($_POST['submit'])){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $position_id = $_POST['position_id'];
    $manager_id = $_POST['manager_id'] ?: NULL;
    $hire_date = $_POST['hire_date'];
    $employment_status = $_POST['employment_status'];

    $stmt = $conn->prepare("INSERT INTO Employees (first_name, last_name, position_id, manager_id, hire_date, employment_status) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssiiss", $first_name, $last_name, $position_id, $manager_id, $hire_date, $employment_status);
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
    <title>เพิ่มพนักงาน</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 900px;
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
        form input[type="text"], form input[type="date"], form select {
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
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        form input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <div class="title">เพิ่มพนักงาน (Employee)</div>
        <a href="list.php" class="btn">กลับรายการ Employee</a>
    </div>

    <form method="post">
        <label for="first_name">ชื่อ</label>
        <input type="text" name="first_name" id="first_name" required>

        <label for="last_name">นามสกุล</label>
        <input type="text" name="last_name" id="last_name" required>

        <label for="position_id">ตำแหน่ง</label>
        <select name="position_id" id="position_id" required>
            <?php while($p = $positions->fetch_assoc()): ?>
                <option value="<?= $p['position_id'] ?>"><?= htmlspecialchars($p['position_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="manager_id">ผู้จัดการ (Manager)</label>
        <select name="manager_id" id="manager_id">
            <option value="">-- ไม่มี --</option>
            <?php while($m = $managers->fetch_assoc()): ?>
                <option value="<?= $m['employee_id'] ?>"><?= htmlspecialchars($m['first_name'] . " " . $m['last_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="hire_date">วันที่เริ่มงาน</label>
        <input type="date" name="hire_date" id="hire_date" required>

        <label for="employment_status">สถานะ</label>
        <select name="employment_status" id="employment_status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <input type="submit" name="submit" value="เพิ่ม Employee">
    </form>
</div>

</body>
</html>
