<?php
/**
 * This Software is the property of ITholics GmbH and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link          http://www.itholics.de
 * @copyright (C) ITholics GmbH 2011-2022
 * @author        ITholics GmbH <oxid@itholics.de>
 * @author        Gabriel Peleskei <gp@itholics.de>
 */

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