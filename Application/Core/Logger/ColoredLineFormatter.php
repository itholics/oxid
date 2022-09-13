<?php

namespace ITholics\Oxid\Application\Core\Logger;

use ITholics\Oxid\Application\Shared\InstanceTrait;
use function json_encode;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * @method static ColoredLineFormatter getInstance($colorScheme = null, $format = null, $dateFormat = null, $allowInlineLineBreaks = true, $ignoreEmptyContextAndExtra = true)
 */
class ColoredLineFormatter extends \Bramus\Monolog\Formatter\ColoredLineFormatter
{
    use InstanceTrait;
    protected int $jsonOptions = 0;
    
    public function __construct($colorScheme = null, $format = null, $dateFormat = null, $allowInlineLineBreaks = true, $ignoreEmptyContextAndExtra = true)
    {
        parent::__construct($colorScheme, $format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }
    
    /**
     * @param int $options
     *
     * @return $this
     */
    public function withJsonOptions(int $options)
    {
        $this->jsonOptions = $options;
        return $this;
    }
    
    /**
     * Return the options based on the PHP version
     * @return int
     */
    public function getJsonOptions(): int
    {
        return JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | $this->jsonOptions;
    }
    
    protected function toJson($data, $ignoreErrors = false)
    {
        // suppress json_encode errors since it's twitchy with some inputs
        if ($ignoreErrors) {
            try {
                return $this->myEncode($data);
            } catch (JsonException $e) {
                return 'null';
            }
        }
        try {
            return $this->myEncode($data);
        } catch (JsonException $e) {
            return parent::toJson($data, $ignoreErrors);
        }
    }
    
    protected function myEncode($data)
    {
        return json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | $this->jsonOptions);
    }
}