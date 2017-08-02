<?php

namespace TestCart\Storage;

use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager
{
    function createMysqlDriver()
    {
        return $this->app->make(MysqlStorage :: class);
    }

    function createRedisDriver()
    {
        return $this->app->make(RedisStorage :: class);
    }

    function getDefaultDriver()
    {
        return $this->app['config']->get('test_cart.storage');
    }
}