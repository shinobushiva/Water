<?php
require_once("../bootstrap.php");
if (isset($_POST['submit'])) {
    $err = "";

    $time = time();
    $locate = (string)$_POST['locate'];
    $comment = (string)$_POST['comment'];
    if (IsLocateString($locate) == false) {
        $err = "経度緯度情報が不正です";
    }

    if ($err === "") {
        $image_url = "";
        if (isset($_FILES["image"]) && is_uploaded_file($_FILES["image"]["tmp_name"])) {
            // アップロード処理
            $file = $_FILES["image"];

//            if (!is_uploaded_file($file["tmp_name"])) {
//                die('ファイルがアップロードされていません');
//            }

            if (!IsImage($file)) {
                die('画像はJPEG(jpg,jpeg)、GIF(gif)、PNG(png)のいずれかとなっております。');
            }
            $savePath = safeImage($file["tmp_name"], TMP_DIR);
            if ($savePath === "") {
                die("不正な画像がuploadされました");
            }

            $result = s3Upload($savePath, '');

            // 書きだした画像を削除
            @unlink($savePath);

            if ($result) {
                $image_url = $result['ObjectURL'];
            }
        }

        //if ($image_url !== "") {

        $query = array(
            "latlng" => h($locate),
            "language" => "ja",
            "sensor" => false
        );
        $res = callApi("GET", "https://maps.googleapis.com/maps/api/geocode/json", $query);

        $address = $res["results"][0]["formatted_address"];

        $sql = "INSERT INTO rousui SET time = :time, locate = :locate, comment = :comment, image_url = :image_url, address = :address, flg = :flg";
        $params = ["time" => $time,
            "locate" => $locate,
            "comment" => $comment,
            "image_url" => $image_url,
            "address" => $address,
            "flg" => 4,
        ];

        DB::conn()->query($sql, $params);
        header('Location: index.php');
        //}
    }
    echo $err . PHP_EOL;
}


$template = Template::factory();
echo $template->render('rousui_post.html', array(
    'php_self' => $_SERVER['PHP_SELF'],
));
