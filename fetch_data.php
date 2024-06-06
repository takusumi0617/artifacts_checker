<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = htmlspecialchars($_POST['uid']);
    $url = "https://enka.network/api/uid/$uid";

    // cURLを使ってAPIからデータを取得
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // JSONデータを配列に変換
    $data = json_decode($response, true);

    // 指定された装備タイプ
    $equipTypes = ["EQUIP_BRACER", "EQUIP_NECKLACE", "EQUIP_SHOES", "EQUIP_RING", "EQUIP_DRESS"];
    
    echo "<h1>装備情報</h1>";
    echo "<table border='1'>";
    echo "<tr><th>Equip Type</th><th>Main Prop ID</th><th>Stat Value</th></tr>";

    foreach ($data['avatarInfoList'] as $avatar) {
        if (isset($avatar['equipList'])) {
            foreach ($avatar['equipList'] as $equip) {
                if (in_array($equip['flat']['equipType'], $equipTypes)) {
                    $equipType = $equip['flat']['equipType'];
                    $mainPropId = $equip['flat']['reliquaryMainstat']['mainPropId'];
                    $statValue = $equip['flat']['reliquaryMainstat']['statValue'];
                    echo "<tr><td>$equipType</td><td>$mainPropId</td><td>$statValue</td></tr>";
                }
            }
        }
    }
    echo "</table>";
}
?>