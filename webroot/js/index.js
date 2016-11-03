var gmarkers = [];
var infoWindows = [];

var tools_height = document.getElementById('tools').clientHeight;
function attachMessage(marker, post_time, flg, comment, rousui_image_url, rousui_status) {
    google.maps.event.addListener(marker, 'click', function (event) {

        var t = new Date(post_time * 1000);

        var year = t.getFullYear();
        var month = t.getMonth() + 1;
        var hours = t.getHours();
        var date = t.getDate();
        // Minutes part from the timestamp
        var minutes = "0" + t.getMinutes();
        // Seconds part from the timestamp
        var seconds = "0" + t.getSeconds();

        // Will display time in 10:30:23 format
        var formattedTime = year + "年" + month + "月" + date + "日" + hours + '時' + minutes.substr(-2) + '分' + seconds.substr(-2) + "秒";

        var flg_str = "";
        if (flg == "no") {
            flg_str = '<img src="no.png" > 水が出ない';
        } else if (flg == "ok") {
            flg_str = '<img src="ok.png" > 水が出る';
        } else if (flg == "go") {
            flg_str = '<img src="go.png" > 水の提供可能';
        } else if (flg == "notdrink") {
            flg_str = '<img src="notdrink.png" > 水出るが飲めない';
        } else if (flg == "rousui" && rousui_status == 1) {
            flg_str = '<img src="resolve.png" > 解決済み';
        } else if (flg == "rousui") {
            flg_str = '<img src="rousui.png" > 水漏れ';
        }

        var comment_str = "";

        if(comment != "null"){
            comment_str = comment;
        }

        var resolve_str = "";
        // 漏水の画像があるなら表示
        if(flg == "rousui" && rousui_status != 1){
            resolve_str = "<br>" + "<a href='' onclick='document.resolve.submit();return false;'>解決済みにする</a>" + "<form name='resolve' method='GET' action='resolve.php'>" + "<input type=hidden name='post_time' value='" + post_time +"'> ";
        }

        var rousui_img = "";
        // 漏水の画像があるなら表示
        if(flg == "rousui" && rousui_image_url !== "" && rousui_image_url !== null && rousui_image_url != "undefined"){
            rousui_img = "<br>" + "<a href='" + rousui_image_url + "' target='_blank'><img src='" + rousui_image_url + "' width='200' alt='' ></a>";
        }

        var now = new Date();
        var del_str = "";
        //5分以内なら削除可能
        if(flg != "rousui" && parseInt(now.getTime() / 1000) < (parseInt(post_time) + (60 * 5))){
            del_str = "<br><br>" + "<a href='' onclick='document.del.submit();return false;'>この情報を削除する</a>" + "<form name='del' method='GET' action='delete.php'>" + "<input type=hidden name='post_time' value='" + post_time +"'> ";
        }

        new google.maps.Geocoder().geocode({
            latLng: marker.getPosition()
        }, function (result, status) {
            if (status == google.maps.GeocoderStatus.OK) {

                closeAllInfoWindows();

                var ifw = new google.maps.InfoWindow({
                    content: "<div class='infowin'>" + formattedTime + "<br>" + flg_str + " " + comment_str + "<br>" + result[0].formatted_address + resolve_str + rousui_img + del_str + "</div>"
                });

                ifw.open(marker.getMap(), marker);

                infoWindows.push(ifw);
            }
        });
    });
}

function closeAllInfoWindows() {
    for (var i = 0; i < infoWindows.length; i++) {
        infoWindows[i].close();
    }
}

