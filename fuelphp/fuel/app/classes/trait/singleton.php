<?php

/**
 * Singleton Trait
 */
trait Trait_Singleton
{
    protected static $instance;

    public static final function get_instance()
    {
        if ( ! isset(static::$instance)) {
            $class = get_called_class();
            static::$instance = new $class;
        }
        return static::$instance;
    }

    protected function __construct() {}

    public final function __clone()
    {
        throw new Exception('Clone is not allowed for '.get_class($this).' as a Singleton');
    }
}