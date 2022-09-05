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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Allows adding logger to class.
 *
 * Logger is accessable through {@see LoggerTrait::$logger protected property $logger}
 */
trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * Set the logger.
     * @param LoggerInterface|null $logger
     *
     * @return $this
     */
    public function withLogger(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
        return $this;
    }
}