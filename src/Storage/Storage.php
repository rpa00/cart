<?php

namespace TestCart\Storage;

abstract class Storage
{
    abstract function fetch(string $key):array;
    abstract function persist(string $key, array $content);

    protected function encode(array $data):string
    {
        return json_encode($data);
    }

    protected function decode(string $data): array
    {
        if (empty($data)) {
            return [];
        }
        return json_decode($data, true);
    }

    protected function encodeKey($key)
    {
        return 'test_cart_' . intval($key);
    }
}