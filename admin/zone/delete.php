<?php
require_once "../../connect/connect.php";

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (!isset($_GET['id'])) {
    die("Error: Zone ID not specified.");
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("DELETE FROM Zone WHERE zone_id=?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "🗑️ ลบสำเร็จแล้ว";
    $stmt->close();
    $conn->close();
    header("Location: list.php");
    exit;
} else {
    echo "❌ Error deleting zone: " . $stmt->error;
    $stmt->close();
    $conn->close();
}
?>
