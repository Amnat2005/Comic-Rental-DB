<?php
require_once "../../connect/connect.php";

// ดึงโซนมาเก็บเป็น array
$zones_res = $conn->query("SELECT * FROM zone");
$zones = [];
if ($zones_res) {
    while ($r = $zones_res->fetch_assoc()) $zones[] = $r;
}

$shelf_name = "";
$zone_id = "";
$capacity = 10; // default
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zone_id = isset($_POST['zone_id']) ? (int) $_POST['zone_id'] : null;
    $shelf_name = isset($_POST['shelf_name']) ? trim($_POST['shelf_name']) : "";
    $capacity = isset($_POST['capacity']) ? (int) $_POST['capacity'] : null;

    if (empty($shelf_name)) $errors[] = "กรุณากรอกชื่อชั้น.";
    if ($zone_id === null || $zone_id === 0) $errors[] = "กรุณาเลือกโซน.";
    if ($capacity === null || $capacity < 0) $errors[] = "กรุณากรอกค่าความจุที่ถูกต้อง.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO shelf (zone_id, shelf_name, capacity) VALUES (?, ?, ?)");
        if (!$stmt) die("SQL Error: " . $conn->error);
        $stmt->bind_param("isi", $zone_id, $shelf_name, $capacity);
        if ($stmt->execute()) {
            header("Location: list.php");
            exit;
        } else {
            $errors[] = "เพิ่มข้อมูลไม่สำเร็จ: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่ม Shelf</title>
    <style>
        body { font-family:"Segoe UI", Arial; background:#f5f7fa; padding:30px; }
        .container{ max-width:600px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 3px 10px rgba(0,0,0,0.1); }
        .error { background:#ffe6e6; border:1px solid #ffb3b3; padding:10px; margin-bottom:12px; border-radius:6px; color:#b30000; }
        label{ display:block; margin:8px 0 4px; font-weight:600;}
        input, select{ width:100%; padding:8px 10px; border:1px solid #ccc; border-radius:6px; margin-bottom:12px; }
        .btn{ background:#3498db; color:#fff; padding:10px 16px; border-radius:6px; border:none; cursor:pointer; }
    </style>
</head>
<body>
<div class="container">
    <h2>เพิ่ม Shelf</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" novalidate>
        <label for="zone_id">โซน</label>
        <select name="zone_id" id="zone_id" required>
            <option value="">-- เลือกโซน --</option>
            <?php foreach ($zones as $z): ?>
                <option value="<?= $z['zone_id'] ?>" <?= ($z['zone_id'] == $zone_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($z['zone_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="shelf_name">หมายเลขชั้น</label>
        <input type="text" name="shelf_name" id="shelf_name" value="<?= htmlspecialchars($shelf_name) ?>" required>

        <label for="capacity">ความจุชั้น (เล่ม)</label>
        <input type="number" name="capacity" id="capacity" value="<?= htmlspecialchars($capacity) ?>" min="0" required>

        <input type="submit" name="submit" value="เพิ่ม Shelf" class="btn">
    </form>
</div>
</body>
</html>
