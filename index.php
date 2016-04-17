<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>Watermap</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-script-type" content="text/javascript">
    <link rel="stylesheet" href="base.css">
    <style>
        body {
            font-family: sans-serif
        }
        
        #postBtn {
            width: 100%;
            height: 45px;
            background: #fff;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            line-height: 34pt;
            color: #000;
        }
        
        .btn {
            width: 50%;
            height: 40px;
            font-size: 20px;
            font-weight: bold;
            background: rgba(240, 240, 240, 1);
            line-height: 28pt;
            text-align: center;
        }
        
        .memo {
            font-size: 10px
        }
    </style>
</head>

<body>

    <div id="tools">
       水道から水が出ていますか？
        <a href="post.php">
            <div id="postBtn">投稿する</div>
        </a>

        <span class="memo">
                <img src="no.png"> 水が出ない<br>
                <img src="ok.png"> 水は出る<br>
                <img src="go.png"> 水の提供可能
            </span>
            <br><br>
        <div id="customZoomBtn">
            <div id="small" class="float_l btn">ズームアウト</div>
            <div id="big" class="float_l btn">ズームイン</div>
        </div>
    </div>

    <!-- View map -->
    <div id="map"></div>

    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script>
        var map,
            // index 3 (marker 3) not exist
            markers = ['no', 'ok', 'go', 'go']

        var m = document.getElementById('map')
        m.style.width = window.innerWidth + 'px'
        m.style.height = window.innerHeight - (document.getElementById('tools').clientHeight) + 'px'

        map = new google.maps.Map(m, {
            center: new google.maps.LatLng(32.7858659, 130.7633434),
            zoom: 9,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        })

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

        var data
        for (var i = 0; i < position.length - 1; i++) {
            data = position[i]['locate'].split(/,/)
            console.log(position[i].flg)
            new google.maps.Marker({
                position: new google.maps.LatLng(data[0], data[1]),
                map: map,
                icon: markers[position[i].flg] + '.png'
            })
        }

        document.getElementById('small').addEventListener('click', function () {
            if (map.zoom > 0) map.setZoom(--map.zoom)
        })

        document.getElementById('big').addEventListener('click', function () {
            map.setZoom(++map.zoom)
        })
    </script>

</body>

</html>