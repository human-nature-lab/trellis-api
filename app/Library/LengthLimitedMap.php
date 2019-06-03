<?php

namespace App\Library;


use Illuminate\Support\Facades\Log;

class LengthLimitedMap extends Map {

    protected $maxEntries;
    protected $evictSize;
    protected $addCount = 0;
    protected $touchCount = 0;
    protected $meta = [];
    protected $size = 0;

    public function __construct ($maxEntries = 100) {
        $this->maxEntries = $maxEntries;
        $this->evictSize = floor($maxEntries / 10);
    }

    public function set (String $key, $obj) {
        $this->items[$key] = $obj;
        $this->meta[$key] = (object) [
            'added_at' => $this->addCount,
            'touched_at' => $this->touchCount,
            'touches' => 0
        ];
        $this->size++;
        $this->addCount++;
        if ($this->size > $this->maxEntries) {
            $this->evict();
        }
    }

    public function get (String $key) {
        if ($this->has($key)) {
            $meta = $this->meta[$key];
            $meta->touches++;
            $meta->touched_at = $this->touchCount;
            $this->touchCount++;
            return $this->items[$key];
        } else {
            return null;
        }
    }

    private function evict () {
        // TODO: Holy cows, Batman. This is not even slightly efficient
        $removed = 0;
        foreach ($this->meta as $key => $meta) {
            if ($meta->touches < 2) {
                unset($this->meta[$key]);
                unset($this->items[$key]);
                $removed++;
                $this->size--;
            }
            if ($removed >= $this->evictSize) {
                break;
            }
        }
    }

}