<?php

namespace ITholics\Oxid\Application\Core\Adapter;

use Doctrine\DBAL\Exception;
use ITholics\Oxid\Application\Core\Utils;
use OxidEsales\Eshop\Core\Registry;
use function is_int;
use function is_object;
use function is_string;
use function json_decode;
use function method_exists;
use const JSON_THROW_ON_ERROR;

/**
 * Helper to save configurations in SQL through JSON.
 *
 * - First overwrite the method {@see Config::getTableName()} to provide the correct name.
 * - Overwrite the method {@see Config::set()} to provide custom setters. You may just return false, to let the default handler handle it.
 * - Add {@see Config::onActivate()} to your module's onActivate event like: Config::getInstance()->onActivate();
 * - The getter works over magic getter or array access:
 *      - $config->my_info or $config['my_info'] returns the value behind it or NULL on miss.
 * - You may add methods with the same naming as the variable you want to fetch:
 *      - public function my_info() { return 'bar'; }
 *      - calling $config->my_info or $config['my_info'] will call $config->my_info() internally.
 *      - this is useful if you want to process something
 * - Setters work that, too:
 *      - $config->my_info = 'bar' or $config['my_info'] = 'bar' to assign the value.
 *      - You may also add a method to process it.
 *
 */
abstract class Config implements \ArrayAccess, \JsonSerializable
{
    protected static array $__instance = [];
    
    /**
     * Table name to use. It will be automatically created if {@see Config::onActivate()} is added to onActivate event of module.
     * @return string
     */
    abstract protected function getTableName(): string;
    /**
     * Options holder serialized to JSON.
     * @var array
     */
    protected array $__config = [];
    protected ?int  $shopId;
    
    /**
     * Initializes data.
     */
    public function __construct(?int $shopId = null)
    {
        $this->shopId = $shopId ?? Registry::getConfig()->getShopId();
        $this->init();
    }
    
    /**
     * @param int|null $shopId
     *
     * @return $this
     */
    public static function getInstance(?int $shopId = null)
    {
        $shopId = $shopId ?? Registry::getConfig()->getShopId();
        if (!isset(static::$__instance[static::class][$shopId])) {
            if (!isset(static::$__instance[static::class])) {
                static::$__instance[static::class] = [];
            }
            static::$__instance[static::class][$shopId] = oxNew(static::class, $shopId);
        }
        return static::$__instance[static::class][$shopId];
    }
    
    /**
     * Set the shop id to use. If id is new the {@see Config::init()} will be called.
     * If an id existed before, {@see Config::save()} is executed before the init.
     *
     * @param int|null $shopId
     *
     * @return $this
     */
    public function withShopId(?int $shopId = null)
    {
        $id = $shopId ?? Registry::getConfig()->getShopId();
        if ($id !== $this->shopId) {
            if (null !== $this->shopId) {
                $this->save();
            }
            $this->init();
        }
        return $this;
    }
    
