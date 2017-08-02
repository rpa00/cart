<?php

namespace TestCart\Coupon;

class PercentCoupon extends Coupon
{
    protected $percent = 0;

    function __construct($number)
    {
        $this->number = $number;
        $this->percent = floatval(str_replace('pct', '', $number));
    }

    function discountAmount(float $amount)
    {
        return $amount - $amount * $this->percent / 100;
    }

    static function exists(string $number)
    {
        return strpos($number, 'pct') === 0;
    }

    function validate()
    {
        return true;
    }
}