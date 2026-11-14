<?php

namespace common\widgets\product\page;

use common\components\multilocal\Local;
use common\models\Product;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

/**
 * RelatedProducts Widget
 *
 * Widget for rendering related products previews
 *
 * @property Product $product
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class RelatedProducts extends Widget
{

    public $product;
    
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
        $relatedData = $this->getRPData();

//        return $relatedData->totalCount;

        if ($relatedData->totalCount < 1) {
            return null;
        }
        
        $size = null;
        
        if ($relatedData->totalCount > 4 || $relatedData->totalCount == 1 || $relatedData->totalCount == 3) {
            $size = 2;
        }

        if ($relatedData->totalCount == 1) {
            $pusher = '<div class="col-lg-2 col-lg-15"></div><div class="col-lg-2 col-lg-15 col-md-4 col-xs-3"></div>';
        } elseif ($relatedData->totalCount == 2) {
            $pusher = '<div class="col-lg-3 col-md-2"></div>';
        } elseif ($relatedData->totalCount == 3) {
            $pusher = '<div class="col-lg-2 col-lg-15"></div>';
        } else {
            $pusher = '';
        }

        return $this->render('related-products', [
            'pusher' => $pusher,
            'dataProvider' => $relatedData,
            'size' => $size
        ]);
    }

    /**
     * Get Related Products data
     *
     * @return ActiveDataProvider
     */
    private function getRPData() {
        return new ActiveDataProvider([
            'query' => \common\models\RelatedProducts::find()
                ->where(1)
                ->innerJoin('product_variant', '`product_variant`.`product_id` = `related_products`.`related_product_id`')
                ->innerJoin('variant_stock', '`variant_stock`.`variant_id` = `product_variant`.`variant_id`')
                ->innerJoin('price', '`price`.`variant_id` = `product_variant`.`variant_id`')
                ->andWhere(['>', 'price.price_rub', 0])
                ->andWhere(['>', 'variant_stock.quantity', 0])
                ->andWhere(['variant_stock.store_id' => Local::choose([
                    Local::RU => '00005',
                    Local::EU => '00049',
                ])])
                ->andWhere(['related_products.product_id' => $this->product->id])
                ->orderBy('related_products.correlation_score DESC')
                ->groupBy('product_variant.product_id')
            ,
            'pagination' => [
                'pageSize' => 4
            ]
        ]);
    }
}
