<?php

namespace TestCart\Storage;

class RedisStorage extends Storage
{

    function fetch(string $key):array
    {
        return $this->decode(\Redis :: get($this->encodeKey($key)) ?? '');
    }

    function persist(string $key, array $data)
    {
        \Redis :: set($this->encodeKey($key), $this->encode($data));
    }


}