<?php
require_once "../../connect/connect.php";

if (!isset($_GET['title_id']) || !isset($_GET['genre_id'])) {
    die("Error: Title ID or Genre ID not specified.");
}

$title_id = (int)$_GET['title_id'];
$genre_id = (int)$_GET['genre_id'];

$stmt = $conn->prepare("DELETE FROM Title_Genre WHERE title_id=? AND genre_id=?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $title_id, $genre_id);

if ($stmt->execute()) {
    echo "🗑️ ลบสำเร็จแล้ว";
    $stmt->close();
    $conn->close();
    header("Location: list.php");
    exit;
} else {
    echo "❌ Error: " . $stmt->error;
    $stmt->close();
    $conn->close();
}
?>
