<?php

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
        $uri = ' ';
        if ($url = Url::getRequestInstance()->url) {
            $uri = sprintf("\n\t\t>> URL=\"%s\"\n\t\t", $url);
        }
        return parent::addRecord($level, sprintf("\n\t[ %s ]%s>> %s", $callee, $uri, $message), $context);
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