<?php

use frontend\modules\citydetector\CityDetector;
use frontend\modules\citydetector\widgets\CityPicker;

?>

<div id="sub-header">
    <div class="container">
        <?= CityPicker::widget() ?>
        <div class="right-side">
<!--            <a href="https://www.madwave.ru/stock?sort=-by_discount">ЛЕТНЯЯ РАСПРОДАЖА!</a>-->
            <span class="a">СКИДКА 3% ПРИ ОПЛАТЕ ОНЛАЙН!</span>
            <span class="pipe">|</span>
            ПОДРОБНЕЕ 8 800 100 57 19
            <span class="pipe">|</span>
            <span class="sosocial">
                <span>ПОДПИСЫВАЙСЯ</span>
                <a target="_blank" href="https://www.instagram.com/madwave_rus/"   class="fa fa-instagram"></a>
                <a target="_blank" href="https://www.facebook.com/madwavemax"   class="fa fa-facebook"></a>
                <a target="_blank" href="https://twitter.com/madwave"           class="fa fa-twitter"></a>
                <a target="_blank" href="https://www.youtube.com/c/madwaverus"  class="fa fa-youtube"></a>
                <a target="_blank" href="https://vk.com/madwaverus"             class="fa fa-vk"></a>
                <a target="_blank" href="https://plus.google.com/+MadWaveRus"   class="fa fa-google-plus"></a>
            </span>
        </div>
    </div>
</div>