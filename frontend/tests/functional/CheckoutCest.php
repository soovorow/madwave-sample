<?php
namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use Yii;

/**
 * Checkout form test
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class CheckoutCest
{
    protected $formId = '#checkoutForm';

    protected $customerFields = [
        'Customer[city]' => 'Санкт-Петербург',
        'Customer[phone]' => '+79110831725',
        'Customer[email]' => 'soovorow@gmail.com',
        'Customer[lastName]' => 'test',
        'Customer[firstName]' => 'test',
        'Customer[secondName]' => 'test',
    ];

    protected $paymentCOD = [
        'Payment[payment_method]' => 'ТГ505443'
    ];
    protected $paymentPSBANK = [
        'Payment[payment_method]' => 'ТГ507040'
    ];

    protected $postOfRussiaCOD = [
        'Delivery[delivery_method]' => 1,
        'MethodMail[locality]' => 'Санкт-Петербург',
        'MethodMail[street]' => 'Пушкинская',
        'MethodMail[house]' => '11',
        'MethodMail[postalCode]' => '191025',
    ];

    protected $courierCOD = [
        'Delivery[delivery_method]' => 2,

        'MethodCourier[locality]' => 'Санкт-Петербург',
        'MethodCourier[street]' => 'Пушкинская',
        'MethodCourier[house]' => '11',
        'MethodCourier[postalCode]' => '191025',
    ];

    protected $firstClassPostOfRussiaCOD = [
        'Delivery[delivery_method]' => 3,

        'MethodFirstClassPostRF[locality]' => 'Санкт-Петербург',
        'MethodFirstClassPostRF[street]' => 'Пушкинская',
        'MethodFirstClassPostRF[house]' => '11',
        'MethodFirstClassPostRF[postalCode]' => '191025',
    ];

    protected $firstClassPostOfRussiaWithEmptyDeliveryMethod = [
        'MethodFirstClassPostRF[locality]' => 'Санкт-Петербург',
        'MethodFirstClassPostRF[street]' => 'Пушкинская',
        'MethodFirstClassPostRF[house]' => '11',
        'MethodFirstClassPostRF[postalCode]' => '191025',
    ];

    public function checkoutWithOutOfStock(FunctionalTester $I)
    {
        $I->addProduct(10022584, 0);

        $I->amOnRoute('/checkout/cart/checkout');

        $I->submitForm($this->formId, $this->customerFields + $this->postOfRussiaCOD + $this->paymentCOD);

        $I->dontSeeInCurrentUrl('checkout/cart/checkout-complete');

    }

    /**
     * Test Post of Russia with COD
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     *
     * @param FunctionalTester $I
     */
    public function checkoutPostOfRussiaCOD(FunctionalTester $I)
    {
        $I->addProduct(10022584, 1);

        $I->amOnRoute('/checkout/cart/checkout');

        $I->submitForm($this->formId, $this->customerFields + $this->postOfRussiaCOD + $this->paymentCOD);

        $I->seeInCurrentUrl('checkout/cart/checkout-complete');

        $I->canSeeRecord('common\models\Order', ['id' => $_GET['id']]);

        $I->canSeeFileFound(Yii::$app->params['1c.order_export_dir'].'/order' . $_GET['id'] . '.txt');
    }

    /**
     * Test Courier with COD
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     *
     * @param FunctionalTester $I
     */
    public function checkoutCourierCOD(FunctionalTester $I)
    {
        $I->addProduct(10022584, 1);

        $I->amOnRoute('/checkout/cart/checkout');

        $I->submitForm($this->formId, $this->customerFields + $this->courierCOD + $this->paymentCOD);

        $I->seeInCurrentUrl('checkout/cart/checkout-complete');

        $I->canSeeRecord('common\models\Order', ['id' => $_GET['id']]);

        $I->canSeeFileFound(Yii::$app->params['1c.order_export_dir'].'/order' . $_GET['id'] . '.txt');
    }

    /**
     * Test First Class Post of Russia with COD
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     *
     * @param FunctionalTester $I
     */
    public function checkoutFirstClassPostOfRussiaCOD(FunctionalTester $I)
    {
        $I->addProduct(10022584, 1);

        $I->amOnRoute('/checkout/cart/checkout');

        $I->submitForm($this->formId, $this->customerFields + $this->firstClassPostOfRussiaCOD + $this->paymentCOD);

        $I->seeInCurrentUrl('checkout/cart/checkout-complete');

        $I->canSeeRecord('common\models\Order', ['id' => $_GET['id']]);

        $I->canSeeFileFound(Yii::$app->params['1c.order_export_dir'].'/order' . $_GET['id'] . '.txt');
    }

    public function checkoutFristClassPostOfRussiaPayOnline(FunctionalTester $I) {
        $I->addProduct(10022584, 1);

        $I->amOnRoute('/checkout/cart/checkout');

        $I->submitForm($this->formId, $this->customerFields + $this->firstClassPostOfRussiaWithEmptyDeliveryMethod + $this->paymentPSBANK);

        $I->dontSeeInCurrentUrl('checkout/cart/checkout-complete');
    }
}