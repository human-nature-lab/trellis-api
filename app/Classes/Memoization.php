<?php

namespace App\Classes;

class Memoization
{

    private static function _memoize ($memo, callable $callback, callable $serializer = null) {
        return function() use ($callback, $memo, $serializer){

            $arguments = func_get_args();

            $key = isset($serializer) ? call_user_func_array($serializer, $arguments) :  serialize($arguments);

            // Return already stored value if it exists
            if($key && $memo->has($key)){
                return $memo->get($key);
            }

            // Otherwise execute the callback and store the returned value
            $result = call_user_func_array($callback, $arguments);
            $memo->set($key, $result);
            return $result;

        };
    }

    /**
     * A method to memoize simple functions with serializable arguments or complex functions with custom serialization
     * @param callable $callback - The function to memoize
     * @param callable [$serializer] - An optional custom serialization function
     * @return callable
     */
    public static function memoize(callable $callback, callable $serializer = null){

        return self::_memoize(new Map(), $callback, $serializer);

    }

    /**
     * Same as the memoize method, but with a limit on the number of records that are stored at a time
     * @param callable $callback
     * @param int $maxRecords
     * @param callable|null $serializer
     * @return \Closure
     */
    public static function memoizeMax (callable $callback, $maxRecords = 100, callable $serializer = null) {

        return self::_memoize(new LengthLimitedMap($maxRecords), $callback, $serializer);

    }

}