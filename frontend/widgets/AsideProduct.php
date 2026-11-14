<?php

namespace frontend\widgets;

use common\models\ProductVariant;
use frontend\models\ProductSearch;
use yii\base\Widget;

/**
 * Aside product
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class AsideProduct extends Widget
{

    public $model;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return $this->render('aside_product', ['v' => $this->model]);
    }
}
