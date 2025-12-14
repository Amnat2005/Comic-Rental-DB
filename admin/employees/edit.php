<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Employee ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูลพนักงานคนที่เลือก
$emp = $conn->query("SELECT * FROM Employees WHERE employee_id=$id")->fetch_assoc();
if (!$emp) {
    die("Error: Employee not found.");
}

// ดึงตำแหน่งทั้งหมด
$positions = $conn->query("SELECT * FROM Job_position");

// ดึงพนักงานทั้งหมด (ไว้เลือกหัวหน้า)
$managers = $conn->query("SELECT * FROM Employees WHERE employee_id != $id");

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position_id = (int)$_POST['position_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $hire_date = $_POST['hire_date'];
    $employment_status = $_POST['employment_status'];
    $manager_id = $_POST['manager_id'] !== "" ? (int)$_POST['manager_id'] : "NULL";

    $update = "UPDATE Employees 
               SET position_id=$position_id, first_name='$first_name', last_name='$last_name', 
                   hire_date='$hire_date', employment_status='$employment_status', manager_id=$manager_id
               WHERE employee_id=$id";

    if ($conn->query($update) === TRUE) {
        $message = "✅ แก้ไขข้อมูลพนักงานสำเร็จแล้ว";
        // รีโหลดข้อมูลใหม่เพื่อแสดงผลในฟอร์ม
        $emp = $conn->query("SELECT * FROM Employees WHERE employee_id=$id")->fetch_assoc();
    } else {
        $message = "❌ เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลพนักงาน</title>
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
        form select, form input[type="text"], form input[type="date"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        form button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        form button:hover {
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
        <div class="title">แก้ไขข้อมูลพนักงาน</div>
        <a href="list.php" class="btn">กลับรายการพนักงาน</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="POST">
        <label>ชื่อ</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($emp['first_name']) ?>" required>

        <label>นามสกุล</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($emp['last_name']) ?>" required>

        <label>ตำแหน่ง</label>
        <select name="position_id" required>
            <?php while ($pos = $positions->fetch_assoc()) { ?>
                <option value="<?= $pos['position_id'] ?>" <?= $emp['position_id']==$pos['position_id']?'selected':'' ?>>
                    <?= htmlspecialchars($pos['position_name']) ?>
                </option>
            <?php } ?>
        </select>

        <label>วันที่เริ่มงาน</label>
        <input type="date" name="hire_date" value="<?= $emp['hire_date'] ?>" required>

        <label>สถานะ</label>
        <select name="employment_status">
            <option value="active" <?= $emp['employment_status']=='active'?'selected':'' ?>>Active</option>
            <option value="inactive" <?= $emp['employment_status']=='inactive'?'selected':'' ?>>Inactive</option>
        </select>

        <label>หัวหน้า</label>
        <select name="manager_id">
            <option value="">-- ไม่มีหัวหน้า --</option>
            <?php while ($mgr = $managers->fetch_assoc()) { ?>
                <option value="<?= $mgr['employee_id'] ?>" <?= $emp['manager_id']==$mgr['employee_id']?'selected':'' ?>>
                    <?= htmlspecialchars($mgr['first_name'] . " " . $mgr['last_name']) ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit">บันทึกการแก้ไข</button>
    </form>
</div>

</body>
</html>
