<?php

namespace ITholics\Oxid\Application\Shared;

/**
 * It is like {@see InstanceTrait::getInstance()} but in a static context.
 * The same class won't be instatiated twice (singleton pattern).
 *
 * If added, add a class comment with the (at)method annotation to allow your IDE better support
 */
trait StaticInstanceTrait
{
    /**
     * Instance holder. We need to use a class-based indexing because of some inheritance problems, that may appear.
     * @var array
     */
    protected static array $__instance = [];
    
    public static function getInstance(... $args)
    {
        return static::$__instance[static::class] ?? (static::$__instance[static::class] = oxNew(static::class, ... $args));
    }
}