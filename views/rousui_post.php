<!DOCTYPE html>
<html lang="ja">

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#  website: http://ogp.me/ns/website#">
    <meta charset="utf-8">
    <title>MIZUDERU.INFO</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="keywords" content="熊本地震,給水,みずでる,水道">
    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-script-type" content="text/javascript">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/post.css">

    <!-- OGP -->
    <meta property="og:type" content="website">
    <meta property="og:description" content="熊本県内の給水情報です。新しく情報を登録したいときは、画面上の「投稿する」をクリックします。そうすると画面が切り替わります。 ここで選択肢から「水が出ない」「水が出る」「水の提供可能」の３つからどれか選んで地図状にその位置をクリックすることで地点の設定ができます。 スマホなど現在地を取得できる機器であれば「現在地を設定」で今の位置を設定できます。 最後に「投稿」ボタンを押せが地図上にその情報が表示されます。">
    <meta property="og:title" content="熊本地震：熊本給水マップ Wartermap">
    <meta property="og:url" content="http://mizuderu.info/">
    <meta property="og:image" content="http://mizuderu.info/Watermap.png">
    <meta property="og:site_name" content="Watermap KUMAMOTO">
    <meta property="og:locale" content="ja_JP" />
    <meta property="fb:admins" content="661927574">
    <!-- OGP -->
</head>

<body>
<?php include_once("analyticstracking.php") ?>
<div style="margin:15px 0;text-align:center">
    水漏れを報告してください
</div>
<form action="<?php print($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data" method="POST" id="post" onsubmit="return confirm('送信してもいいですか？');">
    <input type="hidden" id="time" name="time" value="">
    <input type="hidden" name="locate" id="locate" value="">

    <div id='now' class="box">
        <div class="memo-title">
            <a href="javascript:void(0)" onclick="now()">１．位置を設定する</a>
        </div>
        <div class="memo">本体の設定から位置情報の利用を許可してください．</div>
    </div>
    <div class="box">
        <div class="memo-title">２．水漏れ箇所の情報を入力する</div>
        <textarea id="comment" name="comment"></textarea>
    </div>
    <div class="box">
        <div class="memo-title">３．写真があれば添付してください</div>
        <label class="button-file">
            <input type="file" class="hide" id="image" name="image" value="">
            写真を選ぶ
        </label>
    </div>
    <div class="box-mini">
        <input type="submit" id="js-submit-button" name="submit" value="投稿">
    </div>
    <div class="patchworks">
        <span class="memo">sojo univ. patchworks</span>
    </div>
</form>

<div id="map"></div>
<div id="customZoomBtn">
    <div id="small" class="float_l btn">ズームアウト</div>
    <div id="big" class="float_l btn">ズームイン</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="js/rousui_post.js"></script>

</body>

</html>