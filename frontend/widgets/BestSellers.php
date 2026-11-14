<?php

namespace frontend\widgets;

use common\models\ProductVariant;
use frontend\models\ProductSearch;
use yii\base\Widget;

/**
 * Showcase of bestsellers widget
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class BestSellers extends Widget
{

    public $size = 2;

    public $limit;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(['pageSize' => 4]);
//        $dataProvider = ProductSearch::getSearchQuery()
//            ->groupBy('product_variant.product_id')
//            ->orderBy('product_order_metrics.ctr DESC')
//            ->limit(4)
//            ->all();

        echo '<div class="best-sellers">';
        foreach ($dataProvider->models as $n => $model) {
            $n++;
            /** @var $model ProductVariant */
            echo $model->preview('Виджет бестселлеров', $n, $this->size);
        }
        echo '</div>';
    }
}
