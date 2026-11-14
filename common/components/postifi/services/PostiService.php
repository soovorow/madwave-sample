<?php
namespace common\components\postifi\services;

use frontend\modules\checkout\eu\models\delivery_methods\DeliveryMethod;
use frontend\modules\checkout\eu\models\delivery_methods\DeliveryMethodInterface;

/**
 * Posti.fi services matrix
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
abstract class PostiService extends DeliveryMethod implements PostiServiceInterface, DeliveryMethodInterface
{
    protected $_price;
    protected $_description;

    protected $_items_count;
    protected $_items_weight;

    public function getCost()
    {
        return $this->getPrice();
    }

    public function getFirstName()
    {
    }

    public function getLastName()
    {
    }

    public function getSecondName()
    {
    }

    public function getJsInitFunction()
    {
        return 'initPosti';
    }

    protected function roundPrice($price)
    {
        $round = round($price, 0, PHP_ROUND_HALF_UP);

        $price = $round;

        if ($price > 0 && $price <= 10) {
            return 10;
        }
        if ($price > 10 && $price <= 15) {
            return 15;
        }
        if ($price > 15 && $price <= 20) {
            return 20;
        }
        if ($price > 20 && $price <= 25) {
            return 25;
        }
        if ($price > 25 && $price <= 30) {
            return 30;
        }
        if ($price > 30 && $price <= 35) {
            return 35;
        }
        if ($price > 35 && $price <= 40) {
            return 40;
        }
        if ($price > 40 && $price <= 45) {
            return 45;
        }
        if ($price > 45 && $price <= 50) {
            return 50;
        }

        return $price;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function getItemsCount()
    {
        return $this->_items_count;
    }

    public function setItemsCount($count)
    {
        $this->_items_count = $count;
        return $this;
    }

    public function getItemsWeight()
    {
        return $this->_items_weight;
    }

    public function setItemsWeight($weight)
    {
        $this->_items_weight = $weight;
        return $this;
    }
}