<?php

/**
 * Naughton Trait
 * cf. Singleton, Multiton
 */
trait Trait_Naughton
{
    protected static $properties = [];

    public static function get($property)
    {
        return static::$properties[$property];
    }

    public static function set($property, $value)
    {
        return static::$properties[$property] = $value;
    }

    public final function __construct()
    {
        throw new Exception('Static only');
    }

    public final function __clone()
    {
        throw new Exception('Static only');
    }
}