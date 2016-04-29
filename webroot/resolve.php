<?php
require_once("../bootstrap.php");

$post_time = getPostTime();


$now = time();

if (isset($_POST['resolve_flg'])) {

    // 解決済みログ
    $sql = 'SELECT * FROM rousui WHERE time = ?';
    $params = [
        $post_time
    ];
    $row = DB::conn()->row($sql, $params);;
    logger($row, "resolve_info");

    //  当該情報の更新
    $sql = "UPDATE rousui SET status=:status WHERE time = :time";
    $params = [
        "status" => 1,
        "time" => $post_time,
    ];

    DB::conn()->query($sql, $params);
    header('Location: index.php');

} else {
    //  削除しようとしているデータが存在しているかをチェック
    $sql = 'SELECT * FROM rousui WHERE time = :time AND status != :status';
    $params = [
        "time" => $post_time,
        "status" => 1,
    ];
    $rows = DB::conn()->rows($sql, $params);
}
$json = json_safe_encode($rows);
$json_size = strlen($json);

if (isset($json_size) && $json_size > 2) {
    $template = Template::factory();
    echo $template->render('resolve.html', array(
        'post_time' => $post_time,
    ));
} else {
    echo "この情報はすでに解決済みです、または不正なリクエストです";
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
