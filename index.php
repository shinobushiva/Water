<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>Watermap</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-script-type" content="text/javascript">
    <link rel="stylesheet" href="public/css/base.css">
    <link rel="stylesheet" href="public/css/app.css">
</head>

<body>

    <div id="tools">
        <div style="margin:15px 0;text-align:center">
            水道から水が出ていますか？
        </div>
        <a href="post.php">
            <div id="postBtn">投稿する</div>
        </a>

        <span class="memo">
               <br>
                <img src="public/image/no.png"> 水が出ない<br>
                <img src="public/image/ok.png"> 水は出る<br>
                <img src="public/image/go.png"> 水の提供可能
            </span>
        <br>
        <br>
        <div id="customZoomBtn">
            <div id="small" class="float_l btn">ズームアウト</div>
            <div id="big" class="float_l btn">ズームイン</div>
        </div>
    </div>

    <!-- View map -->
    <div id="map"></div>

    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="public/js/app.js"></script>
    <script>
        var map;

        // index 3 (marker 3) not exist
        var markers = ['no', 'ok', 'go', 'go'];

        var m = document.getElementById('map');

        map = new google.maps.Map(m, {
            center: new google.maps.LatLng(32.7858659, 130.7633434),
            zoom: 9,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        m.style.width = window.innerWidth + 'px';
        m.style.height = window.innerHeight - (document.getElementById('tools').clientHeight) + 'px'

        // Set Data
        var position = <?php

                date_default_timezone_set( 'asia/tokyo' );
                require_once( 'dbconnect.php' );

                $connect = open_db();

                mysqli_query( $connect, 'SET NAMES utf8' );
                mysqli_set_charset( $connect, 'utf8' );

                mysqli_select_db( $connect, '' );

                $res = mysqli_query( $connect, 'select * from info where time>16'. (date('m')-1). date('d') .'00' );
                $json = '[';
                while( $data = mysqli_fetch_array( $res ) ){
                    $json = $json. '{locate:"'. $data['locate']. '",time:'. $data['time'] .',flg:'. $data['flg']. '},';
                }
                $json = $json. '{}]';

                echo $json;

                mysqli_close( $connect );

                ?>

        // console.log( position ) // => [{Data},{Data}...,{}]

        var data;
        for (var i = 0; i < position.length - 1; i++) {
            data = position[i]['locate'].split(/,/)
            console.log(position[i].flg)
            new google.maps.Marker({
                position: new google.maps.LatLng(data[0], data[1]),
                map: map,
                icon: 'public/image/' + markers[position[i].flg] + '.png'
            })
        }

        document.getElementById('small').addEventListener('click', function () {
            if (map.zoom > 0) map.setZoom(--map.zoom)
        });

        document.getElementById('big').addEventListener('click', function () {
            map.setZoom(++map.zoom)
        });
    </script>

</body>

</html>