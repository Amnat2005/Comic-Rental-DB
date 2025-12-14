<?php
require_once "../../connect/connect.php";

$result = $conn->query("SELECT * FROM Customer");
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Customer List</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            padding: 30px;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Toolbar */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            gap: 10px;
        }

        .toolbar .title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }

        .toolbar .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.2s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .back-button {
            display: inline-block;
            background-color: #ecf0f1;
            color: #2c3e50;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.2s;
        }

        .back-button:hover {
            background-color: #d0d7de;
        }

        /* Table control */
        .table-control {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
            flex-wrap: wrap;
        }

        #searchInput {
            flex-grow: 1;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
        }

        #searchInput:focus {
            border-color: #3498db;
            box-shadow: 0 2px 6px rgba(52, 152, 219, 0.3);
            outline: none;
        }

        .order-container {
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .order-container label {
            font-weight: 500;
            color: #2c3e50;
        }

        #orderSelect {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            min-width: 120px;
            transition: 0.2s;
        }

        #orderSelect:hover,
        #orderSelect:focus {
            border-color: #3498db;
            box-shadow: 0 2px 4px rgba(52, 152, 219, 0.3);
            outline: none;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        thead {
            background: linear-gradient(135deg, #34495e, #2980b9);
        }

        thead th {
            color: white;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 16px;
            text-align: left;
        }

        tbody tr {
            border-bottom: 1px solid #e1e4e8;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafe;
        }

        tbody tr:hover {
            background-color: #eef4fb;
        }

        tbody td {
            padding: 12px 16px;
            font-size: 14px;
            vertical-align: middle;
        }

        .td-actions {
            white-space: nowrap;
        }

        .td-actions a {
            margin-right: 8px;
            color: #3498db;
            font-weight: 500;
        }

        .td-actions a:hover {
            color: #1d6fa5;
        }

        @media (max-width:768px) {
            .toolbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .table-control {
                flex-direction: column;
                align-items: stretch;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead {
                display: none;
            }

            tbody tr {
                margin-bottom: 16px;
                display: block;
                border: 1px solid #e1e4e8;
                border-radius: 6px;
            }

            tbody td {
                display: flex;
                justify-content: space-between;
                padding: 10px 12px;
            }

            tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #555;
            }

            .td-actions {
                text-align: right;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar">
        <div class="title">รายการลูกค้า (Customer)</div>
        <div class="actions">
            <a href="../../index.php" class="back-button">🏠 กลับหน้าหลัก</a>
            <a href="add.php" class="btn">➕ เพิ่มลูกค้าใหม่</a>
        </div>
    </div>

    <!-- Search + Order -->
    <div class="table-control">
        <input type="text" id="searchInput" placeholder="ค้นหาลูกค้า..." onkeyup="filterTable()">
        <div class="order-container">
            <label for="orderSelect">เรียงตาม</label>
            <select id="orderSelect" onchange="orderTable()">
                <option value="customer_id">ID</option>
                <option value="first_name">ชื่อ</option>
                <option value="phone">เบอร์โทร</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table id="customerTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อ-สกุล</th>
                    <th>เบอร์โทร</th>
                    <th>อีเมล</th>
                    <th>ที่อยู่</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td data-label="ID"><?= htmlspecialchars($row['customer_id']) ?></td>
                        <td data-label="ชื่อ-สกุล"><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?></td>
                        <td data-label="เบอร์โทร"><?= htmlspecialchars($row['phone']) ?></td>
                        <td data-label="อีเมล"><?= htmlspecialchars($row['email']) ?></td>
                        <td data-label="ที่อยู่"><?= htmlspecialchars($row['address']) ?></td>
                        <td class="td-actions" data-label="จัดการ">
                            <a href="edit.php?id=<?= $row['customer_id'] ?>">Edit</a>
                            <a href="delete.php?id=<?= $row['customer_id'] ?>" onclick="return confirm('Delete this customer?')">Delete</a>
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
            const table = document.getElementById("customerTable");
            const trs = table.getElementsByTagName("tr");
            for (let i = 1; i < trs.length; i++) {
                const tds = trs[i].getElementsByTagName("td");
                let show = false;
                for (let j = 0; j < tds.length - 1; j++) {
                    if (tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        show = true;
                        break;
                    }
                }
                trs[i].style.display = show ? "" : "none";
            }
        }

        function orderTable() {
            const table = document.getElementById("customerTable").tBodies[0];
            const rows = Array.from(table.rows);
            const key = document.getElementById("orderSelect").value;

            rows.sort((a, b) => {
                let aText = "",
                    bText = "";
                if (key === "customer_id") {
                    aText = parseInt(a.cells[0].textContent);
                    bText = parseInt(b.cells[0].textContent);
                } else if (key === "first_name") {
                    aText = a.cells[1].textContent.toLowerCase();
                    bText = b.cells[1].textContent.toLowerCase();
                } else if (key === "phone") {
                    aText = a.cells[2].textContent.toLowerCase();
                    bText = b.cells[2].textContent.toLowerCase();
                }
                return aText > bText ? 1 : aText < bText ? -1 : 0;
            });
            rows.forEach(row => table.appendChild(row));
        }
    </script>

</body>

</html>