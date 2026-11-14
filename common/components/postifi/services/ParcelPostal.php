<?php
namespace common\components\postifi\services;

/**
 * Posti - Postal Parcel
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ParcelPostal extends PostiService
{
    protected $_description = 'A Postal Parcel is picked p at a Posti outlet or at a Parcel Point. Postal Parcel is affordable and fast, and your customer can choose a pickup point from the most extensive network in Finland';

    public function getId()
    {
        return 'PO2103';
    }

    public function getMinimumWeight()
    {
        return 0.1;
    }

    public function getMaximumWeight()
    {
        return 35;
    }

    public function getPrice()
    {
        return $this->roundPrice(7.31 + (0.32 * $this->getItemsWeight()));
    }

    public function getPostalCode()
    {
        // TODO: Implement getPostalCode() method.
    }
}