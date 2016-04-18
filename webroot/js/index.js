var data_source = $('#map').attr('data-source');
var position = JSON.parse(data_source);

var gmarkers = [];
var infoWindows = [];

var tools_height = document.getElementById('tools').clientHeight;
function attachMessage(marker, post_time, flg, comment) {

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
    }

    var comment_str = "";

    if(comment != "null"){
        comment_str = comment;
    }

    var pos = marker.getPosition();
    $('#comments').append('<a href="javascript:clickComment(' + pos.lat() + ',' + pos.lng() + ');">' + formattedTime + ' ' + flg_str + ' ' + comment_str + '</a><br/>');

    google.maps.event.addListener(marker, 'click', function (event) {

        new google.maps.Geocoder().geocode({
            latLng: marker.getPosition()
        }, function (result, status) {
            if (status == google.maps.GeocoderStatus.OK) {

                closeAllInfoWindows();
                var ifw = new google.maps.InfoWindow({
                    content: "<div class='infowin'>" + formattedTime + "<br>" + flg_str + " " + comment_str + "<br>" + result[2].formatted_address + "</div>"
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
    var markers = ['no', 'ok', 'go', 'go'];

    var m = document.getElementById('map');
    window.DEFAULT_LAT = 32.7858659;
    window.DEFAULT_LNG = 130.7633434;
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

    $('#js-post-button').click(function (event) {
        setStorage()
        console.log('google-map-post-location', sessionStorage.getItem('google-map-post-location'));
    });

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
    $('#comments').empty();

    var data;
    var no_count = 0, ok_count = 0, go_count = 0;
    for (var i = 0; i < t_position.length; i++) {
        data = t_position[i]['locate'].split(/,/)
        post_time = t_position[i]['time'];
        comment = t_position[i]['comment'];

        if (t_position[i]['flg'] == 0) {
            no_count++;
        } else if (t_position[i]['flg'] == 1) {
            ok_count++;
        } else if (t_position[i]['flg'] == 2) {
            go_count++;
        }

        var myMarker = new google.maps.Marker({
            position: new google.maps.LatLng(data[0], data[1]),
            map: map,
            icon: markers[t_position[i].flg] + '.png'
        });
        gmarkers.push(myMarker);
        attachMessage(myMarker, post_time, markers[t_position[i].flg], comment);
    }
    $("#no_count").text("(" + no_count + ")");
    $("#ok_count").text("(" + ok_count + ")");
    $("#go_count").text("(" + go_count + ")");
}
// DOMを全て読み込んだあとに実行される
$(function () {

    plotData(position);


    // var map_flg;
    // $('[name=water_flg]').change(function() {
    //     map_flg = $('[name="water_flg"]:checked').map(function(){
    //         return 'map_flg[]=' + $(this).val()
    //     }).get().join('&');
    // });

    loadData();
    loadNews();

    $('[name=water_flg]').change(function() {
        loadData();
    });
});


function loadNews(){

    $.ajax({
            url: 'news_api.php',
            type: 'get', // getかpostを指定(デフォルトは前者)
            dataType: 'json', // 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
        })
        // ・ステータスコードは正常で、dataTypeで定義したようにパース出来たとき
        .done(function (response) {
            console.log(response);
            plotNews(response);
        })
        // ・サーバからステータスコード400以上が返ってきたとき
        // ・ステータスコードは正常だが、dataTypeで定義したようにパース出来なかったとき
        // ・通信に失敗したとき
        .fail(function () {
        });
}

function loadData(){

    var map_flg;
    map_flg = $('[name="water_flg"]:checked').map(function(){
        return $(this).val()
    }).get().join(',');

    $.ajax({
            url: 'api.php',
            type: 'get', // getかpostを指定(デフォルトは前者)
            dataType: 'json', // 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
            data: { // 送信データを指定(getの場合は自動的にurlの後ろにクエリとして付加される)
                map_start: $('#start').val(),
                map_end: $('#end').val(),
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
