<?php

define("DELETE_LIMIT",  60 * 5); //削除リミット

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
