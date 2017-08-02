<?php

namespace TestCart;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;

class Cart
{
    protected $items;
    protected $cartId;
    protected $coupon;
    protected $storage;

    function __construct($cartId)
    {
        $this->cartId = $cartId;
        $this->storage = (new Storage\Manager(Application :: getInstance()))->driver();
        $this->fetch();
    }

    function addItem(Item $item)
    {
        $exists = $this->items->get($item->id);
        if ($exists instanceof Item) {
            if ($exists->price != $item->price) {
                throw new Exceptions\InvalidItemException('Different prices on same item');
            }
            $item->qty += $exists->qty;
        }
        $this->items->put($item->id, $item);
    }

    function getItem(int $itemId)
    {
        $item = $this->items->get($itemId);
        if (!$item instanceof Item) {
            throw new Exceptions\CartException('Item #' . $itemId . ' not found on cart #' . $this->cartId);
        }
        return $item;
    }

    function deleteItem(Item $item)
    {
        $item = $this->items->get($item->id);
        if (!$item instanceof Item) {
            throw new Exceptions\CartException('Item #' . $item->id . ' not found on cart #' . $this->cartId);
        }
        $this->items->forget($item->id);
    }

    function deleteItemById(int $itemId)
    {
        $item = new Item(['id' => $itemId]);
        return $this->deleteItem($item);
    }

    function setItem(Item $item)
    {
        $this->items->put($item->id, $item);
    }

    function increment(int $itemId, float $qty)
    {
        $item = $this->getItem($itemId);
        $item->qty += $qty;
        $this->setItem($item);
    }

    function decrement(int $itemId, float $qty)
    {
        $item = $this->getItem($itemId);
        $item->qty -= $qty;
        if ($item->qty <= 0) {
            throw new Exceptions\InvalidItemException('Invalid quantity {' . $item->qty . '} for item #' . $itemId . ' on cart #' . $this->cartId);
        }
        $this->setItem($item);
    }

    function flush()
    {
        $this->items = new Collection();
        $this->coupon = null;
    }


    function getTotal():float
    {
        $amount = $this->items->reduce(function($sum, $item){
            return $sum + $item->price * $item->qty;
        }) ?? 0;
        if ($this->coupon) {
            $amount = $this->coupon->discountAmount($amount);
        }
        return $amount;
    }

    function assignCoupon(string $number)
    {
        $coupon = Coupon\Coupon :: createByNumber($number);
        if ($coupon->validate()) {
            $this->coupon = $coupon;
            return;
        }
        throw new Exceptions\CouponException('Coupon ' . $number . ' validation failed');
    }

    function dump()
    {
        $dump = $this->toArray();
        $dump['total'] = $this->getTotal();
        return $dump;
    }

    function persist()
    {
        $this->storage->persist($this->cartId, $this->toArray());
    }

    protected function fetch()
    {
        $this->items = new Collection();
        $data = $this->storage->fetch($this->cartId);
        $class = Application :: getInstance()->config['test_cart.itemClass'];
        array_map(function($item) use ($class){
            $this->items->put($item['id'], new $class($item));
        }, $data['items'] ?? []);
        if (!empty($data['coupon'])) {
            $this->assignCoupon($data['coupon']);
        }
    }

    protected function toArray():array
    {
        return [
            'coupon' => $this->coupon ? $this->coupon->getNumber() : null,
            'items' => $this->items->toArray()
        ];
    }
}