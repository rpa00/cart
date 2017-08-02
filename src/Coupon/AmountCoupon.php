<?php

namespace TestCart\Coupon;

class AmountCoupon extends Coupon
{
    protected $discount = 0;

    function __construct($number)
    {
        $this->number = $number;
        $this->discount = floatval(str_replace('amt', '', $number));
    }

    function discountAmount(float $amount)
    {
        return $amount - $this->discount;
    }

    static function exists(string $number)
    {
        return strpos($number, 'amt') === 0;
    }

    function validate()
    {
        return true;
    }
}