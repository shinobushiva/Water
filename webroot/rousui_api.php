<?php
require_once("../bootstrap.php");

$end = $_GET['map_end'];
$from_time = $_GET['map_start'];
$flgs = explode(",", $_GET["map_flg"]);

// format
$end = strtotime(date("Y-m-d H:i:s", (int)$end));
$from_time = strtotime(date("Y-m-d H:i:s", (int)$from_time));

$sql = "SELECT * FROM rousui WHERE time > ? AND time < ?";
$params = [
    $from_time,
    $end
];

if (count($flgs) > 0 && ($flgs[0]) != "") {
    $sql .= " AND flg IN(?)";
    $f = [];
    foreach ($flgs as $flg) {
        $f[] = VerifyFlag($flg);
    }
    $params[] = $f;
} else {
    $sql .= " AND flg NOT IN(0,1,2,3,4)";
}

$rows = DB::conn()->rows($sql, $params);

$json = json_safe_encode($rows);

echo $json;
