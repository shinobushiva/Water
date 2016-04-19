(function () {
    'use strict';

    document.getElementById('small').addEventListener('click', function () {
        if (map.zoom > 0) map.setZoom(--map.zoom)
    });

    document.getElementById('big').addEventListener('click', function () {
        map.setZoom(++map.zoom)
    });

    document.getElementById("image").addEventListener("change", function(e){
        e.target.nextSibling.nodeValue = e.target.files.length ? e.target.files[0].name : "写真を選ぶ";
    });
    
// 前画面で保存したデータを削除
    $('#js-submit-button').click(function (e) {
        setStorage()
    });

    if (!navigator.geolocation)
        document.getElementById('now').style.display = 'none';
    var mapDom = document.getElementById('map');

    var currentMap; // 前の画面から表示データを取得する
    try {
        if (!!sessionStorage.getItem('google-map-post-location')) {
            currentMap = JSON.parse(sessionStorage.getItem('google-map-post-location'));
        } else {
            currentMap = {};
        }
    } catch (e) {
        console.error(e);
    }

    var map = new google.maps.Map(mapDom, {
        center: new google.maps.LatLng(currentMap.lat || 32.7858659, currentMap.lng || 130.7633434),
        zoom: currentMap.zoom || 9,
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

    elem.value = ''+ Math.round(Date.now()/1000);//'16' + month + hours + minutes
    console.log(elem.value);
    var nowPosition;

    map.addListener('click', mapClickListener);

    function mapClickListener (e) {
        var latlng = e.latLng;
        // Get status
        // console.log( 'set position : ', e )
        if (nowPosition) nowPosition.setMap(null);
        document.getElementById('locate').value = latlng.lat() + ',' + latlng.lng();
        nowPosition = createMapMarker(latlng, map, "rousui");

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

                if (nowPosition) nowPosition.setMap(null);
                nowPosition = createMapMarker(latlng, map, "rousui");

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
        nowPosition.setMap(null);
        nowPosition = new google.maps.Marker({
            position: new google.maps.LatLng(n1, n2),
            map: map,
            icon: 'rousui.png'
        });
        return true;
    }
})();
