<?php

namespace TestCart;

class Item
{
    public $id;
    public $qty = 1;
    public $price = 1;

    function __construct(array $args)
    {
        foreach ($args as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
        if (empty($this->id)) {
            throw new Exceptions\InvalidItemException('Item identifier id mandatory');
        }
    }
}