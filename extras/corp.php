<?php

$filename = $_POST['variant_id'].'.jpg';
$n = $_POST['n'];

$arr = [
    ['size' => 'small', 'width' => '80'],
    ['size' => 'medium', 'width' => '250'],
    ['size' => 'large', 'width' => '400']
];

foreach ($arr as $a) {
    create_image($n, $a['width'], $a['size'] );
}

echo json_encode([
    'success' => true,
    'filename' => $filename
]);

function create_image($n, $width, $size) {

    global $filename;

    $original = '/var/www/admin/www/stash.madwave.ru/images/original/pic_'.$n.'/' . $filename;

    if (!img_is_exist($original)) {
        return false;
    }

    $mew_file = '/var/www/admin/www/stash.madwave.ru/images/'.$size.'/pic_'.$n.'/'.$filename;

    list($width_orig, $height_orig) = getimagesize($original);
    $ratio_orig = $width_orig/$height_orig;
    $height = $width/$ratio_orig;

    $image_p = imagecreatetruecolor($width, $height);
    $image = imagecreatefromjpeg($original);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

    create_dir_if_is_does_not_exist($size, $n);


    imagejpeg($image_p, $mew_file);
    imagedestroy($image_p);
}

function img_is_exist($file) {
    $img_size = @getimagesize($file);

    if (empty($img_size)) {
        return false;
    }
    return true;
}

function create_dir_if_is_does_not_exist($size, $n) {
    $size_dir = '/var/www/admin/www/stash.madwave.ru/images/'.$size.'/pic_'.$n;
    if (!file_exists($size_dir)) {
        mkdir($size_dir, 0777, true);
    }
}