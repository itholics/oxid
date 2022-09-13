<?php

namespace ITholics\Oxid\Application\Core\IO\Database;

use Doctrine\DBAL\Connection;
use ITholics\Oxid\Application\Shared\StaticInstanceTrait;

/**
 * Loads the {@see DatabaseInterface correct wrapper}.
 *
 * @example
 *      $db = Provider::getInstance()->get();
 *      // or
 *      $db = Provicer::getInstance()();
 *      // or even
 *      $db = Provider::getInstance(); // using magic calls to enter the methods beneath
 *      // or via oxNew()
 *      $db = oxNew(Provider::class)->get(); // oxNew is used in ::getInstance() wrapped as singleton, meaning: you should use ::getInstance()
 *
 *
 * @method static Provider getInstance()
 */
class Provider
{
    use StaticInstanceTrait;
    
    protected $db;
    
    /**
     *
     */
    public function __construct()
    {
        if (\class_exists(Connection::class)) {
            $this->db = DoctrineDatabase::getInstance();
        } else {
            $this->db = LegacyDatabase::getInstance();
        }
    }
    
    /**
     * @return DatabaseInterface
     * @see Provider::get()
     */
    public function __invoke()
    {
        return $this->db;
    }
    
    /**
     * Retrieves the underlying database interface
     * @return DatabaseInterface
     */
    public function get()
    {
        return $this->db;
    }
    
    /**
     * Calling underlying methods though magic calls.
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->db->{$name}(...$arguments);
    }
}