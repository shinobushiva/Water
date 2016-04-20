<?php
require_once("../bootstrap.php");

$query = 'select * from water_leak';
$arr = DB::conn()->rows($query, []);


foreach ($arr as $a) {

    $sql = "INSERT INTO rousui SET time = :time, locate = :locate, comment = :comment, address = :address, flg = :flg";

    if (empty($a["address"])) {
        $query = array(
            "address" => $a["address1"] . $a["address2"] . $a["address3"],
            "language" => "ja",
        );
        $res = callApi("GET", "https://maps.googleapis.com/maps/api/geocode/json", $query);

        $address = $res["results"][0]["formatted_address"];
        $lat = $res["results"][0]["geometry"]["location"]["lat"];
        $lng = $res["results"][0]["geometry"]["location"]["lng"];

        $params = [
            "time" => strtotime($a["date"]),
            "locate" => $lat . "," . $lat,
            "address" => $address,
            "flg" => 4,
            "comment" => $a["note"],
        ];
    } else {
        $params = [
            "time" => strtotime($a["date"]),
            "locate" => $a["latitude"] . "," . $a["longitude"],
            "address" => $a["address"],
            "flg" => 4,
            "comment" => $a["note"],
        ];
    }
    DB::conn()->query($sql , $params);
}


function callApi($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result, true);
}
