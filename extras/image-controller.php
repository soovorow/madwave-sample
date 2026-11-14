<?
    if (md5($_POST['password']) == md5('mwImageLord123')) {
        setcookie("crazymfcka", md5('mwImageLord123'));
    }
?>

<? if (md5('mwImageLord123') !== $_COOKIE['crazymfcka']) { ?>

    <h1>Hello, now you can manage product images, just enter your password at fild below</h1>
    <form action="/image-controller.php" method="post">
        <input type="text" name="password">
        <input type="submit" value="enter">
    </form>

<? } else { ?>

    <h1>Welcome. There you can manage product images.</h1>

    <form action="/image-controller.php" method="post">
        <label for="">Enter SKU to view original images</label><br>
        <input type="text" name="showSkuImages" value="<?= $_POST['showSkuImages'] ?>">
        <input type="submit" value="show">
    </form>

    <? if ($_POST['showSkuImages']) {

        $sku = $_POST['showSkuImages'];
        $skuLen = strlen($sku);
        if ($skuLen != 8) {
            $zero_count = 8 - $skuLen;
            for ($i = 0; $i < $zero_count; $i ++) {
                $arr[] = 0;
            }
            $filename = implode('', $arr).$sku;
        } else {
            $filename = $sku;
        }

        if (isset($_FILES)) {

            $arr = [
                1 => 'image1',
                2 => 'image2',
                3 => 'image3',
                4 => 'image4',
            ];



            foreach ($arr as $n => $img) {

                if ($_FILES[$img]['size'] > 100) {
                    if (
                        move_uploaded_file(
                            $_FILES['image1']['tmp_name'],
                            sprintf(
                                '/var/www/admin/www/stash.madwave.ru/images/original/pic_'.$n.'/'.$filename.'.jpg',
                                sha1_file($_FILES['image1']['tmp_name']),
                                'jpg'
                            )
                        )
                    ) {
                        unlink('/var/www/admin/www/stash.madwave.ru/images/small/pic_' . $n . '/' . $filename . '.jpg');
                        unlink('/var/www/admin/www/stash.madwave.ru/images/medium/pic_' . $n . '/' . $filename . '.jpg');
                        unlink('/var/www/admin/www/stash.madwave.ru/images/large/pic_' . $n . '/' . $filename . '.jpg');
                    };
                }
            }
        }
        ?>

        <br>
        <br>

        <form action="/image-controller.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="showSkuImages" value="<?= $sku ?>">
            <div style="width: 100%;">
                <div style="width: 10%; float: left; margin-right: 50px">
                    <img style="width: 100%" src="<?= 'http://stash.madwave.ru/images/original/pic_1/'.$filename.'.jpg' ?>" alt="">
                    <input type="file" name="image1">
                </div>
                <div style="width: 10%; float: left; margin-right: 50px">
                    <img style="width: 100%" src="<?= 'http://stash.madwave.ru/images/original/pic_2/'.$filename.'.jpg' ?>" alt="">
                    <input type="file" name="image2">
                </div>
                <div style="width: 10%; float: left; margin-right: 50px">
                    <img style="width: 100%" src="<?= 'http://stash.madwave.ru/images/original/pic_3/'.$filename.'.jpg' ?>" alt="">
                    <input type="file" name="image3">
                </div>
                <div style="width: 10%; float: left; margin-right: 50px">
                    <img style="width: 100%" src="<?= 'http://stash.madwave.ru/images/original/pic_4/'.$filename.'.jpg' ?>" alt="">
                    <input type="file" name="image4">
                </div>
                <div style="clear: both"></div>
            </div>
            <br>
            <br>
            <input type="submit" value="upload">
        </form>


    <? } ?>

<? } ?>