    /**
     * Initializes data: loads config from database.
     * @return void
     */
    public function init(): void
    {
        try {
            $config           = Utils::getInstance()->getDb()->fetchOne("SELECT `config` FROM `{$this->getTableName()}` WHERE `shop_id` = ?", [$this->shopId]);
            if (!$config) {
                Registry::getLogger()->warning('No data received from DB', ['shopId' => $this->shopId]);
            } else {
                $this->__config = json_decode($config, true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (\Throwable $e) {
            $this->__config = [];
            Registry::getLogger()->error('Failed to initialize config from JSON', [$e, 'shopId' => $this->shopId]);
        }
    }
    
    /**
     * Needs to be executed to persist changes.
     * @return void
     */
    public function save(): void
    {
        try {
            $config = json_encode($this->__config, JSON_THROW_ON_ERROR);
            Utils::getInstance()->getDb()->executeStatement("INSERT INTO `{$this->getTableName()}` (`shop_id`, `config`) VALUES (?, '$config') ON DUPLICATE KEY UPDATE config=VALUES(config)", [$this->shopId]);
        } catch (\Throwable $e) {
            Registry::getLogger()->error('Failed to save config as JSON to db', [$e, 'shopId' => $this->shopId]);
        }
    }
    
    public function assign($source): void
    {
        if (is_object($source)) {
            $source = (array)$source;
        }
        if (is_array($source)) {
            foreach ($source as $index => $value) {
                $this->{$index} = $value;
            }
        }
    }
    
    /**
     * Add this method to your onActivate event in your module.
     * - Config::getInstance()->onActivate();
     *
     * @return void
     * @throws Exception
     */
    public function onActivate(): void
    {
        Utils::getInstance()->getDb()->executeStatement(
            "CREATE TABLE IF NOT EXISTS `{$this->getTableName()}` (
                `shop_id` INT(11) NOT NULL,
                `config` JSON NULL DEFAULT NULL,
                PRIMARY KEY (`shop_id`) USING BTREE
            )
            COLLATE='utf8_general_ci'
            ENGINE=InnoDB"
        );
    }
    
    /**
     * Direct value accessor with fallback mechanism.
     *
     * @param string $name
     * @param        $fallback
     * @param bool   $strict
     *
     * @return mixed|null
     */
    public function _(string $name, $fallback = null, bool $strict = false)
    {
        $value = $this->__config[$name] ?? null;
        if ($strict) {
            return $value ?? $fallback;
        }
        return $value ?: $fallback;
    }
    
    /**
     * Extract the language abbreviation of a given $language (f.e.: 'de' or 0).
     * NULL returns the current one used by oxid.
     *
     * @param null|string|int $language
     *
     * @return string
     */
    protected function getLanguageAbbr($language = null): string
    {
        if (is_int($language)) {
            return Registry::getLang()->getLanguageAbbr($language);
        }
        if (null === $language) {
            return Registry::getLang()->getLanguageAbbr();
        }
        return (string)$language;
    }
    
    /**
     * Helper function to set language value
     *
     * @param string          $index
     * @param                 $value
     * @param null|string|int $language look at {@see Config::getLanguageAbbr()}
     *
     * @return void
     */
    protected function handleLanguage(string $index, $value, $language = null): void
    {
        if (is_array($value)) {
            $this->__config[$index] = $value;
        } elseif (is_string($value)) {
            $abbr = $this->getLanguageAbbr($language);
            if (!isset($this->__config[$index])) {
                $this->__config[$index] = [$abbr => $value];
            } else {
                $this->__config[$index][$abbr] = $value;
            }
        }
    }
    
    /**
     * @param string $name
     * @param        $value
     * @param null   $language look at {@see Config::getLanguageAbbr()}
     *
     * @return bool return false to let default handler work
     */
    abstract protected function set(string $name, $value, $language = null): bool;
    
    public function setRaw(string $name, $value): void
    {
        $this->__config[$name] = $value;
    }
    
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}();
        }
        return $this->__config[$name] ?? null;
    }
    
    public function __unset($name)
    {
        unset($this->__config[$name]);
    }
    
    public function __isset($name)
    {
        return isset($this->__config[$name]);
    }
    
    public function __set($name, $value)
    {
        if (false === $this->set($name, $value)) {
            $fnc = "set_$name";
            if (method_exists($this, $fnc)) {
                $this->{$fnc}($value);
            } else {
                $this->__config[$name] = $value;
            }
        }
    }
    
    /**
     * Retrieve value of language field.
     *
     * @param string          $name
     * @param string|int|null $language look at {@see Config::getLanguageAbbr()}
     *
     * @return string|null
     */
    public function getValueOf(string $name, $language = null): ?string
    {
        return $this->__config[$name][$this->getLanguageAbbr($language)] ?? null;
    }
    
    /**
     * Set value of language field.
     *
     * @param string          $name
     * @param mixed           $value
     * @param string|int|null $language look at {@see Config::getLanguageAbbr()}
     *
     * @return void
     */
    public function setValueOf(string $name, $value, $language = null): void
    {
        if (!is_array($this->__config[$name] ?? null)) {
            return;
        }
        $this->__config[$name][$this->getLanguageAbbr($language)] = $value;
    }
    
   
    
    public function offsetExists($offset)
    {
        return isset($this->__config[$offset]);
    }
    
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }
    
    public function offsetSet($offset, $value)
    {
        $this->{$name} = $value;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->__config[$offset]);
    }
    
    public function jsonSerialize()
    {
        return $this->__config;
    }
}