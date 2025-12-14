<?php
require_once "../../connect/connect.php";

if (!isset($_GET['title_id']) || !isset($_GET['genre_id'])) {
    die("Error: Title-Genre ID not specified.");
}

$title_id_old = (int)$_GET['title_id'];
$genre_id_old = (int)$_GET['genre_id'];

// ดึงข้อมูล Mapping ปัจจุบัน
$data_res = $conn->query("SELECT * FROM Title_Genre WHERE title_id=$title_id_old AND genre_id=$genre_id_old");
$data = $data_res->fetch_assoc();
if (!$data) {
    die("Error: Mapping not found.");
}

// ดึงข้อมูล Title และ Genre สำหรับเลือก
$titles = $conn->query("SELECT title_id, series_name FROM Title");
$genres = $conn->query("SELECT genre_id, genre_name FROM Genre");

$message = "";
if (isset($_POST['submit'])) {
    $title_id_new = $_POST['title_id'];
    $genre_id_new = $_POST['genre_id'];

    if ($title_id_new != $data['title_id'] || $genre_id_new != $data['genre_id']) {
        // ตรวจสอบซ้ำ
        $check = $conn->query("SELECT * FROM Title_Genre WHERE title_id=$title_id_new AND genre_id=$genre_id_new");
        if ($check->num_rows > 0) {
            $message = "❌ Mapping นี้มีอยู่แล้ว ไม่สามารถซ้ำกันได้";
        } else {
            $sql = "UPDATE Title_Genre 
                    SET title_id='$title_id_new', genre_id='$genre_id_new'
                    WHERE title_id={$data['title_id']} AND genre_id={$data['genre_id']}";
            if ($conn->query($sql) === TRUE) {
                $message = "✅ แก้ไข Mapping สำเร็จแล้ว";
                $data['title_id'] = $title_id_new;
                $data['genre_id'] = $genre_id_new;
            } else {
                $message = "❌ เกิดข้อผิดพลาด: " . $conn->error;
            }
        }
    } else {
        $message = "⚠️ ไม่มีการเปลี่ยนแปลงใด ๆ";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไข Title-Genre Mapping</title>
<style>
body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f5f7fa; padding:30px; }
.container { max-width:600px; margin:auto; background:white; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); padding:30px; }
.toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
.title { font-size:24px; font-weight:600; color:#2c3e50; }
.btn { display:inline-block; padding:8px 16px; border-radius:6px; font-size:14px; text-decoration:none; color:white; background-color:#3498db; transition:.2s; }
.btn:hover { background-color:#2980b9; }
form label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
form select { width:100%; padding:10px 12px; margin-bottom:20px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
form input[type="submit"] { background-color:#3498db; color:white; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-size:14px; }
form input[type="submit"]:hover { background-color:#2980b9; }
.message { margin-bottom:20px; padding:12px 16px; border-radius:6px; background-color:#eef4fb; color:#2c3e50; font-weight:500; }
</style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <div class="title">แก้ไข Title-Genre Mapping</div>
        <a href="list.php" class="btn">กลับหน้ารายการ Mapping</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="title_id">Title</label>
        <select id="title_id" name="title_id" required>
            <?php while($t = $titles->fetch_assoc()): ?>
                <option value="<?= $t['title_id'] ?>" <?= $t['title_id']==$data['title_id']?'selected':'' ?>>
                    <?= htmlspecialchars($t['series_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="genre_id">Genre</label>
        <select id="genre_id" name="genre_id" required>
            <?php while($g = $genres->fetch_assoc()): ?>
                <option value="<?= $g['genre_id'] ?>" <?= $g['genre_id']==$data['genre_id']?'selected':'' ?>>
                    <?= htmlspecialchars($g['genre_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
