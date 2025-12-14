<?php
require_once "../../connect/connect.php";

if (!isset($_GET['title_id']) || !isset($_GET['author_id'])) {
    die("Error: Title ID or Author ID not specified.");
}

$title_id = (int)$_GET['title_id'];
$author_id = (int)$_GET['author_id'];

$stmt = $conn->prepare("DELETE FROM Title_Author WHERE title_id=? AND author_id=?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $title_id, $author_id);

if ($stmt->execute()) {
    echo "🗑️ ลบสำเร็จแล้ว";
    $stmt->close();
    $conn->close();
    // สามารถ redirect กลับ list.php ได้ถ้าต้องการ
    header("Location: list.php");
    exit;
} else {
    echo "❌ Error: " . $stmt->error;
    $stmt->close();
    $conn->close();
}
?>
