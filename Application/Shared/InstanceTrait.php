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