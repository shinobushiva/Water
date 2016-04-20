<?php

define("DELETE_LIMIT",  60 * 5); //削除リミット


use Aws\S3\S3Client;

// 拡張子を取得する
function extension($filename) {
    $str = strrchr($filename, '.');
    if ($str === FALSE) {
        return NULL;
    } else {
        return substr($str, 1);
    }
}

function s3Upload($file, $s3Dir) {

    $ext = extension($file);
    $srcPath = $file;

    $timestamp = uniqid();
    $name = $timestamp . "_file." . $ext;

    $s3 = S3Client::factory(
        array(
            'key'    => getenv('AWS_BUCKET_KEY'),// 取得したAccess Key IDを使用
            'secret' => getenv('AWS_BUCKET_SECRET'),// 取得した Secret Access Keyを使用
            'region' => getenv('AWS_BUCKET_REGION'),
            'version' => getenv('AWS_BUCKET_VERSION'),
        )
    );

    try {
        // Upload a file.
        $result = $s3->putObject(array(
            'Bucket'       => getenv('AWS_BUCKET_NAME'),
            'Key'        => $name,
            'SourceFile' => $srcPath,
            'ACL'          => 'public-read',
        ));

        return $result;
    }catch (RuntimeException $e){
        return false;
    }

}

/**
 * 安全なJSONにエンコードする
 * @param $data
 *
 * @return string
 */
function json_safe_encode($data) {
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}



/**
 *
 * 日時の切り上げ/切捨て/四捨五入
 *
 * @param string $date
 * @param integer $place 1|10|100|1000|10000|100000
 * @param string $math ceil|floor|round
 * @return string
 */
function getDateRound($date, $place, $math) {
    list($Y, $m, $d, $H, $i, $s) = explode("-", date('Y-m-d-H-i-s', strtotime($date)));

    // 秒（1で1の位、10で秒全体）
    if ($place == 1) {
        $s = $math($s * 0.1) * 10;
    } elseif ($place > 1) {
        $s = $math($s * 0.01) * 100;
        if ($s > 60) {
            $s = 60;
        }
    }

    if ($place > 10) {
        $date = date('Y-m-d-H-i-s', mktime($H, $i, $s, $m, $d, $Y));
        list($Y, $m, $d, $H, $i, $s) = explode("-", $date);
    }
    // 分（100で1の位、1000で分全体）
    if ($place == 100) {
        $i = $math($i * 0.1) * 10;
    } elseif ($place > 100) {
        $i = $math($i * 0.01) * 100;
        if ($i > 60) {
            $i = 60;
        }
    }

    if ($place > 1000) {
        $date = date('Y-m-d-H-i-s', mktime($H, $i, $s, $m, $d, $Y));
        list($Y, $m, $d, $H, $i, $s) = explode("-", $date);
    }
    // 時間（10000で1の位、100000で時間全体）
    if ($place == 10000) {
        $H = $math($H * 0.1) * 10;
    } elseif ($place > 10000) {
        $H = $math($H * 0.01) * 100;
        if ($H > 24) {
            $H = 24;
        }
    }

    return date('Y-m-d H:i:s', mktime($H, $i, $s, $m, $d, $Y));
}

/**
 *
 * HTML特殊文字エスケープのエイリアス
 *
 * @param string $str
 * @return string
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 *
 * CurlでAPIを叩きます。
 * @param            $method
 * @param            $url
 * @param bool|false $data
 *
 * @return mixed
 */
function callApi($method, $url, $data = false){
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result, true);
}
