<?php
require_once "../../connect/connect.php";

// ดึงข้อมูล Reservation พร้อมชื่อหนังสือและชื่อลูกค้า
$sql = "
SELECT 
    Reservation.*, 
    Title.series_name,
    Customer.first_name,
    Customer.last_name
FROM Reservation
JOIN Title ON Reservation.title_id = Title.title_id
JOIN Customer ON Reservation.customer_id = Customer.customer_id
";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายการจอง (Reservation)</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:"Segoe UI", Arial, sans-serif; background-color:#f5f7fa; color:#333; padding:30px; }
        a { text-decoration:none; color:inherit; }

        .toolbar { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:20px; gap:10px; }
        .toolbar .title { font-size:24px; font-weight:600; color:#2c3e50; }
        .toolbar .actions { display:flex; gap:10px; flex-wrap:wrap; }
        .btn { display:inline-block; background-color:#3498db; color:white; padding:8px 16px; border-radius:6px; font-size:14px; transition:0.2s; }
        .btn:hover { background-color:#2980b9; }
        .back-button { display:inline-block; background-color:#ecf0f1; color:#2c3e50; padding:8px 14px; border-radius:6px; font-size:14px; transition:0.2s; }
        .back-button:hover { background-color:#d0d7de; }

        .table-control { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; gap:10px; flex-wrap:wrap; }
        #searchInput { flex-grow:1; padding:10px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px; box-shadow:0 2px 4px rgba(0,0,0,0.05); transition:0.2s; }
        #searchInput:focus { border-color:#3498db; box-shadow:0 2px 6px rgba(52,152,219,0.3); outline:none; }
        .order-container { display:flex; align-items:center; gap:5px; white-space:nowrap; }
        .order-container label { font-weight:500; color:#2c3e50; }
        #orderSelect { padding:6px 10px; border:1px solid #ccc; border-radius:6px; font-size:14px; background-color:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.05); cursor:pointer; min-width:120px; transition:0.2s; }
        #orderSelect:hover, #orderSelect:focus { border-color:#3498db; box-shadow:0 2px 4px rgba(52,152,219,0.3); outline:none; }

        .table-container { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; background-color:white; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        thead { background:linear-gradient(135deg, #34495e, #2980b9); }
        thead th { color:white; font-size:14px; font-weight:600; padding:12px 16px; text-align:left; }
        tbody tr { border-bottom:1px solid #e1e4e8; }
        tbody tr:last-child { border-bottom:none; }
        tbody tr:nth-child(even) { background-color:#f9fafe; }
        tbody tr:hover { background-color:#eef4fb; }
        tbody td { padding:12px 16px; font-size:14px; vertical-align:middle; }
        .td-actions { white-space:nowrap; }
        .td-actions a { margin-right:8px; color:#3498db; font-weight:500; }
        .td-actions a:hover { color:#1d6fa5; }

        @media (max-width:768px) {
            .toolbar { flex-direction:column; align-items:flex-start; gap:12px; }
            .table-control { flex-direction:column; align-items:stretch; }
            table, thead, tbody, th, td, tr { display:block; }
            thead { display:none; }
            tbody tr { margin-bottom:16px; display:block; border:1px solid #e1e4e8; border-radius:6px; }
            tbody td { display:flex; justify-content:space-between; padding:10px 12px; }
            tbody td::before { content:attr(data-label); font-weight:600; color:#555; }
            .td-actions { text-align:right; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <div class="title">รายการจอง (Reservation)</div>
    <div class="actions">
        <a href="../../index.php" class="back-button">🏠 กลับหน้าหลัก</a>
        <a href="add.php" class="btn">➕ เพิ่มการจอง</a>
    </div>
</div>

<div class="table-control">
    <input type="text" id="searchInput" placeholder="ค้นหาการจอง..." onkeyup="filterTable()">
    <div class="order-container">
        <label for="orderSelect">เรียงตาม</label>
        <select id="orderSelect" onchange="orderTable()">
            <option value="reservation_id">ID</option>
            <option value="customer_name">ลูกค้า</option>
            <option value="series_name">ชื่อหนังสือ</option>
            <option value="reserve_date">วันที่จอง</option>
            <option value="expire_date">วันที่หมดอายุ</option>
        </select>
    </div>
</div>

<div class="table-container">
    <table id="reservationTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>ลูกค้า</th>
                <th>รหัสหนังสือ</th>
                <th>ชื่อหนังสือ</th>
                <th>วันที่จอง</th>
                <th>วันที่หมดอายุ</th>
                <th>สถานะ</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td data-label="ID"><?= htmlspecialchars($row['reservation_id']) ?></td>
                <td data-label="ลูกค้า"><?= htmlspecialchars($row['customer_id'] . ' - ' . $row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td data-label="รหัสหนังสือ"><?= htmlspecialchars($row['title_id']) ?></td>
                <td data-label="ชื่อหนังสือ"><?= htmlspecialchars($row['series_name']) ?></td>
                <td data-label="วันที่จอง"><?= htmlspecialchars($row['reserve_date']) ?></td>
                <td data-label="วันที่หมดอายุ"><?= htmlspecialchars($row['expire_date']) ?></td>
                <td data-label="สถานะ"><?= htmlspecialchars($row['reservation_status']) ?></td>
                <td class="td-actions" data-label="Action">
                    <a href="edit.php?id=<?= $row['reservation_id'] ?>">Edit</a>
                    <a href="delete.php?id=<?= $row['reservation_id'] ?>" onclick="return confirm('ต้องการลบการจองนี้หรือไม่?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("reservationTable");
    const trs = table.getElementsByTagName("tr");
    for(let i=1;i<trs.length;i++){
        const tds = trs[i].getElementsByTagName("td");
        let show=false;
        for(let j=0;j<tds.length-1;j++){
            if(tds[j].textContent.toLowerCase().indexOf(filter)>-1){ show=true; break; }
        }
        trs[i].style.display = show ? "" : "none";
    }
}

function orderTable() {
    const table = document.getElementById("reservationTable").tBodies[0];
    const rows = Array.from(table.rows);
    const key = document.getElementById("orderSelect").value;
    const colIndexMap = {
        "reservation_id":0,
        "customer_name":1,
        "series_name":3,
        "reserve_date":4,
        "expire_date":5
    };
    const colIndex = colIndexMap[key];
    rows.sort((a,b)=>{
        let aText=a.cells[colIndex].textContent.toLowerCase();
        let bText=b.cells[colIndex].textContent.toLowerCase();
        if(!isNaN(aText) && !isNaN(bText)) { aText=parseFloat(aText); bText=parseFloat(bText); }
        return aText>bText?1:aText<bText?-1:0;
    });
    rows.forEach(row=>table.appendChild(row));
}
</script>

</body>
</html>
