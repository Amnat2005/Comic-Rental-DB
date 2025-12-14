<?php 
require_once "../../connect/connect.php";

// ดึงชื่อหนังสือและผู้แต่ง
$titles = $conn->query("SELECT title_id, series_name FROM Title");
$authors = $conn->query("SELECT author_id, author_name FROM Author");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title_id = $_POST['title_id'];
    $author_id = $_POST['author_id'];

    $sql = "INSERT INTO Title_Author (title_id, author_id) VALUES ('$title_id', '$author_id')";
    if ($conn->query($sql) === TRUE) {
        header("Location: list.php");
        exit;
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่ม Title-Author</title>
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
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <div class="title">เพิ่ม Title Author</div>
        <a href="list.php" class="btn">กลับรายการ Title Author</a>
    </div>

    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <label for="title_id">ชื่อหนังสือ</label>
        <select name="title_id" id="title_id" required>
            <?php while ($t = $titles->fetch_assoc()) { ?>
                <option value="<?= $t['title_id'] ?>"><?= htmlspecialchars($t['series_name']) ?></option>
            <?php } ?>
        </select>

        <label for="author_id">ผู้แต่ง</label>
        <select name="author_id" id="author_id" required>
            <?php while ($a = $authors->fetch_assoc()) { ?>
                <option value="<?= $a['author_id'] ?>"><?= htmlspecialchars($a['author_name']) ?></option>
            <?php } ?>
        </select>

        <input type="submit" value="เพิ่ม Mapping">
    </form>
</div>

</body>
</html>
