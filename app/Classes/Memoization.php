<?php

namespace App\Classes;

class Memoization
{
    /**
     * A simple function to memoize simple functions with serializable arguments.
     * @param callable $callback - The function to memoize
     */
    public static function memoize(callable $callback){

        return function() use ($callback){

            $memo = [];
            $arguments = func_get_args();
            $serialized = md5(serialize($arguments));
            // Return already stored value if it exists
            if(isset($memo[$serialized])){
                return $memo[$serialized];
            }

            // Otherwise execute the callback and store the returned value
            $result = call_user_func_array($callback, $arguments);
            $memo[$serialized] = $result;
            return $result;

        };
    }

}