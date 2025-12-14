<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id = $_GET['id'];

// เตรียม statement ลบ
$stmt = $conn->prepare("DELETE FROM Author WHERE author_id=?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // ลบสำเร็จ
    $stmt->close();
    $conn->close();
    header("Location: list.php");
    exit;
} else {
    // มี FK constraint หรือ error อื่น
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    die("Error deleting Author: " . htmlspecialchars($error));
}
?>
