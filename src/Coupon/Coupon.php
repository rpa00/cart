<?php

namespace TestCart\Coupon;

use TestCart\Exceptions\CouponException;

abstract class Coupon
{
    protected $number;

    abstract function discountAmount(float $amount);
    abstract static function exists(string $number);
    abstract function validate();

    static function createByNumber($number)
    {
        $pipe = [AmountCoupon :: class, PercentCoupon :: class];
        foreach ($pipe as $class) {
            if ($class :: exists($number)) {
                return new $class($number);
            }
        }
        throw new CouponException('Coupon ' . $number . ' is invalid');
    }

    function getNumber()
    {
        return $this->number;
    }
}