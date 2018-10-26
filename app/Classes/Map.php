<?php

namespace App\Classes;


class Map
{
    protected $items = [];

    public function set (String $key, $obj) {
        $this->items[$key] = $obj;
    }

    public function get (String $key) {
        if ($this->has($key)) {
            return $this->items[$key];
        } else {
            return null;
        }
    }

    public function has (String $key) {
        return array_key_exists($key, $this->items);
    }
}