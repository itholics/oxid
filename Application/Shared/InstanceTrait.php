<?php

namespace ITholics\Oxid\Application\Shared;

use function oxNew;

/**
 * Use this trait to add instantiation capabilities over {@see oxNew()}.
 *
 * If added, add a class comment with the (at)method annotation to allow your IDE better support
 */
trait InstanceTrait
{
    /**
     * Returns an object instantiated over {@see oxNew()}
     * @param ...$args
     *
     * @return InstanceTrait|$this
     * @uses oxNew
     */
    public static function getInstance(...$args)
    {
        return oxNew(static::class, ...$args);
    }
}