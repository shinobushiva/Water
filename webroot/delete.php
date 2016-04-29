<?php
require_once("../bootstrap.php");

$post_time = getPostTime();

$now = time();


if($now < ($post_time + DELETE_LIMIT)){
    if (isset($_POST['del_flg'])) {

        // 削除ログ
        $sql = 'SELECT * FROM info WHERE time = ?';
        $params = [
            $post_time
        ];
        $row = DB::conn()->row($sql, $params);;
        logger($row, "delete_info");

        //  当該情報の削除
        $sql = "DELETE FROM info WHERE time = ?";
        $params = [
            $post_time
        ];

        DB::conn()->query($sql, $params);
        header('Location: index.php');

    } else {
        //  削除しようとしているデータが存在しているかをチェック
        $sql = 'SELECT * FROM info WHERE time = ?';
        $params = [
            $post_time
        ];
        $rows = DB::conn()->rows($sql, $params);
    }
    $json = json_safe_encode($rows);
    $json_size = strlen($json);
}

if (isset($json_size) && $json_size > 2) {
    $template = Template::factory();
    echo $template->render('delete.html', array(
        'post_time' => $post_time,
    ));
} else {
    echo "この情報は削除されました、または不正なリクエストです";
}

function logger($text, $title = "test")
{
    if (is_array($text)) {
        $text = json_encode($text);
    }

    $sql = "INSERT INTO logs SET title = :title, post_text = :post_text, post_date = :post_date";
    $params = [
        "title"    => $title ,
        "post_text"  => $text ,
        "post_date"     => date("Y-m-d H:i:s") ,
    ];

    DB::conn()->query($sql, $params);
}
