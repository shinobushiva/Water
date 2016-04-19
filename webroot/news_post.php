<?php
require_once("../bootstrap.php");

if( isset( $_POST['submit'] ) ){

    $title = $_POST['title'];
    $url = $_POST['url'];

    if( IsUrl($url) ) {

        error_log("Post:".$title.",".$url);

        $err = '不正な値が入力された可能性があります．投稿に失敗しました．';

        if( $title != '' && $url != ''){

            $sql = "INSERT INTO news SET title = :title, url = :url";
            $params = ["title"    => $title ,
                "url"  => $url ,
            ];

            DB::conn()->query($sql , $params);
            header('Location: index.php');

        }
    }
    echo $err .PHP_EOL;
}

include VIEW_DIR . '/news_post.php';
