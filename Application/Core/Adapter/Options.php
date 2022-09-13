<?php

namespace ITholics\Oxid\Application\Core\Adapter;

use ITholics\Oxid\Application\Shared\StaticInstanceTrait;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;

/**
 * You can use this blueprint to access your options. You may also extend it to your needs.
 * Look at {@see Options::_()} how this all works.
 * The corresponding $name can be requested over array or object access like:
 *
 *  $opt = Options::getInstance();
 *  $opt->test      == $opt['test']     == $opt->_('test')
 *  $opt->__test    == $opt['__test']   == $opt->('test', Module::ID)
 *  $opt->id__test  == $opt['id__test'] == $opt->('test', 'id')
 */
abstract class Options implements \ArrayAccess
{
    use StaticInstanceTrait;
    
    /**
     * @var ModuleSettingBridgeInterface
     */
    protected $bridge;
    
    public function __construct()
    {
        $this->bridge = ContainerFactory::getInstance()->getContainer()->get(ModuleSettingBridgeInterface::class);
    }
    
    abstract public function getModuleId(): string;
    
    /**
     * Return the config value of a module/shop.
     * If your name starts with '__' (double underscore) the rest will be used as name and the module id will be taken from {@see Options::getModuleId()}.
     * If you seperate the module id and the options variable with '__' (double underscore) it will use the left side as the module id and the right as the variable.
     * Otherwise {@see Config::getConfigParam()} will be used.
     *
     * @param string      $name
     * @param string|null $moduleId
     *
     * @return mixed|null null if not found
     */
    public function _(string $name, ?string $moduleId = null)
    {
        if (0 === strpos($name, '__')) {
            [, $name] = explode('__', $name, 2);
            $moduleId = $this->getModuleId();
        } else {
            if (strpos($name, '__') !== false) {
                [$moduleId, $name] = explode('__', $name, 2);
            }
        }
        if (null !== $moduleId) {
            return $this->bridge->get($name, $moduleId);
        }
        return Registry::getConfig()->getConfigParam($name);
    }
    
    public function __get($name)
    {
        return $this->_($name);
    }
    
    protected function processInternalbeforeSave(string $name, $value, ?string $moduleId = null)
    {
        return $value;
    }
    
    public function __set($name, $value)
    {
        $pos = strpos($name, '__');
        if (false !== $pos) {
            
            if ($pos === 0) {
                $name = substr($name, 2);
                $id = $this->getModuleId();
            } else {
                [$name, $id] = explode('__', $name, 2);
            }
            $newValue = $this->processInternalbeforeSave($name, $value, $id);
            $this->bridge->save($name, $newValue, $id);
        }
    }
    
    public function offsetGet($offset)
    {
        return $this->_($offset);
    }
    public function offsetExists($offset)
    {
        return null !== $this->_($offset);
    }
    public function offsetSet($offset, $value)
    {
        // do nothing
    }
    public function offsetUnset($offset)
    {
        // do nothing
    }
}