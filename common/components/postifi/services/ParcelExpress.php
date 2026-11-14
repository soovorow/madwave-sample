<?php
namespace common\components\postifi\services;

/**
 * Posti - Express Parcel
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class ParcelExpress extends PostiService
{
    protected $_description = 'Posti Express is a cost-effective and reliable nationwide transport service, with which your item is delivered as a parcel, in a roll cage, or on a pallet by 2 p.m the next weekday.';

    public $label = 'Posti Express Parcel';
    public $description = 'Posti Express is a cost-effective and reliable nationwide transport service, with which your item is delivered as a parcel, in a roll cage, or on a pallet by 2 p.m the next weekday.';

    public $code = '"ТГ507168"';


    public $_autoComplete;
    public $address;
    public $postalCode;

    public $view = 'checkout___delivery__courier';

    public function getId()
    {
        return 'PO2102';
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
        return $this->roundPrice(12.58 + (0.305 * $this->getItemsWeight()));
    }

    public function getAddress()
    {
        return $this->address;
    }
    public function getPostalCode()
    {
        return $this->postalCode;
    }
}