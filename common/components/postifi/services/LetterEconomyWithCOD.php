<?php
namespace common\components\postifi\services;

/**
 * Posti - Economy-letter with COD
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class LetterEconomyWithCOD extends PostiService
{
    protected $_description = 'An affordable Economy Letter is delivered to the recipient primarily on the third weekday following the mailing';

    public $label = 'Posti Economy Letter with COD';
    public $description = 'An affordable Economy Letter is delivered to the recipient primarily on the third weekday following the mailing';
    public $code = 'PO5001';

    public $_autoComplete;
    public $address;

    //Возможно пора переименовать
    public $view = 'checkout___delivery__courier';

    public function getId()
    {
        return 'PO5002';
    }

    public function getPrice()
    {
        $cod_price = 5;
        $price = 0.6358 + (4.3794 * $this->getItemsWeight());

        if (\Yii::$app->cart->getCost() > 2000) {
            return false;
        }
        return $this->roundPrice($cod_price + $price);
    }

    public function getMinimumWeight()
    {
        return 0;
    }

    public function getMaximumWeight()
    {
        return null;
    }

    public function getPostalCode()
    {
        // TODO: Implement getPostalCode() method.
    }
}