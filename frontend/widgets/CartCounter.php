<?php

namespace frontend\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class CartCounter extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $c = \Yii::$app->cart->getCount();
        $counter = $c ? ('<span class="cc">' . ($c > 9 ? '9+' : $c) . '</span>') : '';
        return Html::tag('i', '<span class="cart-counter">'.$counter.'</span>', [
            'class' => 'fa fa-shopping-bag '.($c ? 'has-items' : '')
        ]);
    }
}
