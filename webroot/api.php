<?php
require_once("../bootstrap.php");

$end = $_GET['map_end'];
$from_time = $_GET['map_start'];
$flgs = explode(",", $_GET["map_flg"]);

// format
$end = strtotime(date("Y-m-d H:i:s", (int)$end));
$from_time = strtotime(date("Y-m-d H:i:s", (int)$from_time));


$params = [
    $from_time,
    $end
];
$r_rows = [];
$rows =[];
if (count($flgs) > 0 && ($flgs[0]) != "") {

    // rousui table
    //if(in_array(4, $flgs)){
        $sql = "SELECT * FROM rousui";
        $r_rows = DB::conn()->rows($sql, $params);
    //}

    // info table
    $sql = "SELECT * FROM info WHERE time > ? AND time < ? ";
    $sql .= " AND flg IN(?)";
    $sql .= " ORDER BY time DESC LIMIT 1000";
    $f = [];
    foreach ($flgs as $flg) {
        $f[] = VerifyFlag($flg);
    }
    $params[] = $f;

    $rows = DB::conn()->rows($sql, $params);
}

$result = array_merge($r_rows, $rows);

$json = json_safe_encode($result);

echo $json;
