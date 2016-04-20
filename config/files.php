<?php

/**
 * 一度画像を別の形式に変換し、jpg形式に変換する
 * TODO: @utsumi-k PHP Parserを使うかは場合は別途用意する
 * @param $orgFilePath
 * @param $exportFilePath
 *
 * @return string
 */
function safeImage($orgFilePath, $exportFilePath) {
    // 書き出しファイル名を生成
    $outputFilePath = $exportFilePath . "/" . generateUniqueFileName();

    // 元画像情報を取得
    $size = getimagesize($orgFilePath);
    if ($size === false) {
        // 画像として認識できなかった
        return "";
    }
    list($w, $h, $type) = $size;
    if ($type == IMG_JPEG) {
        // JPEG の場合は一度pngへ変換します。
        $jpg = imagecreatefromjpeg($orgFilePath);
        if (!imagepng($jpg, $outputFilePath . ".png")) {
            // pngに出来なかった
            return "";
        }
        $res = imagecreatefrompng($outputFilePath . ".png");
        // リソース取得したので、png画像は削除
        @unlink($outputFilePath . ".png");
    } elseif ($type === IMG_PNG) {
        $res = imagecreatefrompng($orgFilePath);
    } elseif ($type == IMG_GIF) {
        $res = imagecreatefromgif($orgFilePath);
    } else {
        return "";
    }

    if ($res === false) {
        // 画像作れなかった
        return "";
    }

    // 問題なかったのでjpgにしましょう。
    list($width, $height) = getSaveFileSize($w, $h);
    $jpg = ImageCreateTrueColor($width, $height);
    if (!imagecopyresized($jpg, $res, 0, 0, 0, 0, $width, $height, $w, $h)) {
        // リサイズに失敗
        return "";
    }
    if (!imagejpeg($jpg, $outputFilePath)) {
        // 最後の書き出しで失敗
        return "";
    }

    return $outputFilePath;
}

/**
 * Uniqueなファイル名を生成する
 * TODO: 絶対かぶらない保証もないし、非同期で保存されるけど、今んとこそこは危惧するレベルではないと判断
 * @return string
 */
function generateUniqueFileName() {
    return md5(uniqid(rand(),1)) . ".jpg";
}


/**
 * 保存するサイズを計算する
 * 960*540 までのサイズにする
 * @param $orgWidth
 * @param $orgHeight
 *
 * @return array
 */
function getSaveFileSize($orgWidth, $orgHeight) {
    $maxWidth = 960;
    $maxHeight = 540;
    $w = $orgWidth;
    $h = $orgHeight;

    if ($orgWidth > $maxWidth || $orgHeight > $maxHeight) {
        // リサイズ必要
        if ($orgWidth > $orgHeight) {
            // 横長
            $rate = $maxWidth / $orgWidth;

        } elseif ($orgHeight > $orgWidth) {
            // 縦長
            $rate = $maxHeight / $orgHeight;
        } else {
            // 正方形
            $rate = $maxHeight / $orgHeight;
        }
        $w = (int)($orgWidth * $rate);
        $h = (int)($orgHeight * $rate);
    }
    return [$w,$h];
}
