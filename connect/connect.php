<?php
// ข้อมูลการเชื่อมต่อฐานข้อมูล
$host = "localhost";      // หรือ IP server ของคุณ
$dbname = "Comic_rental"; // ชื่อฐานข้อมูล
$username = "root";       // ชื่อผู้ใช้ MySQL
$password = "";           // รหัสผ่าน MySQL (ถ้าไม่มี ให้เว้นว่าง)

// สร้างการเชื่อมต่อ
$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่า charset เป็น UTF-8
$conn->set_charset("utf8mb4");

//echo "Connected successfully"; // สำหรับทดสอบการเชื่อมต่อ
?>
