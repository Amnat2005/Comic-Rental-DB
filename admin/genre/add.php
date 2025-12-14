<?php
require_once "../../connect/connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $genre_name = $_POST['genre_name'];

    $stmt = $conn->prepare("INSERT INTO Genre (genre_name) VALUES (?)");
    $stmt->bind_param("s", $genre_name);
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
    <title>เพิ่มประเภทหนังสือ (Genre)</title>
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
        <div class="title">เพิ่มประเภทหนังสือ (Genre)</div>
        <a href="list.php" class="btn">กลับรายการ Genre</a>
    </div>

    <form method="POST">
        <label for="genre_name">ประเภทหนังสือ</label>
        <input type="text" name="genre_name" id="genre_name" required>
        <input type="submit" name="submit" value="เพิ่มประเภทหนังสือ">
    </form>
</div>

</body>
</html>
