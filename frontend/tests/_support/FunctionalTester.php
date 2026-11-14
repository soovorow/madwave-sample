<?php
namespace frontend\tests;

use common\models\ProductVariant;
use yz\shoppingcart\ShoppingCart;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    public function seeValidationError($message)
    {
        $this->see($message, '.help-block');
    }
    public function dontSeeValidationError($message)
    {
        $this->dontSee($message, '.help-block');
    }

    public function addProduct($sku, $quantity)
    {
        /**
         * @var $cart ShoppingCart
         */
        $cart = \Yii::$app->cart;

        $product = ProductVariant::findOne($sku);
        $stock = $product->getStock()->one();
        $stock->quantity = $quantity;
        $stock->save(false);

        $cart->put($product);
    }
}
