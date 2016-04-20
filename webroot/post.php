<?php
require_once("../bootstrap.php");

if( isset( $_POST['submit'] ) ){
    $err = "";

    $time    = time();
    $flg     = VerifyFlag($_POST['flg']);
    $locate  = (string)$_POST['locate'];
    $comment = (string)$_POST['comment'];
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

$template = Template::factory();
echo $template->render('post.html', array(
    'php_self' => $_SERVER['PHP_SELF'],
));
