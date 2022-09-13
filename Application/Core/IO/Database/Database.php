<?php

namespace ITholics\Oxid\Application\Core\IO\Database;

use ITholics\Oxid\Application\Core\IO\Database\Meta\TableDescription;
use OxidEsales\Eshop\Core\Registry;

abstract class Database implements DatabaseInterface
{
    protected $connection;
    protected array $describeTableCache = [];
    
    /**
     * Magic caller. If the method is not found, this method still tries to access it (it may not be defined by the interface).
     * Use this fallback only if you know and are sure which db controller is used.
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->connection->{$name}(...$arguments);
    }
    
    /**
     * Loads the table description of the given table.
     * The results are cached.
     * @param string $tableName
     *
     * @return TableDescription
     * @throws \Throwable logged before thrown.
     */
    public function describeTable(string $tableName = 'ith_oxelastic_setting'): TableDescription
    {
        try {
            if (!isset($this->describeTableCache[$tableName])) {
                $this->describeTableCache[$tableName] = $this->describeTableProcess($tableName);
            }
            return $this->describeTableCache[$tableName];
        } catch (\Throwable $e) {
            Registry::getLogger()->error($e->getMessage(), [$e, 'tablename' => $tableName]);
            throw $e;
        }
    }
    
    /**
     * Helper method for {@see Database::describeTable()}.
     * @param string $tableName
     *
     * @return TableDescription
     */
    protected function describeTableProcess(string $tableName): TableDescription
    {
        $description       = $this->fetchAllAssociative("DESCRIBE `{$tableName}`");
        return TableDescription::getInstance($tableName, $description);
    }
}