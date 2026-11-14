<?php

namespace frontend\modules\checkout\ru\models;

use frontend\modules\checkout\ru\models\payment_methods\MethodCOD;
use frontend\modules\checkout\ru\models\payment_methods\MethodPsBank;
use frontend\modules\checkout\ru\models\payment_methods\MethodYandex;
use frontend\modules\checkout\ru\models\payment_methods\PaymentMethod;
use frontend\modules\checkout\ru\models\payment_methods\PaymentMethodInterface;
use frontend\modules\checkout\ru\models\pricing\CardOnly;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Payment extends Model
{

    public $_payment_methods;
    public $payment_method;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->_payment_methods = [
            new MethodPsBank(),
//            new MethodYandex(),
            new MethodCOD(),
        ];
    }

    public function rules()
    {
        return [
            [['payment_method'],'required'],
            [['payment_method'],'validatePaymentMethod']
        ];
    }

    public function attributeLabels()
    {
        return [
            'payment_method' => 'Способ оплаты'
        ];
    }

    public function validatePaymentMethod()
    {
        /** @var $pm PaymentMethod */
        //TODO: Сделать более универсально, так чтобы определялось через метод а не по его порядковому номеру
        $cardOnlyDiscount = CardOnly::fetchFromSession();
        if ($cardOnlyDiscount && $this->payment_method === (new MethodCOD())->getCode()) {
            $this->addError('payment_method');
            return false;
        }

        foreach ($this->_payment_methods as $pm) {

            if ($this->payment_method === $pm->getCode()) {
                return true;
            }
        }
        $this->addError('payment_method');
    }

    /**
     * @param $form ActiveForm
     * @return string
     */
    public static function widget($model, $form)
    {
        $payment_methods = self::mapAllMethods();

        if (CardOnly::fetchFromSession()) {
            $model->payment_method = (new MethodYandex())->getCode();
        }

        $radio = $form
            ->field($model, 'payment_method')
            ->radioList($payment_methods, ['class' => 'delivery-radio list padded clearfix'])
            ->label(false);

        $content = '<h4 class="divide" id="payment_methods_h4">Выберите способ оплаты</h4>';
        $content.= Html::tag('div', $radio, ['class' => 'mb30 clearfix help-block-radio']);
        $widget = Html::tag('div', $content, ['class' => 'payment-methods']);

        return $widget;
    }

    private function mapAllMethods()
    {
        $payment_methods = [];
        $payment = new Payment();

        foreach ($payment->_payment_methods as $method) {
            /** @var $method PaymentMethodInterface */
            $payment_methods[$method->getCode()] = self::wrapRadio($method->getLabel(), $method->getDescription());
        }

        return $payment_methods;
    }

    private function wrapRadio($label, $description) {
        return '<span class="clearfix"><i></i><span class="l">'.$label.'</span><small>'.$description.'</small></span>';
    }

    public function getMethodByCode($code)
    {
        foreach ($this->_payment_methods as $paymentMethod) {
            if ($paymentMethod->code == $code) {
                return $paymentMethod;
            }
        }

        return false;
    }
}