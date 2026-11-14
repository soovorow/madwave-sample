<?php
namespace common\components\postifi\services;

/**
 *  Posti - Small Parcel
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ParcelSmall extends PostiService
{
    protected $_description = 'Posti Small Parcel is an affordable and easy way tto send light goods that fit into a mail slot or box. It is delivered with the day mail';

    public function getId()
    {
        return 'PO2461';
    }

    public function getMinimumWeight()
    {
        return 0;
    }

    public function getMaximumWeight()
    {
        return 2;
    }

    public function getPrice()
    {
        return $this->roundPrice(3.90);
    }

    public function getPostalCode()
    {
        // TODO: Implement getPostalCode() method.
    }
}