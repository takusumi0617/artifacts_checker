<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>装備情報照会</title>
</head>
<body>
    <h1>装備情報照会</h1>
    <form action="fetch_data.php" method="post">
        <label for="uid">UID:</label>
        <input type="text" id="uid" name="uid" required>
        <button type="submit">照会</button>
    </form>
</body>
</html>