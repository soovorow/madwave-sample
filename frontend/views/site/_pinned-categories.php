<?php

use \common\components\multilocal\Local;
use yii\helpers\Url;

$categories = [
    [
        'href' => Local::choose([
            Local::RU => Url::to('/catalog/inventar-dlya-plavaniya', true),
            Local::EU => Url::to('/catalog/swimming-equipment', true),
        ]),
        'bg' => 'background: #888888 url(\'/images/homepage_pins/x.png\') center center / cover',
        'icon' => '/images/homepage_pins/pin01.png',
        'title' => Yii::t('app', 'Inventory'),
        'desc' => Yii::t('app', 'Trainers, fins, shoulder blades and other swimming equipment')
    ],
    [
        'href' => Local::choose([
            Local::RU => Url::to('/catalog/aksessuary/ochki/startovye-ochki', true),
            Local::EU => Url::to('/catalog/racing-goggles', true),
        ]),
        'bg' => 'background: #44d89b url(\'/images/homepage_pins/triangle.png\') center center / cover',
        'icon' => '/images/homepage_pins/pin02.png',
        'title' => Yii::t('app', 'Swimming goggles'),
        'desc' => Yii::t('app', 'Professional swimming goggles for competitions. FINA Approved.')
    ],
    [
        'href' => Local::choose([
            Local::RU => Url::to('/catalog/gidrokostjumy-dlya-plavaniya', true),
            Local::EU => Url::to('/catalog/racing-suits', true),
        ]),
        'bg' => 'background: #f74d4d url(\'/images/homepage_pins/romb.png\') center center / cover',
        'icon' => '/images/homepage_pins/pin2.png',
        'title' => Yii::t('app', 'Racing suits'),
        'desc' => Yii::t('app', 'Start suits with unique advanced technologies.')
    ],
    [
        'href' => Local::choose([
            Local::RU => Url::to('/catalog/tekstil-sportivnoe-plavanie', true),
            Local::EU => Url::to('/catalog/womens-swimwear', true),
        ]),
        'bg' => 'background: #61b7c5 url(\'/images/homepage_pins/circle.png\') center center / cover',
        'icon' => '/images/homepage_pins/pin04.png',
        'title' => Yii::t('app', 'Swimsuits'),
        'desc' => Yii::t('app', 'One- and two-piece sport Mad Wave swimsuits')
    ],
];
?>

<section id="pinned-categories">
    <div class="container p0">
        <?php foreach ($categories as $c) { ?>
            <div class="col-sm-6 col-md-3">
                <a class="pinned-item" href="<?= $c['href'] ?>">
                    <div class="background" style="<?= $c['bg'] ?>">
                        <div style="background: url('<?= $c['icon'] ?>') center center / contain no-repeat"></div>
                    </div>
                    <h4><?= $c['title'] ?></h4>
                    <p><?= $c['desc'] ?></p>
                </a>
            </div>
        <?php } ?>
    </div>
</section>