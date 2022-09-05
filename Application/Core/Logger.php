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

namespace ITholics\Oxid\Application\Core;

use ITholics\Oxid\Application\Shared\InstanceTrait;
use function array_shift;
use function sprintf;

/**
 * Monolog logger extension.
 *
 * Features:
 * - adding {@see Logger::addRecord() custom record formating}
 *
 * @method static Logger getInstance($name, array $handlers = [], array $processors = [])
 */
class Logger extends \Monolog\Logger
{
    use InstanceTrait;
    
    protected Utils $utils;
    
    public function __construct($name, array $handlers = [], array $processors = [])
    {
        parent::__construct($name, $handlers, $processors);
        $this->utils = Utils::getInstance();
    }
    
    /**
     * Change how the message is presented (contents)
     * @override
     *
     * @param       $level
     * @param       $message
     * @param array $context
     *
     * @return bool
     */
    public function addRecord($level, $message, array $context = [])
    {
        $callee = $this->utils->getCallee(2);
        if ($ex = ($context[0] ?? null) and $ex instanceof \Throwable) {
            array_shift($context);
            $ex      = $this->formatException($ex);
            $message = $message ? "*{$message}* " . $ex : $ex;
        }
        return parent::addRecord($level, sprintf("\n\t[ %s ] >> %s", $callee, $message), $context);
    }
    
    /**
     * Exception formatter.
     * @param \Throwable $e
     *
     * @return string
     */
    public function formatException(\Throwable $e): string
    {
        return $this->utils->formatException($e);
    }
}