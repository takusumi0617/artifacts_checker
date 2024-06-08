<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>UID照会</title>
    <style>
        table {
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
    </style>
</head>
<body>
    <form method="post">
        <label for="uid">UID:</label>
        <input type="text" id="uid" name="uid" required>
        <button type="submit">照会</button>
    </form>

    <?php
    function translate($key) {
        $translations = json_decode(file_get_contents("loc.json"), true);
        return isset($translations["ja"][$key]) ? $translations["ja"][$key] : $key;
    }
    function chara_name($key) {
        $translations = json_decode(file_get_contents("characters.json"), true);
        return isset($translations[$key]["NameTextMapHash"]) ? $translations[$key]["NameTextMapHash"] : $key;
    }
    function chara_url($key) {
        $translations = json_decode(file_get_contents("characters.json"), true);
        return isset($translations[$key]["SideIconName"]) ? $translations[$key]["SideIconName"] : $key;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $uid = htmlspecialchars($_POST["uid"]);
        $apiUrl = "https://enka.network/api/uid/". $uid;
        $headers = array(
            'Content-type: application/json; charset=UTF-8',
            'Accept: application/json',
            'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)'
        );

        // cURLを使ってAPIリクエストを送信
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if ($response === FALSE) {
            die('エラー: APIリクエストが失敗しました。');
        }
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data["avatarInfoList"])) {
            $equipTypes = ["EQUIP_BRACER", "EQUIP_NECKLACE", "EQUIP_SHOES", "EQUIP_RING", "EQUIP_DRESS"];
            
            echo "<h2>装備情報:</h2>";
            echo "<table>";
            echo "<tr><th>キャラクター名</th><th>装備タイプ</th><th>メインステータス</th><th>ステータス値</th></tr>";

            $previousCharacterName = null;
            $rowspan = 0;

            foreach ($data["avatarInfoList"] as $avatar) {
                $characterName = translate(chara_name($avatar["avatarId"]));
                $characterpicurl = "https://enka.network/ui/" . chara_url($avatar["avatarId"]) . ".png";
                $equipRows = [];

                foreach ($avatar["equipList"] as $equip) {
                    if (isset($equip["flat"]["equipType"]) && in_array($equip["flat"]["equipType"], $equipTypes)) {
                        $equipType = translate($equip["flat"]["equipType"]);
                        $mainPropId = translate($equip["flat"]["reliquaryMainstat"]["mainPropId"]);
                        $statValue = $equip["flat"]["reliquaryMainstat"]["statValue"];
                        
                        // "パーセンテージ"または"率"が含まれている場合に"%"を付ける
                        if (strpos($mainPropId, "パーセンテージ") !== false || strpos($mainPropId, "率") !== false) {
                            $statValue .= "%";
                        }

                        $equipRows[] = "<td>{$equipType}</td><td>{$mainPropId}</td><td>{$statValue}</td></tr>";
                    }
                }

                if ($characterName === $previousCharacterName) {
                    $rowspan++;
                } else {
                    $rowspan = count($equipRows);
                    $previousCharacterName = $characterName;
                }

                foreach ($equipRows as $index => $row) {
                    if ($index === 0) {
                        echo "<tr><td rowspan='{$rowspan}'>{$characterName}<br><img src='{$characterpicurl}'/></td>{$row}";
                    } else {
                        echo "<tr>{$row}";
                    }
                }
            }
            echo "</table>";
        } else {
            echo "<p>データが見つかりませんでした。</p>";
        }
    }
    ?>
</body>
</html>
