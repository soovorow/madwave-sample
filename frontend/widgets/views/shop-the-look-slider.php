<?php

use common\models\TheLook;
use yii\helpers\Html;

/**
 * @var $look TheLook
 * @var $looks TheLook[]
 */

$this->registerJs(<<<JS
    $('.look-slider').slick({
        slidesToShow: $slideToShow,
        slidesToScroll: 1,
        infinite: false,
        dots: false,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    initialSlide: $key,
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    })
JS
);
?>

<style>
    .look-pic {
        overflow: hidden;
        position: relative;
        margin-top: -60px;
        margin-bottom: -60px;
    }

    .look-slider {
        background: #f9f9f9;
    }

    .look-slider,
    .look-slider .slick-list
    {
        overflow: visible;
    }
    .look-pic img {
        width: 100%;
    }

    .look-pic .btn-container {
        position: absolute;
        top: 220px;
        left: 0;
        text-align: center;
        width: 100%;
    }

    .look-pic .btn {
        display: none;
    }

    .look-pic.selected {
        border-bottom: 3px solid #00b0ce;
    }
    .look-pic:hover .btn {
        display: inline-block;
        box-shadow: 0 0 30px 15px rgba(84, 206, 255, .5);
    }
</style>

<div class="look-slider">
    <?php if ($looks) {
        $looks_count = count($looks);
        $class = $looks_count == 3 ? 'col-xs-4' : 'col-xs-3';
        foreach ($looks as $i => $look) { ?>
            <div class="<?= $class ?> look-pic <?= $i == $key && $looks_count !== 3 ? 'selected' : '' ?>">
                <?= Html::img($look->getImage(), [
                    'alt' => $look->title
                ]) ?>

                <div class="btn-container">
                    <?= Html::a(Yii::t('app', 'VIEW THE LOOK'), ['/look/index', 'slug' => $look->slug], [
                        'class' => 'btn btn-primary'
                    ]) ?>
                </div>
            </div>
        <?php }
    } ?>
</div>
