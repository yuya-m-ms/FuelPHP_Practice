<?php

/**
 * Naughton pattern Trait
 * cf. Singleton, Multiton
 */
trait Trait_Naughton
{
    protected static $properties = [];

    public static function get($property)
    {
        return isset(static::$properties[$property]) ? static::$properties[$property] : null;
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