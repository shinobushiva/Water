<?php
require_once("../bootstrap.php");

if( isset( $_POST['submit'] ) ){
    $err = "";

    $time    = time();
    $flg     = VerifyFlag($_POST['flg']);
    $locate  = $_POST['locate'];
    $comment = $_POST['comment'];
    if (IsLocateString($locate) == false) {
        $err = "経度緯度情報が不正です";
    }

    if ($err === "") {

        $query = array(
            "latlng" => h($locate),
            "language" => "ja",
            "sensor" => false
        );
        $res = callApi("GET", "https://maps.googleapis.com/maps/api/geocode/json", $query);

        $address= $res["results"][0]["formatted_address"];

        $sql = "INSERT INTO info SET time = :time, locate = :locate, flg = :flg, comment = :comment, address = :address";
        $params = ["time"    => $time ,
            "locate"  => $locate ,
            "flg"     => (int)$flg ,
            "comment" => $comment ,
            "address" => $address,
        ];

        DB::conn()->query($sql , $params);
        header('Location: index.php');
    }
    echo $err .PHP_EOL;
}

function callApi($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
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

$template = Template::factory();
echo $template->render('post.html', array(
    'php_self' => $_SERVER['PHP_SELF'],
));
