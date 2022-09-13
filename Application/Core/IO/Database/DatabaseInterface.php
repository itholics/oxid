<?php

namespace ITholics\Oxid\Application\Core\IO\Database;

use ITholics\Oxid\Application\Core\IO\Database\Meta\TableDescription;

interface DatabaseInterface
{
    /**
     * Load the table description
     * @param string $tableName
     *
     * @return TableDescription
     */
    public function describeTable(string $tableName): TableDescription;
    /**
     * Yields an associative arrow for each row.
     * @param string $query
     * @param array  $parameters
     * @param array  $types
     *
     * @return \Generator|array
     */
    public function iterateAssociative(string $query, array $parameters = [], array $types = []): \Generator;
    /**
     * Yields a numeric array for each row.
     * @param string $query
     * @param array  $parameters
     * @param array  $types
     *
     * @return \Generator
     */
    public function iterateNumeric(string $query, array $parameters = [], array $types = []): \Generator;
    /**
     * Returns the full dataset with each row as associative array.
     * @param string $query
     * @param array  $parameters
     * @param array  $types
     *
     * @return array
     */
    public function fetchAllAssociative(string $query, array $parameters = [], array $types = []): array;
    /**
     * Returns the full dataset with each row as numeric array.
     * @param string $query
     * @param array  $parameters
     * @param array  $types
     *
     * @return array
     */
    public function fetchAllNumeric(string $query, array $parameters = [], array $types = []): array;
    /**
     * Returns the first value of the first column.
     * @param string $query
     * @param array  $params
     * @param array  $types
     *
     * @return mixed
     */
    public function fetchOne(string $query, array $params = [], array $types = []);
    /**
     * Returns the values of the first colum.
     * @param string $query
     * @param array  $params
     * @param array  $types
     *
     * @return array
     */
    public function fetchFirstColumn(string $query, array $params = [], array $types = []): array;
    /**
     * Retrieves the first row as associative array
     * @param string $query
     * @param array  $params
     * @param array  $types
     *
     * @return array|null
     */
    public function fetchAssociative(string $query, array $params = [], array $types = []): ?array;
    /**
     * Retrieves the first row as numeric array
     * @param string $query
     * @param array  $params
     * @param array  $types
     *
     * @return array|null
     */
    public function fetchNumeric(string $query, array $params = [], array $types = []): ?array;
    /**
     * Executes a statement and returns the affected rows.
     * @param string $query
     * @param array  $params
     * @param array  $types
     *
     * @return int
     */
    public function executeStatement(string $query, array $params = [], array $types = []): int;
    /**
     * Quotes a value.
     * @param $what
     * @param ...$args
     *
     * @return mixed
     */
    public function quote($what, ...$args);
    /**
     * Quotes all values in an array.
     * @param array $values
     * @param       ...$args
     *
     * @return array
     */
    public function quoteArray(array $values, ...$args): array;
    /**
     * Retrieve the last inserted id, this may work falsey depending on database und driver.
     * @param $name
     *
     * @return mixed
     */
    public function lastInsertId($name = null);
}