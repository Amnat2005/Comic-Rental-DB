<?php
require_once "../../connect/connect.php";

// ตรวจสอบว่า loan_id ถูกส่งมาหรือไม่
if (!isset($_GET['loan_id'])) {
    die("Error: No loan ID specified.");
}

$loan_id = (int)$_GET['loan_id']; // แปลงเป็น integer ป้องกัน SQL injection

// เตรียม statement ลบ
$stmt = $conn->prepare("DELETE FROM Loan WHERE loan_id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $loan_id);

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
    die("Error deleting Loan record: " . htmlspecialchars($error));
}
?>
