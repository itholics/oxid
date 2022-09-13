<?php

namespace ITholics\Oxid\Application\Core\IO\Database;

use ITholics\Oxid\Application\Shared\StaticInstanceTrait;
use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * @method static LegacyDatabase getInstance()
 */
class LegacyDatabase extends Database
{
    use StaticInstanceTrait;
    /**
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    public function __construct()
    {
        $this->connection = DatabaseProvider::getDb();
    }
    
    /**
     * @param string $query
     * @param array  $parameters
     * @param array  $types
     *
     * @return \Generator|array
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function iterateAssociative(string $query, array $parameters = [], array $types = []): \Generator
    {
        $this->connection->setFetchMode($this->connection::FETCH_MODE_ASSOC);
        $result = $this->connection->select($query, $parameters);
        while (!$result->EOF) {
            yield $result->getFields();
            $result->fetchRow();
        }
    }
    
    public function iterateNumeric(string $query, array $parameters = [], array $types = []): \Generator
    {
        $this->connection->setFetchMode($this->connection::FETCH_MODE_NUM);
        $result = $this->connection->select($query, $parameters);
        while (!$result->EOF) {
            yield $result->getFields();
            $result->fetchRow();
        }
    }
    
    public function fetchAllAssociative(string $query, array $parameters = [], array $types = []): array
    {
        $this->connection->setFetchMode($this->connection::FETCH_MODE_ASSOC);
        $result = $this->connection->select($query, $parameters);
        return $result->fetchAll();
    }
    
    public function fetchAllNumeric(string $query, array $parameters = [], array $types = []): array
    {
        $this->connection->setFetchMode($this->connection::FETCH_MODE_NUM);
        return $this->connection->select($query, $parameters)->fetchAll();
    }
    
    public function fetchOne(string $query, array $params = [], array $types = [])
    {
        $result = $this->connection->getOne($query, $params);
        if (false === $result) {
            return null;
        }
        return $result;
    }
    
    public function fetchFirstColumn(string $query, array $params = [], array $types = []): array
    {
        return $this->connection->getCol($query, $params);
    }
    
    public function fetchAssociative(string $query, array $params = [], array $types = []): ?array
    {
        $this->connection->setFetchMode($this->connection::FETCH_MODE_ASSOC);
        return $this->connection->getRow($query, $params) ?: null;
    }
    
    public function fetchNumeric(string $query, array $params = [], array $types = []): ?array
    {
        $this->connection->setFetchMode($this->connection::FETCH_MODE_NUM);
        return $this->connection->getRow($query, $params) ?: null;
    }
    
    public function executeStatement(string $query, array $params = [], array $types = []): int
    {
        return $this->connection->execute($query, $params);
    }
    
    public function quote($what, ...$args)
    {
        $value = $this->connection->quote($what);
        if (false === $value) {
            throw new \UnexpectedValueException(sprintf('%s() > Failed to quote `%s`', __METHOD__, $args[0] ?? $what));
        }
        return $value;
    }
    
    public function quoteArray(array $values, ...$args): array
    {
        $items = [];
        foreach ($values as $value) {
            $items[] = $this->quote($value, ...$args);
        }
        return $items;
    }
    
    public function lastInsertId($name = null)
    {
        return $this->connection->getLastInsertId();
    }
}