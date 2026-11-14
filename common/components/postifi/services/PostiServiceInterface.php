<?php

namespace common\components\postifi\services;

/**
 * Posti.fi service interface
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
interface PostiServiceInterface
{
    public function getId();

    public function getPrice();

    public function getMinimumWeight();

    public function getMaximumWeight();

    public function getItemsCount();

    public function setItemsCount($count);

    public function getItemsWeight();

    public function setItemsWeight($weight);

    public function getDescription();

}