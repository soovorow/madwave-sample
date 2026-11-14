<?php

namespace common\widgets\product\page;

use common\components\multilocal\Local;
use common\models\Product;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class MarketingLabels extends Widget
{

    public $v;
    public $model;
    public $show_product_page_button;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {


        return $this->render('marketing-labels', [
            'v' => $this->v,
            'model' => $this->model,
            'show_product_page_button' => $this->show_product_page_button
        ]);
    }
}
