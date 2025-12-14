<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Zone ID not specified.");
}

$id = (int)$_GET['id'];
$zone_res = $conn->query("SELECT * FROM Zone WHERE zone_id=$id");
$zone = $zone_res->fetch_assoc();
if (!$zone) {
    die("Error: Zone not found.");
}

$message = "";
if (isset($_POST['submit'])) {
    $zone_name = $_POST['zone_name'];

    $stmt = $conn->prepare("UPDATE Zone SET zone_name=? WHERE zone_id=?");
    $stmt->bind_param("si", $zone_name, $id);

    if ($stmt->execute()) {
        $message = "✅ แก้ไข Zone สำเร็จแล้ว";
        $zone['zone_name'] = $zone_name;
    } else {
        $message = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไข Zone</title>
<style>
body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f5f7fa; padding:30px; }
.container { max-width:600px; margin:auto; background:white; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); padding:30px; }
.toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; }
.title { font-size:24px; font-weight:600; color:#2c3e50; }
.btn { display:inline-block; padding:8px 16px; border-radius:6px; font-size:14px; text-decoration:none; color:white; background-color:#3498db; transition:.2s; }
.btn:hover { background-color:#2980b9; }
form label { display:block; margin-bottom:6px; font-weight:600; color:#2c3e50; }
form input[type="text"] { width:100%; padding:10px 12px; margin-bottom:20px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
form input[type="submit"] { background-color:#3498db; color:white; border:none; padding:10px 18px; border-radius:6px; cursor:pointer; font-size:14px; }
form input[type="submit"]:hover { background-color:#2980b9; }
.message { margin-bottom:20px; padding:12px 16px; border-radius:6px; background-color:#eef4fb; color:#2c3e50; font-weight:500; }
</style>
</head>
<body>

<div class="container">
    <div class="toolbar">
        <div class="title">แก้ไข Zone</div>
        <a href="list.php" class="btn">กลับหน้ารายการ Zone</a>
    </div>

    <?php if ($message) { ?>
        <div class="message"><?= $message ?></div>
    <?php } ?>

    <form method="post">
        <label for="zone_name">โซน</label>
        <input type="text" id="zone_name" name="zone_name" value="<?= htmlspecialchars($zone['zone_name']) ?>" required>
        <input type="submit" name="submit" value="บันทึกการแก้ไข">
    </form>
</div>

</body>
</html>
