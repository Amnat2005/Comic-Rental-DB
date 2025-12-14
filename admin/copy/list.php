<?php
require_once "../../connect/connect.php";

// Query ข้อมูล copy_backup
$result = $conn->query("
    SELECT c.copy_id, t.series_name, s.shelf_name, c.book_condition, c.copy_status, c.acquired_date, c.purchase_price
    FROM copy_backup c
    JOIN title t ON c.title_id = t.title_id
    JOIN shelf s ON c.shelf_id = s.shelf_id
");

if(!$result){
    die("Query failed: ".$conn->error);
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายการสำเนา (Copy)</title>
<style>
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:"Segoe UI", Arial, sans-serif; background:#f5f7fa; color:#333; padding:30px; }
a { text-decoration:none; color:inherit; }

.toolbar { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:20px; gap:10px; }
.toolbar .title { font-size:24px; font-weight:600; color:#2c3e50; }
.actions a { display:inline-block; background:#3498db; color:#fff; padding:8px 16px; border-radius:6px; transition:.2s; }
.actions a:hover { background:#2980b9; }

.table-control { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px; }
#searchInput { padding:10px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px; flex:1; min-width:0; }
#searchInput:focus { border-color:#3498db; box-shadow:0 2px 6px rgba(52,152,219,0.3); outline:none; }
#orderSelect { padding:6px 10px; border:1px solid #ccc; border-radius:6px; font-size:14px; background:#fff; cursor:pointer; min-width:120px; }

.table-container { overflow-x:auto; }
table { width:100%; border-collapse:collapse; background:#fff; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
thead { background:linear-gradient(135deg,#34495e,#2980b9); }
thead th { color:#fff; font-weight:600; padding:12px 16px; text-align:left; font-size:14px; }
tbody td { padding:12px 16px; font-size:14px; vertical-align:middle; }
tbody tr:nth-child(even) { background:#f9fafe; }
tbody tr:hover { background:#eef4fb; }
.td-actions a { margin-right:8px; color:#3498db; font-weight:500; }
.td-actions a:hover { color:#1d6fa5; }

@media(max-width:768px){
.toolbar{flex-direction:column;align-items:flex-start;gap:12px;}
.table-control{flex-direction:column;align-items:stretch;}
table,thead,tbody,th,td,tr{display:block;}
thead{display:none;}
tbody tr{margin-bottom:16px;display:block;border:1px solid #e1e4e8;border-radius:6px;}
tbody td{display:flex;justify-content:space-between;padding:10px 12px;}
tbody td::before{content:attr(data-label);font-weight:600;color:#555;}
.td-actions{text-align:right;}
}
</style>
</head>
<body>

<div class="toolbar">
    <div class="title">รายการสำเนา (Copy)</div>
    <div class="actions">
        <a href="../../index.php">🏠 หน้าหลัก</a>
        <a href="add.php">➕ เพิ่มสำเนาใหม่</a>
    </div>
</div>

<div class="table-control">
    <input type="text" id="searchInput" placeholder="ค้นหาสำเนา..." onkeyup="filterTable()">
    <div style="display:flex;align-items:center;gap:5px;white-space:nowrap;">
        <label for="orderSelect" style="font-weight:500;color:#2c3e50;">เรียงตาม</label>
        <select id="orderSelect" onchange="orderTable()">
            <option value="copy_id">ID</option>
            <option value="series_name">ชื่อหนังสือ</option>
            <option value="shelf_name">ชั้นวาง</option>
            <option value="book_condition">สภาพหนังสือ</option>
            <option value="copy_status">สถานะ</option>
        </select>
    </div>
</div>

<div class="table-container">
<table id="copyTable">
<thead>
<tr>
<th>ID</th>
<th>ชื่อหนังสือ</th>
<th>หมายเลขชั้น</th>
<th>สภาพหนังสือ</th>
<th>สถานะ</th>
<th>วันที่ได้รับ</th>
<th>ราคา</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td data-label="ID"><?= $row['copy_id'] ?></td>
<td data-label="ชื่อหนังสือ"><?= htmlspecialchars($row['series_name']) ?></td>
<td data-label="หมายเลขชั้น"><?= htmlspecialchars($row['shelf_name']) ?></td>
<td data-label="สภาพหนังสือ"><?= htmlspecialchars($row['book_condition']) ?></td>
<td data-label="สถานะ"><?= htmlspecialchars($row['copy_status']) ?></td>
<td data-label="วันที่ได้รับ"><?= htmlspecialchars($row['acquired_date']) ?></td>
<td data-label="ราคา"><?= htmlspecialchars($row['purchase_price']) ?></td>
<td class="td-actions" data-label="Action">
<a href="edit.php?id=<?= $row['copy_id'] ?>">Edit</a>
<a href="delete.php?id=<?= $row['copy_id'] ?>" onclick="return confirm('ต้องการลบสำเนานี้หรือไม่?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<script>
function filterTable() {
    const filter=document.getElementById("searchInput").value.toLowerCase();
    const table=document.getElementById("copyTable");
    const trs=table.getElementsByTagName("tr");
    for(let i=1;i<trs.length;i++){
        const tds=trs[i].getElementsByTagName("td");
        let show=false;
        for(let j=0;j<tds.length-1;j++){
            if(tds[j].textContent.toLowerCase().includes(filter)){ show=true; break; }
        }
        trs[i].style.display=show?"":"none";
    }
}

function orderTable() {
    const table=document.getElementById("copyTable").tBodies[0];
    const rows=Array.from(table.rows);
    const key=document.getElementById("orderSelect").value;
    const colMap={
        copy_id:0, series_name:1, shelf_name:2, book_condition:3, copy_status:4
    };
    const colIndex=colMap[key] || 0;

    rows.sort((a,b)=>{
        let aText=a.cells[colIndex].textContent.toLowerCase();
        let bText=b.cells[colIndex].textContent.toLowerCase();
        if(!isNaN(aText) && !isNaN(bText)){ aText=parseFloat(aText); bText=parseFloat(bText); }
        return aText>bText?1:aText<bText?-1:0;
    });

    rows.forEach(r=>table.appendChild(r));
}
</script>

</body>
</html>
