<?php

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