<?php
require_once("../bootstrap.php");

$now = time();
$from_time = $now - (60 * 60 * 24 * 2);
$query = 'select * from info where time > ? order by time desc';
$arr = DB::conn()->rows($query, [$from_time]);


foreach($arr as $a){

    if(empty($a["address"])){
        $query = array(
            "latlng" => $a["locate"],
            "language" => "ja",
            "sensor" => false
        );
        $res = callApi("GET", "https://maps.googleapis.com/maps/api/geocode/json", $query);

        $address= (string)$res["results"][0]["formatted_address"];

        $query = "UPDATE info SET address = ? WHERE Id = ?;";

        DB::conn()->query($query, [$address,$a["Id"]]);
    }
}

