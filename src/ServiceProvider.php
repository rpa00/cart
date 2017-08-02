<?php

namespace TestCart;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database');
        $this->publishes([
            __DIR__.'/../config/test_cart.php' => config_path('test_cart.php'),
        ]);

        $namespace = $this->app->config['test_cart.namespace'];

        $this->app->events->listen($namespace . '.add', function($cartId, Item $item){
            $cart = $this->getCart($cartId);
            $cart->addItem($item);
            $cart->persist();
        });
        $this->app->events->listen($namespace . '.delete', function($cartId, Item $item){
            $cart = $this->getCart($cartId);
            $cart->deleteItem($item);
            $cart->persist();
        });
        $this->app->events->listen($namespace . '.coupon', function($cartId, string $number){
            $cart = $this->getCart($cartId);
            $cart->assignCoupon($number);
            $cart->persist();
        });
        $this->app->events->listen($namespace . '.dump', function($cartId){
            $cart = $this->getCart($cartId);
            echo json_encode($cart->dump(), JSON_PRETTY_PRINT);
        });
        $this->app->events->listen($namespace . '.flush', function($cartId){
            $cart = $this->getCart($cartId);
            $cart->flush();
            $cart->persist();
        });

    }

    function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/test_cart.php', 'test_cart'
        );
        $this->app->singleton(Cart :: class);
    }

    private function getCart($cartId)
    {
        $namespace = $this->app->config['test_cart.namespace'];
        $bindKey = $namespace . '.cart.' . $cartId;
        try {
            $cart = $this->app->make($bindKey);
        } catch (\Exception $e) {
            $cart = $this->app->makeWith(Cart :: class, ['cartId' => $cartId]);
            $this->app->instance($bindKey, $cart);
        }
        return $cart;
    }
}