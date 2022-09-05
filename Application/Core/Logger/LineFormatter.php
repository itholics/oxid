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

namespace ITholics\Oxid\Application\Core\Logger;

use ITholics\Oxid\Application\Shared\InstanceTrait;
use JsonException;
use function json_encode;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Custom line formatter to set line breaks und context ignoring by default on true.
 * Also adding option to format the context in beautiful JSON or add other JSON options.
 *
 * @method static LineFormatter getInstance($format = null, $dateFormat = null, $allowInlineLineBreaks = true, $ignoreEmptyContextAndExtra = true)
 */
class LineFormatter extends \Monolog\Formatter\LineFormatter
{
    use InstanceTrait;
    protected int $jsonOptions = 0;
    
    public function __construct($format = null, $dateFormat = null, $allowInlineLineBreaks = true, $ignoreEmptyContextAndExtra = true)
    {
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }
    
    /**
     * Add extra json options
     *
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
    
    /**
     * Return the JSON representation of a value
     *
     * @param mixed $data
     * @param bool  $ignoreErrors
     *
     * @return string
     * @throws \RuntimeException if encoding fails and errors are not ignored
     */
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
    
    /**
     * @override
     *
     * @param mixed $data
     *
     * @return string|false JSON encoded data or null on failure
     * @throws \JsonException
     */
    protected function myEncode($data)
    {
        return json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | $this->jsonOptions);
    }
}