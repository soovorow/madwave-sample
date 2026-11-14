<?php
namespace common\components\postifi\services;

/**
 * Posti - Express letter
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class LetterExpress extends PostiService
{
    protected $_description = 'A fast Priority Letter is delivered primarily on the weekday following the mailing';

    public $label = 'Posti Express Letter';
    public $description = 'A fast Priority Letter is delivered primarily on the weekday following the mailing';
    public $code = 'PO5001';

    public $_autoComplete;
    public $address;

    //Возможно пора переименовать
    public $view = 'checkout___delivery__courier';

    public function getId()
    {
        return 'PO5001';
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getPrice()
    {
        $price = 0.9253 + (8.2236 * $this->getItemsWeight());
        return $this->roundPrice($price + 3.42);
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