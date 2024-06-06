<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UID照会</title>
</head>
<body>
    <form method="post">
        <label for="uid">UID:</label>
        <input type="text" id="uid" name="uid" required>
        <button type="submit">照会</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $uid = htmlspecialchars($_POST["uid"]);
        $apiUrl = "https://enka.network/api/uid/". $uid;

        // cURLを使ってAPIリクエストを送信
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($response === FALSE) {
            die('エラー: APIリクエストが失敗しました。');
        }
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data["avatarInfoList"])) {
            $equipTypes = ["EQUIP_BRACER", "EQUIP_NECKLACE", "EQUIP_SHOES", "EQUIP_RING", "EQUIP_DRESS"];
            $results = [];

            foreach ($data["avatarInfoList"] as $avatar) {
                foreach ($avatar["equipList"] as $equip) {
                    if (in_array($equip["flat"]["equipType"], $equipTypes)) {
                        $mainPropId = $equip["flat"]["reliquaryMainstat"]["mainPropId"];
                        $statValue = $equip["flat"]["reliquaryMainstat"]["statValue"];
                        $results[] = [
                            "equipType" => $equip["flat"]["equipType"],
                            "mainPropId" => $mainPropId,
                            "statValue" => $statValue
                        ];
                    }
                }
            }

            // 結果を表示
            if (!empty($results)) {
                echo "<h2>装備情報:</h2>";
                echo "<table border='1'>";
                echo "<tr><th>Equip Type</th><th>Main Prop ID</th><th>Stat Value</th></tr>";

                foreach ($results as $result) {
                    echo "<tr><td>{$result['equipType']}</td><td>{$result['mainPropId']}</td><td>{$result['statValue']}</td></tr>";
                }

                echo "</table>";
            } else {
                echo "<p>該当する装備が見つかりませんでした。</p>";
            }
        } else {
            echo "<p>データが見つかりませんでした。</p><br>";
            echo var_dump($response);
        }
    }
    ?>
</body>
</html>
