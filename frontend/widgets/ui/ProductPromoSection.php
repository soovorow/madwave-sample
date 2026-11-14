<?php

namespace frontend\widgets\ui;

use yii\base\Widget;

class ProductPromoSection extends Widget
{
    const FLOAT_RIGHT = 'right';

    const FLOAT_LEFT = 'left';

    public $float;

    public $image;

    public $title;

    public $desc;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return $this->render('product-promo-section', [
            'model' => $this
        ]);
    }
}
