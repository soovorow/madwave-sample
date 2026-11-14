<?php
namespace frontend\widgets;
use frontend\modules\checkout\ru\models\payment_methods\MethodPsBank;
use yii\base\Widget;
use common\models\Order;
use yii\helpers\Url;

class PsBank extends Widget{
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
        $method = new MethodPsBank();
        return $this->render('ps-bank', $method->post($this->order_id));
    }
}