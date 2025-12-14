<?php
require_once "../../connect/connect.php";

if(isset($_POST['submit'])){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO Customer (first_name,last_name,phone,email,address) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss",$first_name,$last_name,$phone,$email,$address);
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
    <title>เพิ่มลูกค้า (Customer)</title>
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
        form input[type="text"], form input[type="email"] {
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
        <div class="title">เพิ่มลูกค้า (Customer)</div>
        <a href="list.php" class="btn">กลับรายการลูกค้า</a>
    </div>

    <form method="post">
        <label for="first_name">ชื่อ</label>
        <input type="text" name="first_name" id="first_name" required>

        <label for="last_name">นามสกุล</label>
        <input type="text" name="last_name" id="last_name" required>

        <label for="phone">เบอร์โทร</label>
        <input type="text" name="phone" id="phone" required>

        <label for="email">อีเมล</label>
        <input type="email" name="email" id="email" required>

        <label for="address">ที่อยู่</label>
        <input type="text" name="address" id="address" required>

        <input type="submit" name="submit" value="เพิ่มลูกค้า">
    </form>
</div>

</body>
</html>
