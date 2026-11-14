<?php

namespace common\components;

/**
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class DateTimeFormatter
{
    public static function dateAsString($timestamp)
    {
        $today = date('d.m.Y');
        $yesterday = date('d.m.Y', strtotime('-1 day'));

        $order = date('d.m.Y', $timestamp);
        if ($today == $order) {
            $orderDate = 'Сегодня,';
        } elseif ($yesterday == $order) {
            $orderDate = 'Вчера,';
        } else {
            $orderDate = $order;
        }

        return $orderDate.' '.date('H:i', $timestamp).'';
    }

}