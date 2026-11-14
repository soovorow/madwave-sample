<?php
namespace common\components\postifi\services;

/**
 * Posti - Home Parcel
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ParcelHome extends PostiService
{
    protected $_description = 'With Home Parcel you can deliver items to your customer\'s home at the agreed time, even by the next weekday. Home Parcel is also suitable for large items and is available to any destination in Finland';

    public function getId()
    {
        return 'PO2104';
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
        return $this->roundPrice(12.93 + (0.314 * $this->getItemsWeight()));
    }

    public function getPostalCode()
    {
        // TODO: Implement getPostalCode() method.
    }
}