<?php 
require_once "../../connect/connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position_name = $_POST['position_name'];

    $stmt = $conn->prepare("INSERT INTO Job_position (position_name) VALUES (?)");
    $stmt->bind_param("s", $position_name);
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
    <title>เพิ่มตำแหน่งงาน (Job Position)</title>
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
        .btn:hover {
            background-color: #2980b9;
        }
        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #2c3e50;
        }
        form input[type="text"] {
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
        <div class="title">เพิ่มตำแหน่งงาน (Job Position)</div>
        <a href="list.php" class="btn">กลับรายการตำแหน่งงาน</a>
    </div>

    <form method="POST">
        <label for="position_name">ชื่อตำแหน่งงาน</label>
        <input type="text" name="position_name" id="position_name" required>
        <input type="submit" name="submit" value="เพิ่มตำแหน่งงาน">
    </form>
</div>

</body>
</html>
