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

namespace ITholics\Oxid\Application\Exception;

use ITholics\Oxid\Application\Core\Utils;
use ITholics\Oxid\Application\Shared\InstanceTrait;

/**
 * Base exception to use for your projects.
 * It adds 2 features:
 * - {@see Exception::withMethod() contextual method}
 * - {@see Exception::withInternalCode() contextual internal code}
 *
 * This method uses the {@see InstanceTrait}. Thus containing a (at)method decleration.
 * This decleration should be updated in each inheritance for better IDE support.
 *
 * @method static $this getInstance($message = "", $code = 0, \Throwable $previous = null)
 */
class Exception extends \Exception
{
    use InstanceTrait;
    
    protected ?int $internalCode;
    protected ?string $method;
    
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->withMethod();
    }
    
    /**
     * Allows setting a method name. The idea behind it is to have fast access to the method, that has thrown the exception.
     *
     * @param string|null $method if null it will use {@see Utils::getCallee()} to retrieve the method where the exception was created in. This is called with the constructor by default.
     *
     * @return $this
     */
    public function withMethod(?string $method = null)
    {
        if (null === $method) {
            $method = Utils::getInstance()->getCallee(2);
        }
        $this->method = $method;
        return $this;
    }
    
    /**
     * An internal code allows you to find the point of error realy fast.
     * You need to track/increment the codes you use, but than, each code is unique and can be found fast.
     * @param int|null $code
     *
     * @return $this
     */
    public function withInternalCode(?int $code)
    {
        $this->internalCode = $code;
        return $this;
    }
    
    /**
     * Retrieve contextual method.
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }
    
    /**
     * Retrieve contextual internal code.
     * @return int|null
     */
    public function getInternalCode(): ?int
    {
        return $this->internalCode;
    }
    
    /**
     * Throw this exception. Allows fluid call of the throw.
     * @return void
     * @throws $this
     */
    public function throws()
    {
        throw $this;
    }
    
}