<?php
require_once "../../connect/connect.php";

// ตรวจสอบว่า id ถูกส่งมาหรือไม่
if (!isset($_GET['id'])) {
    die("Error: No position ID specified.");
}

$id = (int)$_GET['id']; // ป้องกัน SQL injection

// เตรียม statement ลบ
$stmt = $conn->prepare("DELETE FROM Job_Position WHERE position_id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // ลบสำเร็จแล้ว redirect กลับ list.php
    $stmt->close();
    $conn->close();
    header("Location: list.php");
    exit;
} else {
    // มีข้อผิดพลาด เช่น FK constraint
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    die("Error deleting Job Position: " . htmlspecialchars($error));
}
?>
