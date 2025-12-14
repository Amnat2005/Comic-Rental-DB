<?php
require_once "../../connect/connect.php";

// ดึงข้อมูล shelf พร้อม join กับ zone
$result = $conn->query("SELECT s.*, z.zone_name FROM shelf s JOIN zone z ON s.zone_id=z.zone_id");
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายการชั้นวาง (Shelf)</title>
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
    <div class="title">รายการชั้นวาง (Shelf)</div>
    <div class="actions">
        <a href="../../index.php" class="back-button">🏠 กลับหน้าหลัก</a>
        <a href="add.php" class="btn">➕ เพิ่มชั้นวาง</a>
    </div>
</div>

<div class="table-control">
    <input type="text" id="searchInput" placeholder="ค้นหาชั้นวาง..." onkeyup="filterTable()">
    <div class="order-container">
        <label for="orderSelect">เรียงตาม</label>
        <select id="orderSelect" onchange="orderTable()">
            <option value="shelf_id">ID</option>
            <option value="zone_name">โซน</option>
            <option value="shelf_name">ชื่อชั้น</option>
            <option value="capacity">ความจุชั้น</option>
        </select>
    </div>
</div>

<div class="table-container">
    <table id="shelfTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>โซน</th>
                <th>หมายเลขชั้น</th>
                <th>ความจุชั้น</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td data-label="ID"><?= htmlspecialchars($row['shelf_id']) ?></td>
                <td data-label="โซน"><?= htmlspecialchars($row['zone_name']) ?></td>
                <td data-label="ชื่อชั้น"><?= htmlspecialchars($row['shelf_name']) ?></td>
                <td data-label="ความจุชั้น"><?= htmlspecialchars($row['capacity']) ?></td>
                <td class="td-actions" data-label="Action">
                    <a href="edit.php?id=<?= $row['shelf_id'] ?>">Edit</a>
                    <a href="delete.php?id=<?= $row['shelf_id'] ?>" onclick="return confirm('ต้องการลบชั้นวางนี้หรือไม่?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
function filterTable() {
    const filter = document.getElementById("searchInput").value.toLowerCase();
    const table = document.getElementById("shelfTable");
    const trs = table.getElementsByTagName("tr");
    for(let i=1;i<trs.length;i++){
        const tds = trs[i].getElementsByTagName("td");
        let show=false;
        for(let j=0;j<tds.length-1;j++){
            if(tds[j].textContent.toLowerCase().indexOf(filter)>-1){ show=true; break; }
        }
        trs[i].style.display = show?"":"none";
    }
}

function orderTable() {
    const table = document.getElementById("shelfTable").tBodies[0];
    const rows = Array.from(table.rows);
    const key = document.getElementById("orderSelect").value;
    const colIndexMap = { "shelf_id":0, "zone_name":1, "shelf_name":2, "capacity":3 };
    const colIndex = colIndexMap[key];
    rows.sort((a,b)=>{
        let aText=a.cells[colIndex].textContent.toLowerCase();
        let bText=b.cells[colIndex].textContent.toLowerCase();
        if(!isNaN(aText) && !isNaN(bText)){ aText=parseFloat(aText); bText=parseFloat(bText); }
        return aText>bText?1:aText<bText?-1:0;
    });
    rows.forEach(row=>table.appendChild(row));
}
</script>

</body>
</html>
