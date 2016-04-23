(function () {
    'use strict';

    document.getElementById('small').addEventListener('click', function () {
        if (map.zoom > 0) map.setZoom(--map.zoom)
    });

    document.getElementById('big').addEventListener('click', function () {
        map.setZoom(++map.zoom)
    });
    
// 前画面で保存したデータを削除
    $('#js-submit-button').click(function (e) {
        setStorage()
    });
    
    var markers = ['no', 'ok', 'go', 'notdrink'];
    var marker = 0 ;// Selected marker
    if (!navigator.geolocation)
        document.getElementById('now').style.display = 'none';
    var mapDom = document.getElementById('map');

    var data   = JSON.parse( sessionStorage.getItem( 'google-map-post-location' ) ),
        center = new google.maps.LatLng(data ? data.lat : 32.7858659, data ? data.lng : 130.7633434 ),
        zoom   = data ? data.zoom : 9

    var map = new google.maps.Map(mapDom, {
        center: center,
        zoom: zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    mapDom.style.width = window.innerWidth + 'px';
    mapDom.style.height = window.innerHeight - (document.getElementById('post').clientHeight) - 80 + 'px';

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

    var elem = document.getElementById('time');

// Create time now
//     n    = new Date()
//
// // Create time now
// var month   = n.getMonth() + 1,
//     hours   = n.getHours(),
//     day     = n.getDate(),
//     minutes = n.getMinutes()
//
// month   = month.toString().length > 1 ? month : '0' + month
// hours   = hours.toString().length > 1 ? hours : '0' + hours
// day     = day.toString().length > 1 ? day : '0' + day
// minutes = minutes.toString().length > 1 ? minutes : '0' + minutes
    elem.value = ''+ Math.round(Date.now()/1000);//'16' + month + hours + minutes
    console.log(elem.value);
    var nowPosition;

    map.addListener('click', mapClickListener);

    function mapClickListener (e) {
        var latlng = e.latLng;
        // Get status
        marker = Number(document.getElementById('flg').value);
        // console.log( 'set position : ', e )
        if (nowPosition) nowPosition.setMap(null);
        document.getElementById('locate').value = latlng.lat() + ',' + latlng.lng();

        nowPosition = createMapMarker(latlng, map, markers[marker]);

        setTimeout(function () {
            alert('間違いがなければ "投稿" ボタンをクリックしてください．');
        }, 100);
    }

    // 現在位置を設定
    window.now = function () {
        navigator.geolocation.getCurrentPosition(function (position) {
                var data = position.coords;
                var lat  = data.latitude;
                var lng  = data.longitude;
                document.getElementById('locate').value = lat + ',' + lng;
                var latlng = new google.maps.LatLng(lat, lng);

                marker = Number(document.getElementById('flg').value);

                if (nowPosition) nowPosition.setMap(null);
                nowPosition = createMapMarker(latlng, map, markers[marker]);

                setTimeout(function () {
                    alert('間違いがなければ "投稿" ボタンをクリックしてください．');
                }, 100);

            },
            function (error) {
                var errMsg;
                switch (error.code) {
                    case 1:
                        errMsg = "位置情報の利用が許可されていません．設定から位置情報の使用を許可してください．";
                        break;
                    case 2:
                        errMsg = "デバイスの位置が判定できません．";
                        break;
                    case 3:
                        errMsg = "タイムアウトしました．";
                        break;
                }
                if (navigator.userAgent.match(/FBAN/)) {
                    errMsg = "Facebookアプリでは位置情報が取得できませんので、タイムラインのリンクを長押しし外部のブラウザで起動して下さい。";
                }
                alert("位置情報の取得に失敗しました．" + errMsg);
            }
        )
    };

    function createMapMarker(latlng, map, icon) {
        return new google.maps.Marker({
            position : latlng,
            map      : map,
            icon     : icon + '.png'
        });
    }

    function updateValue() {
        if (!nowPosition) return;
        var n1 = nowPosition.position.lat();
        var n2 = nowPosition.position.lng();
        marker = Number(document.getElementById('flg').value);
        nowPosition.setMap(null);
        nowPosition = new google.maps.Marker({
            position: new google.maps.LatLng(n1, n2),
            map: map,
            icon: markers[marker] + '.png'
        });
        return true;
    }
})();