function plotNews(t_news) {
    for (var i = 0; i < t_news.length; i++) {
        $("#breaking_news").prepend('<div class="item"><a href="' + t_news[i]['url'] + '" target="_blank">' + t_news[i]['title'] + '</a></div>');
    }

    if(1 < t_news.length){
        $(".owl-carousel").owlCarousel({
            items:1,
            loop:true,
            margin:0,
            autoplay:true,
            autoplayTimeout:3000,
            autoplayHoverPause:true
        });
    }else{
        $(".owl-carousel").show();
    }
}
function plotData(t_position) {
    // index 3 (marker 3) not exist
    var markers = ['no', 'ok', 'go', 'notdrink', 'rousui'];

    var m = document.getElementById('map');
    window.DEFAULT_LAT = 35.3362854;
    window.DEFAULT_LNG = 133.5450567;
    window.DEFAULT_ZOOM = 9;

    // 変更が加えられた際は SessionStorage から読み込む
    var data   = JSON.parse( sessionStorage.getItem( 'google-map-post-location' ) ),
        center = new google.maps.LatLng(data ? data.lat : window.DEFAULT_LAT, data ? data.lng : window.DEFAULT_LNG ),
        zoom   = data ? data.zoom : window.DEFAULT_ZOOM
    
    var map = new google.maps.Map(m, {
        center: center,
        zoom: zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    m.style.width = window.innerWidth + 'px';
    m.style.height = window.innerHeight - (tools_height) - 65 + 'px';

    // イベント発生時にストレージに保存
    // マップの情報を保存
    function setStorage(){
        var currentCenter = map.getCenter();
        var ss = {
            lat: currentCenter.lat() || window.DEFAULT_LAT,
            lng: currentCenter.lng() ||window.DEFAULT_LNG,
            zoom: map.getZoom() || window.DEFAULT_ZOOM
        };
        sessionStorage.setItem('google-map-post-location', JSON.stringify(ss));
        return ss
    }

    google.maps.event.addListener(map, 'zoom_changed', function() {
        setStorage()
    });

    var setStorageDom = document.getElementsByClassName( 'post-button' )
    for( var i=0; i<setStorageDom.length; i++ ){
        setStorageDom[i].addEventListener( 'click', function(){
            setStorage()
            console.log('google-map-post-location', sessionStorage.getItem('google-map-post-location'));
        } )
    }

    // document.getElementById('small').addEventListener('click', function () {
    //     if (map.zoom > 0) map.setZoom(--map.zoom)
    //     setStorage()
    // });
    //
    // document.getElementById('big').addEventListener('click', function () {
    //     map.setZoom(++map.zoom)
    //     setStorage()
    // });

    removeMarkers();

    var data;
    var no_count = 0, ok_count = 0, go_count = 0, notdrink_count = 0, rousui_count = 0;
    for (var i = 0; i < t_position.length; i++) {
        data = t_position[i]['locate'].split(/,/)
        post_time = t_position[i]['time'];
        comment = t_position[i]['comment'];
        rousui_image_url = t_position[i]['image_url'];
        rousui_status = t_position[i]['status'];

        if (t_position[i]['flg'] == 0) {
            no_count++;
        } else if (t_position[i]['flg'] == 1) {
            ok_count++;
        } else if (t_position[i]['flg'] == 2) {
            go_count++;
        } else if (t_position[i]['flg'] == 3) {
            notdrink_count++;
        } else if (t_position[i]['flg'] == 4) {
            rousui_count++;
        }

        var icon = markers[t_position[i].flg];

        if(t_position[i]['flg'] == 4 && rousui_status == 1){
            icon = "resolve";
        }

        var myMarker = new google.maps.Marker({
            position: new google.maps.LatLng(data[0], data[1]),
            map: map,
            icon: icon + '.png'
        });
        gmarkers.push(myMarker);
        attachMessage(myMarker, post_time, markers[t_position[i].flg], comment, rousui_image_url, rousui_status);
    }
    $("#no_count").text("(" + no_count + ")");
    $("#ok_count").text("(" + ok_count + ")");
    $("#go_count").text("(" + go_count + ")");
    $("#notdrink_count").text("(" + notdrink_count + ")");
    $("#rousui_count").text("(" + rousui_count + ")");
}

function loadNews(){

    $.ajax({
            url: 'news_api.php',
            type: 'get', // getかpostを指定(デフォルトは前者)
            dataType: 'json', // 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
        })
        // ・ステータスコードは正常で、dataTypeで定義したようにパース出来たとき
        .done(function (response) {
            plotNews(response);
        })
        // ・サーバからステータスコード400以上が返ってきたとき
        // ・ステータスコードは正常だが、dataTypeで定義したようにパース出来なかったとき
        // ・通信に失敗したとき
        .fail(function () {
        });
}

function loadData(start){

    var end = $('#end').val();
    if(!start){
        start = $('#start').val();
    }
    var map_flg;
    map_flg = $('[name="water_flg"]:checked').map(function(){
        return $(this).val()
    }).get().join(',');

    $.ajax({
            url: 'api.php',
            type: 'get', // getかpostを指定(デフォルトは前者)
            dataType: 'json', // 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
            data: { // 送信データを指定(getの場合は自動的にurlの後ろにクエリとして付加される)
                map_start: start,
                map_end: end,
                map_flg: map_flg
            }
        })
        // ・ステータスコードは正常で、dataTypeで定義したようにパース出来たとき
        .done(function (response) {
            // console.log(response);
            // console.log(response.length);
            plotData(response);
        })
        // ・サーバからステータスコード400以上が返ってきたとき
        // ・ステータスコードは正常だが、dataTypeで定義したようにパース出来なかったとき
        // ・通信に失敗したとき
        .fail(function () {
        });
}

function removeMarkers(){
    for(i=0; i<gmarkers.length; i++){
        gmarkers[i].setMap(null);
    }
}

/**
 * 日付をフォーマットする
 * @param  {Date}   date     日付
 * @param  {String} [format] フォーマット
 * @return {String}          フォーマット済み日付
 */
var formatDate = function (date, format) {
    if (!format) format = 'YYYY-MM-DD hh:mm:ss.SSS';
    format = format.replace(/YYYY/g, date.getFullYear());
    format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
    format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));
    format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));
    format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
    format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
    if (format.match(/S/g)) {
        var milliSeconds = ('00' + date.getMilliseconds()).slice(-3);
        var length = format.match(/S/g).length;
        for (var i = 0; i < length; i++) format = format.replace(/S/, milliSeconds.substring(i, i + 1));
    }
    return format;
};

// DOMを全て読み込んだあとに実行される
$(function () {

    var from_time = parseInt($("#start").val(), 10);
    var now = parseInt($("#end").val(), 10);
    var default_begin = now - 60 * 60 * 6;

    $("#slider-range").slider({
        range: true,
        min: from_time,
        max: now,
        step: 1800,
        values: [default_begin, now],
        slide: function (event, ui) {
            $("#start").val(ui.values[0]);
            $("#end").val(ui.values[1]);
            $("#amount").val(formatDate(new Date(ui.values[0] * 1000), "MM月DD日hh時mm分") + " - " + formatDate(new Date(ui.values[1] * 1000), "MM月DD日hh時mm分"));
        },
        stop: function( event, ui ) {
            loadData();
        }
    });
    $("#amount").val((formatDate(new Date($("#slider-range").slider("values", 0) * 1000), "MM月DD日hh時mm分")) +
        " - " + (formatDate(new Date($("#slider-range").slider("values", 1) * 1000), "MM月DD日hh時mm分")));

    loadData(default_begin);
    loadNews();

    $("#start").val(default_begin);

    $('[name=water_flg]').change(function() {
        loadData();
    });

});
