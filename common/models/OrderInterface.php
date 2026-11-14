<?php

namespace common\models;

interface OrderInterface
{
    public function create();

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return OrderItem[]
     */
    public function getOrderItems();

    /**
     * @return double
     */
    public function getWeight();

    /**
     * @return double
     */
    public function getClearPrice();

    /**
     * @return double
     */
    public function getTotal();

    /**
     * @return string
     */
    public function getCustomerFullName();

    /**
     * @return string
     */
    public function getCustomerPhone();

    /**
     * @return string
     */
    public function getCustomerEmail();

    /**
     * @return string
     */
    public function getDeliveryAddress();

    /**
     * @param bool $code ? return 1cCode : Method Label
     * @return string
     */
    public function getDeliveryMethod($code = false);

    /**
     * @param bool $code ? return 1cCode : Method Label
     * @return string
     */
    public function getPaymentMethod($code = false);

}