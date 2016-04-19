<!DOCTYPE html>
<html lang="ja">

<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#  website: http://ogp.me/ns/website#">
    <meta charset="utf-8">
    <title>MIZUDERU.INFO</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="keywords" content="熊本地震,給水,みずでる,水道">
    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-script-type" content="text/javascript">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/index.css">
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
<div id="tools">
    <div class="flex-container select-button-box">
        <a href="rousui_post.php" id="js-post-button" class="post-button">
            水漏れがありますか？<br>報告する
        </a>
        <a href="post.php" id="js-post-button" class="post-button">
            水が出ていますか？<br>投稿する
        </a>
    </div>

    <div class="memo">
        <br>
        <img src="no.png"> 水が出ない&nbsp;
        <img src="ok.png"> 水が出る&nbsp;
        <img src="go.png"> 水の提供可能&nbsp;
        <img src="notdrink.png"> 水出るが飲めない&nbsp;&nbsp;&nbsp;
        <img src="rousui.png"> 水漏れ&nbsp;&nbsp;&nbsp;
        <button id="range-toggle">絞り込み</button>
    </div>
    <div id="filter_options" class="memo" style="display:none">
        <div id="water_filter">
            <input name="water_flg" type="checkbox" value="0" checked><img src="no.png"> 水が出ない <span id="no_count"></span><br />
            <input name="water_flg" type="checkbox" value="1" checked><img src="ok.png"> 水が出る <span id="ok_count"></span><br />
            <input name="water_flg" type="checkbox" value="2" checked><img src="go.png"> 水の提供可能 <span id="go_count"></span><br />
            <input name="water_flg" type="checkbox" value="3" checked><img src="notdrink.png"> 水出るが飲めない <span id="notdrink_count"></span><br />
            <input name="water_flg" type="checkbox" value="4" checked><img src="rousui.png"> 水漏れ <span id="rousui_count"></span><br />
        </div>
        <div id="time-range">
            <p>
                <input type="text" id="amount" style="border: 0; color: #f6931f; font-weight: bold;" size="100"/>
            </p>
            <input type="hidden" id="start" value="<?php echo $from_time; ?>" >
            <input type="hidden" id="end" value="<?php echo $now + 1800; ?>" >
            <div id="slider-range"></div>
        </div>
    </div>
</div>

<!-- View map -->
<div id="map"></div>
<!--<div id="customZoomBtn">-->
<!--    <div id="small" class="float_l btn">ズームアウト</div>-->
<!--    <div id="big" class="float_l btn">ズームイン</div>-->
<!--</div>-->
<!-- Set up your HTML -->
<div id="breaking_news" class="owl-carousel"></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="js/jquery.ui.touch-punch.min.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="js/index.js"></script>
<script>
    $( "#range-toggle" ).click(function() {
        $( "#filter_options" ).toggle( "fold", 1000 );
    });
</script>
</body>

</html>
