<?php

if (!function_exists('array_uunique')) {
    /**
     *
     * @param array $array
     * @param callable $comparator
     * @return array
     *
     * @see https://www.php.net/manual/ru/function.array-unique.php#122857
     */
    function array_uunique(array $array, callable $comparator): array
    {
        $unique_array = [];
        do {
            $element = array_shift($array);
            $unique_array[] = $element;

            $array = array_udiff(
                $array,
                [$element],
                $comparator
            );
        } while (count($array) > 0);

        return $unique_array;
    }
}
