<?php

namespace TestCart\Storage;

class MysqlStorage extends Storage
{

    function fetch(string $key):array
    {
        $row = \DB :: selectOne('select content from test_cart where id=?', [$this->encodeKey($key)]);
        return $this->decode($row->content ?? '');
    }

    function persist(string $key, array $data)
    {
        \DB :: statement('replace into test_cart (id, content) values (?, ?)', [
            $this->encodeKey($key),
            $this->encode($data)
        ]);
    }


}