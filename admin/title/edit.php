<?php
require_once "../../connect/connect.php";

if (!isset($_GET['id'])) {
    die("Error: Title ID not specified.");
}

$id = (int)$_GET['id'];

// ดึงข้อมูล Title
$title = $conn->query("SELECT * FROM Title WHERE title_id=$id")->fetch_assoc();
if (!$title) {
    die("Error: Title not found.");
}

// ดึง Publisher, Author, Genre สำหรับเลือก
$publishers = $conn->query("SELECT publisher_id, publisher_name FROM Publisher");
$authors = $conn->query("SELECT author_id, author_name FROM Author");
$genres = $conn->query("SELECT genre_id, genre_name FROM Genre");

// ดึง Author ของ Title นี้
$existing_authors = [];
$res = $conn->query("SELECT author_id FROM Title_Author WHERE title_id=$id");
while ($row = $res->fetch_assoc()) $existing_authors[] = $row['author_id'];

// ดึง Genre ของ Title นี้
$existing_genres = [];
$res2 = $conn->query("SELECT genre_id FROM Title_Genre WHERE title_id=$id");
while ($row2 = $res2->fetch_assoc()) $existing_genres[] = $row2['genre_id'];

$message = "";
if (isset($_POST['submit'])) {
    $publisher_id = $_POST['publisher_id'];
    $series_name = $_POST['series_name'];
    $book_number = $_POST['book_number'];
    $release_year = $_POST['release_year'];
    $author_ids = $_POST['author_ids'] ?? [];
    $genre_ids = $_POST['genre_ids'] ?? [];

    // Update Title
    $stmt = $conn->prepare("UPDATE Title SET publisher_id=?, series_name=?, book_number=?, release_year=? WHERE title_id=?");
    $stmt->bind_param("isiii", $publisher_id, $series_name, $book_number, $release_year, $id);

    if ($stmt->execute()) {
        // Update Title_Author
        $conn->query("DELETE FROM Title_Author WHERE title_id=$id");
        if (!empty($author_ids)) {
            $stmt2 = $conn->prepare("INSERT INTO Title_Author (title_id, author_id) VALUES (?,?)");
            foreach ($author_ids as $aid) {
                $stmt2->bind_param("ii", $id, $aid);
                $stmt2->execute();
            }
            $stmt2->close();
        }

        // Update Title_Genre
        $conn->query("DELETE FROM Title_Genre WHERE title_id=$id");
        if (!empty($genre_ids)) {
            $stmt3 = $conn->prepare("INSERT INTO Title_Genre (title_id, genre_id) VALUES (?,?)");
            foreach ($genre_ids as $gid) {
                $stmt3->bind_param("ii", $id, $gid);
                $stmt3->execute();
            }
            $stmt3->close();
        }

        $message = "✅ แก้ไขข้อมูล Title สำเร็จแล้ว";
    } else {
        $message = "❌ เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>แก้ไข Title</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            color: white;
            background-color: #3498db;
            transition: 0.2s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #2c3e50;
        }

        form select,
        form input[type="text"],
        form input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        form input[type="submit"] {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        form input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .checkbox-group {
            margin-bottom: 30px;
            /* เดิม 20px -> เพิ่มเป็น 30px ให้มีพื้นที่ด้านล่าง */
        }

        .checkbox-item {
            margin-left: 20px;
            font-weight: normal;
            margin-bottom: 6px;
            /* เว้นระยะระหว่าง checkbox แต่ละตัว */
        }

        .message {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 6px;
            background-color: #eef4fb;
            color: #2c3e50;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="toolbar">
            <div class="title">แก้ไข Title</div>
            <a href="list.php" class="btn">กลับหน้ารายการ Title</a>
        </div>

        <?php if ($message) { ?>
            <div class="message"><?= $message ?></div>
        <?php } ?>

        <form method="post">
            <label for="series_name">ชื่อหนังสือ</label>
            <input type="text" name="series_name" id="series_name" value="<?= htmlspecialchars($title['series_name']) ?>" required>

            <label for="book_number">เล่มที่</label>
            <input type="number" name="book_number" id="book_number" value="<?= $title['book_number'] ?>" required>

            <label for="publisher_id">สำนักพิมพ์</label>
            <select name="publisher_id" id="publisher_id" required>
                <?php while ($p = $publishers->fetch_assoc()): ?>
                    <option value="<?= $p['publisher_id'] ?>" <?= $p['publisher_id'] == $title['publisher_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['publisher_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="release_year">ปีที่พิมพ์</label>
            <select name="release_year" id="release_year" required>
                <?php
                $currentYear = date("Y");
                $startYear = 1980;
                for ($y = $currentYear; $y >= $startYear; $y--) {
                    $selected = $y == $title['release_year'] ? "selected" : "";
                    echo "<option value='$y' $selected>$y</option>";
                }
                ?>
            </select>

            <div class="checkbox-group">
                <label>ประเภท:</label>
                <?php foreach ($genres->fetch_all(MYSQLI_ASSOC) as $g): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="genre_ids[]" value="<?= $g['genre_id'] ?>" <?= in_array($g['genre_id'], $existing_genres) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($g['genre_name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="checkbox-group">
                <label>ผู้แต่ง:</label>
                <?php foreach ($authors->fetch_all(MYSQLI_ASSOC) as $a): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="author_ids[]" value="<?= $a['author_id'] ?>" <?= in_array($a['author_id'], $existing_authors) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($a['author_name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <input type="submit" name="submit" value="บันทึกการแก้ไข">
        </form>
    </div>

</body>

</html>