<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: No title ID specified.");
}

$id = (int)$_GET['id']; // ป้องกัน SQL Injection

// ลบ Title_Author ที่เชื่อมกับ title ก่อน
$conn->query("DELETE FROM Title_Author WHERE title_id = $id");

// เตรียมลบ Title หลัก
$stmt = $conn->prepare("DELETE FROM Title WHERE title_id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: list.php");
    exit;
} else {
    echo "Error deleting title: " . $stmt->error;
    $stmt->close();
    $conn->close();
}
?>
