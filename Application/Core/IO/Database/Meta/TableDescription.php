<?php

namespace ITholics\Oxid\Application\Core\IO\Database\Meta;

use ITholics\Oxid\Application\Shared\InstanceTrait;

/**
 * @method static TableDescription getInstance(string $tableName, array $desciptionContainer)
 */
class TableDescription implements \JsonSerializable, \ArrayAccess, \Countable
{
    use InstanceTrait;
    
    protected string $tableName;
    protected array  $data = [];
    
    /**
     * @param string $tableName
     * @param array  $description
     *
     */
    public function __construct(string $tableName, array $description)
    {
        $this->tableName = $tableName;
        $this->init($description);
    }
    
    /**
     * Lookup if column of table exists.
     *
     * @param string $column
     *
     * @return bool
     */
    public function has(string $column): bool
    {
        return isset($this->data[\strtolower($column)]);
    }
    
    public function get(string $column): ?TableFieldDescription
    {
        return $this->data[\strtolower($column)] ?? null;
    }
    
    /**
     * @param array $desciptionContainer
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function init(array $desciptionContainer): void
    {
        $this->data = [];
        foreach ($desciptionContainer as $description) {
            $description                       = TableFieldDescription::getInstance($description);
            $this->data[$description->index()] = $description;
        }
    }
    
    public function jsonSerialize(): array
    {
        return ['__tableName' => $this->tableName] + $this->data;
    }
    
    public function __debugInfo()
    {
        return ['__tableName' => $this->tableName] + $this->data;
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }
    
    public function __isset($name)
    {
        return $this->has($name);
    }
    
    public function __set($name, $value)
    {
        // noop
    }
    
    public function __unset($name)
    {
        // noop
    }
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    public function offsetSet($offset, $value)
    {
        // noop
    }
    public function offsetUnset($offset)
    {
        // noop
    }
    
    /**
     * @return int number of columns in table
     */
    public function count(): int
    {
        return count($this->data);
    }
}