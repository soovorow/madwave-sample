<?php

namespace common\components\pop_ups;

use yii\bootstrap\Modal;

class PopUps
{
    const SESSION_KEY = 'signup_popup';

    const IN_CART = 100;
    const OUT_CART = 101;

    
    public static function run()
    {
        if (!\Yii::$app->user->isGuest) {
            return false;
        }
        $session = \Yii::$app->session;
        $controller = \Yii::$app->controller->id;
        $mod = $controller == 'cart' ? self::IN_CART : self::OUT_CART;
        $session_value = $session->has(self::SESSION_KEY) ? $session->get(self::SESSION_KEY) : null;

        if (!isset($session_value[$mod])) {
            $session_value[$mod] = true;
            self::initiate();
            $session->set(self::SESSION_KEY, $session_value);
        }
    }

    public function initiate() {
        $id = 'signup-popup';
        $view = \Yii::$app->view;

        Modal::begin(['id' => $id,
            'header' => '<h2>Выгода с первой покупки! <hr><small>Чтобы применить скидку до 60% по картам постоянных покупателей,
просто зарегистрируйтесь или войдите в систему.</small></h2>',
        ]);
        echo $view->render('_authorization');
        Modal::end();
        $view->registerJs("$(window).load(function() { $('#$id').modal('show') })");
    }
}