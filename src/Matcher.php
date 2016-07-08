<?php namespace Mascame\Katina;

/**
 * This class is completely based on https://github.com/ptrofimov/matchmaker (Petr Trofimov)
 * 
 * Class Matcher
 * @package Mascame\Katina
 */
class Matcher
{
    /**
     * Returns true if $value matches $pattern
     *
     * @param $value
     * @param string $pattern
     *
     * @return bool
     *
     * Fix inline regex attempt: https://github.com/DannyBen/matchmaker/blob/3d0864426f7ee9c081c17ad09abb9272e5ecd6f2/src/matcher.php
     * (Does not pass the tests)
     *
     * @see https://github.com/ptrofimov/matchmaker - ultra-fresh PHP matching functions
     * @author Petr Trofimov <petrofimov@yandex.ru>
     */
    protected static function matcher($value, $pattern)
    {
        $args = [];
        if (($p = ltrim($pattern, ':')) != $pattern) foreach (explode(' ', $p) as $name) {
            if (substr($name, -1) == ')') {
                list($name, $args) = explode('(', $name);
                $args = explode(',', rtrim($args, ')'));
            }
            if (is_callable(Rules::get($name))) {
                if (!call_user_func_array(Rules::get($name), array_merge([$value], $args))) {
                    return false;
                }
            } elseif (Rules::get($name) !== $value) {
                return false;
            }
        } else {
            return $pattern === '' || $value === $pattern;
        }

        return true;
    }

    /**
     * Returns true if $value matches $pattern
     *
     * @param $value
     * @param $pattern
     *
     * @return bool
     *
     * @see https://github.com/ptrofimov/matchmaker - ultra-fresh PHP matching functions
     * @author Petr Trofimov <petrofimov@yandex.ru>
     */
    public static function matches($value, $pattern)
    {
        if (is_array($pattern)) {
            if (!is_array($value) && !$value instanceof \Traversable) {
                return false;
            }

            $keyMatcher = self::key_matcher($pattern);

            foreach ($value as $key => $item) {
                if (! $keyMatcher($key, $item)) return false;
            }

            if (! $keyMatcher()) return false;

            return true;
        }

        return self::matcher($value, $pattern);
    }

    /**
     * Returns matcher closure by $pattern
     *
     * @param array $pattern
     *
     * @return \Closure
     *
     * @see https://github.com/ptrofimov/matchmaker - ultra-fresh PHP matching functions
     * @author Petr Trofimov <petrofimov@yandex.ru>
     */
    protected static function key_matcher(array $pattern)
    {
        $keys = [];
        foreach ($pattern as $k => $v) {
            $chars = ['?' => [0, 1], '*' => [0, PHP_INT_MAX], '!' => [1, 1]];
            if (isset($chars[$last = substr($k, -1)])) {
                $keys[$k = substr($k, 0, -1)] = $chars[$last];
            } elseif ($last == '}') {
                list($k, $range) = explode('{', $k);
                $range = explode(',', rtrim($range, '}'));
                $keys[$k] = count($range) == 1
                    ? [$range[0], $range[0]]
                    : [$range[0] === '' ? 0 : $range[0], $range[1] === '' ? PHP_INT_MAX : $range[1]];
            } else {
                $keys[$k] = $chars[$k[0] == ':' ? '*' : '!'];
            }
            array_push($keys[$k], $v, 0);
        }

        return function ($key = null, $value = null) use (&$keys) {
            if (is_null($key)) foreach ($keys as $count) {
                if ($count[3] < $count[0] || $count[3] > $count[1]) return false;
            } else foreach ($keys as $k => &$count) if (self::matcher($key, $k)) {
                if (!self::matches($value, $count[2])) return false;
                $count[3]++;
            }
            return true;
        };
    }
}