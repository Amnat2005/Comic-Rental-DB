<?php
header('Content-Type: text/html; charset=UTF-8');
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Comic Rental DB</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* Reset & base */
* { box-sizing: border-box; margin:0; padding:0; }
body { font-family:"Segoe UI", Arial, sans-serif; background:#f5f7fa; color:#2c3e50; }

/* Top bar */
.navbar {
    display:flex;
    justify-content:flex-end;
    align-items:center;
    background: linear-gradient(135deg, #2c3e50, #3498db);
    padding:14px 30px;
    color:#fff;
    font-size:20px;
    font-weight:700;
    border-radius:0 0 10px 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: background 0.3s;
}
.navbar i { margin-right:10px; font-size:24px; }
.navbar:hover { background: linear-gradient(135deg, #34495e, #2980b9); }

/* Header */
header {
    text-align:center;
    margin:20px auto 25px auto;
    padding:20px 30px;
    background:#fff;
    max-width:900px;
    border-radius:12px;
    box-shadow:0 3px 15px rgba(0,0,0,0.1);
}
header h1 {
    font-size:30px;
    color:#2c3e50;
    margin-bottom:8px;
}
header p {
    font-size:17px;
    color:#555;
}

/* Container for cards */
.container {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
    gap:20px;
    padding:30px;
}

/* Card style */
.card {
    background:#fff;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    padding:25px;
    text-align:center;
    transition: transform 0.25s, box-shadow 0.25s;
}
.card:hover {
    transform: translateY(-6px);
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}
.card a {
    display:block;
    font-size:16px;
    font-weight:600;
    color:#3498db;
    text-decoration:none;
    transition: color 0.2s;
}
.card a:hover { color:#1d6fa5; }
.card i {
    font-size:40px;
    margin-bottom:12px;
    color:#3498db;
}

/* Responsive */
@media(max-width:480px){
    header h1 { font-size:22px; }
    header p { font-size:14px; }
    .card { padding:20px; }
    .navbar { flex-direction:column; align-items:flex-start; gap:8px; }
}
</style>
</head>
<body>

<!-- Top bar -->
<div class="navbar">
    <i class="fas fa-database"></i> Comic Rental DB
</div>

<!-- Header -->
<header>
    <h1>ฐานข้อมูลร้านเช่าหนังสือการ์ตูน</h1>
    <p>จัดการข้อมูลของแต่ละตารางอย่างมืออาชีพ</p>
</header>

<!-- Card grid -->
<div class="container">
    <div class="card"><i class="fas fa-user"></i><a href="admin/customer/list.php">Customer</a></div>
    <div class="card"><i class="fas fa-id-card"></i><a href="admin/member/list.php">Member</a></div>
    <div class="card"><i class="fas fa-briefcase"></i><a href="admin/job_position/list.php">Job Position</a></div>
    <div class="card"><i class="fas fa-users"></i><a href="admin/employees/list.php">Employees</a></div>
    <div class="card"><i class="fas fa-book"></i><a href="admin/publisher/list.php">Publisher</a></div>
    <div class="card"><i class="fas fa-pen-nib"></i><a href="admin/author/list.php">Author</a></div>
    <div class="card"><i class="fas fa-book-open"></i><a href="admin/title/list.php">Title</a></div>
    <div class="card"><i class="fas fa-tags"></i><a href="admin/genre/list.php">Genre</a></div>
    <div class="card"><i class="fas fa-map-marker-alt"></i><a href="admin/zone/list.php">Zone</a></div>
    <div class="card"><i class="fas fa-layer-group"></i><a href="admin/shelf/list.php">Shelf</a></div>
    <div class="card"><i class="fas fa-copy"></i><a href="admin/copy/list.php">Copy</a></div>
    <div class="card"><i class="fas fa-hand-holding-dollar"></i><a href="admin/loan/list.php">Loan</a></div>
    <div class="card"><i class="fas fa-calendar-check"></i><a href="admin/reservation/list.php">Reservation</a></div>
</div>

</body>
</html>
