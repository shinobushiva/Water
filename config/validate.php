<?php

/**
 * 送信されたflgの正当性を検証
 *  異常値の場合は、0(水が出ない)を返す
 * @param $flag
 *
 * @return int
 */
function VerifyFlag($flag) {
    $i = (int)$flag;
    if( 0 <= $i && $i <= 4 ){
        return $i;
    }
    return 0;
}


/**
 * 経度緯度の文字列を検証する
 * TODO:範囲指定は特になし
 * @param $locate
 *
 * @return bool
 */
function IsLocateString($locate) {
    if (preg_match("/\d+\.\d+,\d+\.\d+/", $locate) === 1) {
        return true;
    }
    return false;
}


/**
 * URLとして妥当かチェックする
 * @param url
 *
 * @return boolean
 */
function IsUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
