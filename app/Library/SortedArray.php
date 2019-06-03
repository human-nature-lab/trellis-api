<?php

namespace App\Library;

function binarySearchIndex ($arr, $value) {
    if (!count($arr)) return 0;

}

class SortedArray {
    protected $vals = [];
    private $valueExtractor;

    public function  __construct (callable $valueExtractor = null) {
        $this->valueExtractor = $valueExtractor;
    }

    public function add ($obj) {
        $index = binarySearchIndex($arr, isset($this->valueExtractor) ? $this->valueExtractor : $obj);
        return $this;
    }
}