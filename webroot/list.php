<?php
require_once("../bootstrap.php");

$now = time();
$from_time = $now - (60 * 60 * 24 * 2);
$query = 'SELECT * FROM info';
$rows = DB::conn()->rows('SELECT * FROM info', []);
$r_rows = DB::conn()->rows('SELECT * FROM rousui', []);

$arr = array_merge($r_rows, $rows);

include VIEW_DIR. '/list.php';
