<?php
require_once "../../connect/connect.php";
$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM Shelf WHERE shelf_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$stmt->close();
header("Location: list.php");
exit;
?>
