<?php

namespace common\widgets\product\page;

use common\models\Product;
use yii\base\Widget;
use yii\data\ActiveDataProvider;

/**
 * Viewed products Widget
 *
 * Widget for rendering last viewed products
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class ViewedProducts extends Widget
{

    const SESSION_KEY = 'viewedProducts';

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
        $session = \Yii::$app->session;

        if (!$session->has(self::SESSION_KEY)) {
            return false;
        }

        $viewedProducts = $session->get(self::SESSION_KEY);

        if (count($viewedProducts) > 1) {
            return $this->render('viewed-products', [
                'viewedProducts' => array_reverse($viewedProducts),
            ]);
        }
        return false;
    }

    /**
     * Save viewd product id in session
     *
     * @param $product_id integer
     * @return void
     */
    public static function addViewInSession($product_id) {
        $session = \Yii::$app->session;
        if (!$session->has(self::SESSION_KEY)) {
            $viewedProducts = [];
        } else {
            $viewedProducts = $session->get(self::SESSION_KEY);
        }

        $key = array_search($product_id,$viewedProducts);
        if (is_int($key)) {
            unset($viewedProducts[$key]);
        }
        $viewedProducts = array_values($viewedProducts);
        $viewedProducts[] = $product_id;

        if (count($viewedProducts) > 5) {
            unset($viewedProducts[0]);
            $viewedProducts = array_values($viewedProducts);
        }

        $session->set(self::SESSION_KEY,  $viewedProducts);
    }

}
