<?php

use common\components\multilocal\Local;
use yii\helpers\Html;

$suggestions_ru = [
    'Очки для плавания',
    'Ласты',
    'Бутылка',
    'Купальник',
    'Маска',
    'Мешок',
    'Шейкер',
    'Тренажер',
    'Полотенце',
    'Плеер',
    'Секундомер',
];

$suggestions_eu = [
    'Swimming goggles',
    'Fins',
    'Bottle',
    'Swimsuit',
    'Mask',
    'Bag',
    'Shaker',
    'Trainer',
    'Towel',
    'Player',
    'Stopwatch',
];
$suggestions = Local::choose([
    Local::RU => $suggestions_ru,
    Local::EU => $suggestions_eu
])
?>

<h3> <?= Yii::t('app', 'Suggestions') ?> </h3>
<ul id="search-suggestions">
    <?php foreach ($suggestions as $s) {
        echo Html::tag('li', $s, ['class' => 'a']);
    } ?>
</ul>