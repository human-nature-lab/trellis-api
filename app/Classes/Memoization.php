<?php

namespace App\Classes;

use Log;

class Memoization
{
    /**
     * A simple function to memoize simple functions with serializable arguments.
     * @param callable $callback - The function to memoize
     */
    public static function memoize(callable $callback){
        $memo = array();
        return function() use (&$memo, $callback){

            $arguments = func_get_args();
            $serialized = serialize($arguments);
            // Return already stored value if it exists
            if(array_key_exists($serialized, $memo)){
                return $memo[$serialized];
            }

            // Otherwise execute the callback and store the returned value
            $result = $callback(...$arguments);
            $memo[$serialized] = $result;
            return $result;

        };
    }

}