<?php
namespace frontend\widgets;
use yii\base\Widget;
use common\models\Order;
use common\models\User;
use yii\helpers\Url;

class YandexKassa extends Widget{
    public $order_id;

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
        if (!empty($_GET['payment'])) {
            if ($_GET['payment'] == 'success') {
                \Yii::$app->session->setFlash('success', 'Спасибо! Ваш заказ оплачен!');
            }

            if ($_GET['payment'] == 'fail') {
                \Yii::$app->session->setFlash(
                    'danger',
                    'Ваш заказ не оплачен. Пожалуйста, 
                    повторите попытку или свяжитесь с нами по телефону: 8 800 100 57 19'
                );
            }
        }

        $order = Order::findOne($this->order_id);
        $user = User::findByEmail($order->customer_email)->getId();
        $currentUrl =  Url::base(true) . '/'. \Yii::$app->request->pathInfo . '?id=' . $this->order_id;

        $params = [
            'order' => $order,
            'order_id' => $order->getId(),
            'user' => $user,
            'total' => $order->total,
            'success' => $currentUrl . '&payment=success',
            'fail' => $currentUrl  . '&payment=fail',
            'default' => $currentUrl
        ];

        return $this->render('yandex-kassa', $params);
    }
}