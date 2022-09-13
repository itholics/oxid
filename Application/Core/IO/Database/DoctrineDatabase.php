<?php

namespace ITholics\Oxid\Application\Core\IO\Database;

use Doctrine\DBAL\Connection;
use ITholics\Oxid\Application\Shared\StaticInstanceTrait;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactory;

/**
 * @method static DoctrineDatabase getInstance()
 */
class DoctrineDatabase extends Database
{
    use StaticInstanceTrait;
    
    /**
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function __construct()
    {
        if (\class_exists(ConnectionFactory::class)) {
            $this->connection = ConnectionFactory::get(Connection::class);
        } else {
            $this->connection = ContainerFactory::getInstance()->getContainer()->get(Connection::class);
        }
    }
    
    public function iterateAssociative(string $query, array $parameters = [], array $types = []): \Generator
    {
        yield from $this->connection->iterateAssociative($query, $parameters, $types);
    }
    
    public function iterateNumeric(string $query, array $parameters = [], array $types = []): \Generator
    {
        yield from $this->connection->iterateNumeric($query, $parameters, $types);
    }
    
    public function fetchAllAssociative(string $query, array $parameters = [], array $types = []): array
    {
        return $this->connection->fetchAllAssociative($query, $parameters, $types);
    }
    
    public function fetchAllNumeric(string $query, array $parameters = [], array $types = []): array
    {
        return $this->connection->fetchAllNumeric($query, $parameters, $types);
    }
    
    public function fetchOne(string $query, array $parameters = [], array $types = [])
    {
        return $this->connection->fetchOne($query, $parameters, $types);
    }
    
    public function fetchFirstColumn(string $query, array $parameters = [], array $types = []): array
    {
        return $this->connection->fetchFirstColumn($query, $parameters, $types);
    }
    
    public function fetchAssociative(string $query, array $parameters = [], array $types = []): array
    {
        return $this->connection->fetchAssociative($query, $parameters, $types);
    }
    
    public function fetchNumeric(string $query, array $parameters = [], array $types = []): array
    {
        return $this->connection->fetchNumeric($query, $parameters, $types);
    }
    
    public function executeStatement(string $query, array $parameters = [], array $types = []): int
    {
        return (int)$this->connection->executeStatement($query, $parameters, $types);
    }
    
    public function quote($what, ...$args)
    {
        return $this->connection->quote($what);
    }
    
    public function quoteArray(array $values, ...$args): array
    {
        $update = [];
        foreach ($values as $index => $value) {
            $update[$index] = $this->quote($value, ...$args);
        }
        return $update;
    }
    
    public function lastInsertId($name = null)
    {
        return $this->connection->lastInsertId($name);
    }
}