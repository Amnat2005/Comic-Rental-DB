<?php
require_once "../../connect/connect.php";

// ดึง Publisher, Author, Genre สำหรับ dropdown/checkbox
$publishers = $conn->query("SELECT publisher_id, publisher_name FROM Publisher");
$authors = $conn->query("SELECT author_id, author_name FROM Author");
$genres = $conn->query("SELECT genre_id, genre_name FROM Genre");

if (isset($_POST['submit'])) {
    $publisher_id = $_POST['publisher_id'];
    $series_name = $_POST['series_name'];
    $book_number = $_POST['book_number'];
    $release_year = $_POST['release_year'];
    $author_ids = $_POST['author_ids'] ?? []; // array
    $genre_ids = $_POST['genre_ids'] ?? []; // array

    // Insert Title
    $stmt = $conn->prepare("INSERT INTO Title (publisher_id, series_name, book_number, release_year) VALUES (?,?,?,?)");
    $stmt->bind_param("isii", $publisher_id, $series_name, $book_number, $release_year);
    $stmt->execute();
    $title_id = $stmt->insert_id; // id ของ Title ใหม่
    $stmt->close();

    // Insert Title_Author mapping
    $stmt2 = $conn->prepare("INSERT INTO Title_Author (title_id, author_id) VALUES (?,?)");
    foreach ($author_ids as $aid) {
        $stmt2->bind_param("ii", $title_id, $aid);
        $stmt2->execute();
    }
    $stmt2->close();

    // Insert Title_Genre mapping
    $stmt3 = $conn->prepare("INSERT INTO Title_Genre (title_id, genre_id) VALUES (?,?)");
    foreach ($genre_ids as $gid) {
        $stmt3->bind_param("ii", $title_id, $gid);
        $stmt3->execute();
    }
    $stmt3->close();

    header("Location: list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เพิ่ม Title</title>
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
            background: #ffffff;
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
            transition: background-color 0.2s;
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

        .checkbox-group label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="toolbar">
            <div class="title">เพิ่ม Title</div>
            <a href="list.php" class="btn">กลับรายการ Title</a>
        </div>

        <form method="post">

            <label for="series_name">ชื่อหนังสือ</label>
            <input type="text" name="series_name" id="series_name" required>

            <label for="book_number">เล่มที่</label>
            <input type="number" name="book_number" id="book_number" required>

            <label for="publisher_id">สำนักพิมพ์</label>
            <select name="publisher_id" id="publisher_id" required>
                <?php while ($p = $publishers->fetch_assoc()): ?>
                    <option value="<?= $p['publisher_id'] ?>"><?= htmlspecialchars($p['publisher_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="release_year">ปีที่พิมพ์</label>
            <select name="release_year" id="release_year" required>
                <?php
                $currentYear = date("Y");
                $startYear = 1980; // ตั้งแต่ปีไหนก็ได้
                for ($y = $currentYear; $y >= $startYear; $y--) {
                    echo "<option value='$y'>$y</option>";
                }
                ?>
            </select>

            <div class="checkbox-group">
                <label>ประเภท:</label>
                <?php while ($g = $genres->fetch_assoc()): ?>
                    <label><input type="checkbox" name="genre_ids[]" value="<?= $g['genre_id'] ?>"> <?= htmlspecialchars($g['genre_name']) ?></label>
                <?php endwhile; ?>
            </div>

            <div class="checkbox-group">
                <label>ผู้แต่ง:</label>
                <?php while ($a = $authors->fetch_assoc()): ?>
                    <label><input type="checkbox" name="author_ids[]" value="<?= $a['author_id'] ?>"> <?= htmlspecialchars($a['author_name']) ?></label>
                <?php endwhile; ?>
            </div>

            <input type="submit" name="submit" value="เพิ่ม Title">
        </form>
    </div>

</body>

</html>