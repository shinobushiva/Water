<?php

require_once("../bootstrap.php");

$db = DB::conn();
$now = time();
$from_time = $now - (60 * 60 * 24 * 2);
$rows = $db->rows("SELECT * FROM info WHERE time > ? ORDER BY time ASC", [$from_time]);
$r_rows = $db->rows("SELECT * FROM rousui WHERE time > ? ORDER BY time ASC", [$from_time]);

if($rows[0]['time'] > $r_rows[0]['time']){
    $base_time = $r_rows[0]['time'];
}else {
    $base_time = $rows[0]['time'];
}

$from_time = strtotime(getDateRound(date("Y-m-d H:i:s", $base_time), 100, "floor"));
$now = strtotime(getDateRound(date("Y-m-d H:i:s", $now), 100, "ceil"));

$template = Template::factory();
echo $template->render('index.html', array(
    'from_time' => $from_time,
    'now' => $now,
));
