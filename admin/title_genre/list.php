<?php
require_once "../../connect/connect.php";

$sql = "SELECT tg.title_id, tg.genre_id, t.series_name, g.genre_name
        FROM Title_Genre tg
        JOIN Title t ON tg.title_id = t.title_id
        JOIN Genre g ON tg.genre_id = g.genre_id";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Title-Genre List</title>
   <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            padding: 30px;
        }
        a { text-decoration: none; color: inherit; }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .toolbar .title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }
        .toolbar .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .btn:hover { background-color: #2980b9; }

        .back-button {
            display: inline-block;
            background-color: #ecf0f1;
            color: #2c3e50;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .back-button:hover { background-color: #d0d7de; }

        .table-container { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        thead { background: linear-gradient(135deg, #34495e, #2980b9); }
        thead th {
            color: white;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 16px;
            text-align: left;
        }
        tbody tr { border-bottom: 1px solid #e1e4e8; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:nth-child(even) { background-color: #f9fafe; }
        tbody tr:hover { background-color: #eef4fb; }
        tbody td { padding: 12px 16px; font-size: 14px; vertical-align: middle; }
        .td-actions { white-space: nowrap; }
        .td-actions a { margin-right: 8px; color: #3498db; font-weight: 500; }
        .td-actions a:hover { color: #1d6fa5; }

        @media (max-width: 768px) {
            .toolbar { flex-direction: column; align-items: flex-start; gap: 12px; }
            table, thead, tbody, th, td, tr { display: block; }
            thead { display: none; }
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
            .td-actions { text-align: right; }
        }
    </style>
</head>

<body>

    <div class="toolbar">
        <div class="title">Title Genre List</div>
        <div class="actions">
            <a href="../../index.php" class="back-button">🏠 กลับหน้าหลัก</a>
            <a href="add.php" class="btn">➕ เพิ่ม Mapping</a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ชื่อหนังสือ</th>
                    <th>ประเภทหนังสือ</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Title"><?= htmlspecialchars($row['series_name']) ?></td>
                        <td data-label="Genre"><?= htmlspecialchars($row['genre_name']) ?></td>
                        <td class="td-actions" data-label="Action">
                            <a href="edit.php?title_id=<?= $row['title_id'] ?>&genre_id=<?= $row['genre_id'] ?>">Edit</a>
                            <a href="delete.php?title_id=<?= $row['title_id'] ?>&genre_id=<?= $row['genre_id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>