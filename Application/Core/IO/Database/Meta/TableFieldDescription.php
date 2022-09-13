<?php

namespace ITholics\Oxid\Application\Core\IO\Database\Meta;

use ITholics\Oxid\Application\Shared\InstanceTrait;

/**
 * @property-read string      $index
 * @property-read string      $name
 * @property-read string      $field
 * @property-read string      $fieldOrigin
 * @property-read string      $typeRaw
 * @property-read string      $type
 * @property-read int|null    $typeLength
 * @property-read bool        $unsigned
 * @property-read bool        $nullable
 * @property-read bool        $null
 * @property-read string|null $key
 * @property-read string      $default
 * @property-read string|null $extra
 *
 *
 * @method static TableFieldDescription getInstance(array $source)
 */
class TableFieldDescription implements \JsonSerializable, \ArrayAccess
{
    use InstanceTrait;
    
    public const REQUIRED_FIELDS = [
        'field',
        'fieldOrigin',
        'type',
        'null',
        'key',
        'default',
        'extra'
    ];
    
    protected array $data = [];
    
    /**
     * @return string field name (lowercased) aka field, used by {@see TableDescription} for indexing
     * @see TableFieldDescription::$index
     * @see TableFieldDescription::$name
     */
    public function index(): string
    {
        return $this->data['field'];
    }
    
    /**
     * @return string field name (lowercased)
     * @see TableFieldDescription::$field
     * @see TableFieldDescription::$name
     */
    public function field(): string
    {
        return $this->data['field'];
    }
    
    /**
     * @return string
     * @see TableFieldDescription::$field
     * @see TableFieldDescription::$index
     */
    public function name(): string
    {
        return $this->data['field'];
    }
    
    /**
     * @return string origin field name (case-sensitive)
     * @see TableFieldDescription::$fieldOrigin
     */
    public function fieldOrigin(): string
    {
        return $this->data['fieldOrigin'];
    }
    
    /**
     * @return string raw type definition by delivered by describe
     * @see TableFieldDescription::$typeRaw
     */
    public function typeRaw(): string
    {
        return $this->data['type'];
    }
    
    /**
     * @return string mysql type (like tinyint, timestamp, biginteger, ..)
     * @see TableFieldDescription::$type
     */
    public function type(): string
    {
        $match = [];
        preg_match('/\w+/', $this->data['type'], $match);
        return $match[0] ?? '';
    }
    
    /**
     * @return int|null the type length of mysql, null if not given, aka default
     * @see TableFieldDescription::$typeLength
     */
    public function typeLength(): ?int
    {
        $match = [];
        preg_match('/\((\d+)\)/', $this->data['type'], $match);
        return \filter_var($match[1] ?? null, \FILTER_VALIDATE_INT, \FILTER_NULL_ON_FAILURE);
    }
    
    /**
     * @return bool if value is unsigned
     * @see TableFieldDescription::$unsigned
     */
    public function unsigned(): bool
    {
        return preg_match('/unsigned$/i', $this->data['type']);
    }
    
    /**
     * @return bool if field is nullable
     * @see TableFieldDescription::nullable()
     * @see TableFieldDescription::$nullable
     */
    public function null(): bool
    {
        return $this->nullable();
    }
    
    /**
     * @return bool if field is nullable
     * @see TableFieldDescription::null()
     * @see TableFieldDescription::$nullable
     */
    public function nullable(): bool
    {
        return $this->data['null'] !== 'no';
    }
    
    /**
     * @return string|null
     * @see TableFieldDescription::$key
     */
    public function key(): ?string
    {
        return $this->data['null'] ?: null;
    }
    
    /**
     * @return string
     * @see TableFieldDescription::$default
     */
    public function default(): string
    {
        return $this->data['default'];
    }
    
    /**
     * @return string|null
     * @see TableFieldDescription::$extra
     */
    public function extra(): ?string
    {
        return $this->data['extra'] ?: null;
    }
    
    /**
     * @param array $source
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $source)
    {
        $this->init($source);
        $this->required();
    }
    
    /**
     * Initiation method
     *
     * @param array $source
     *
     * @return void
     */
    protected function init(array $source): void
    {
        foreach ($source as $index => $value) {
            $this->data[\strtolower($index)] = $value;
        }
        $origin = $this->data['field'];
        foreach ($this->data as &$value) {
            $value = \strtolower($value);
        }
        $this->data['fieldOrigin'] = $origin;
    }
    
    protected function get(string $name)
    {
        if (\method_exists($this, $name)) {
            return $this->{$name}();
        }
        return $this->data[$name] ?? null;
    }
    
    protected function has(string $name): bool
    {
        return \method_exists($this, $name) || isset($this->data[$name]);
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
    
    public function jsonSerialize(): array
    {
        return [
            'index'       => $this->index(),
            'field'       => $this->field(),
            'fieldOrigin' => $this->fieldOrigin(),
            'typeRaw'     => $this->typeRaw(),
            'type'        => $this->type(),
            'typeLength'  => $this->typeLength(),
            'unsigned'    => $this->unsigned(),
            'nullable'    => $this->nullable(),
            'key'         => $this->key(),
            'default'     => $this->default(),
            'extra'       => $this->extra()
        ];
    }
    
    public function __debugInfo(): array
    {
        return ['__class' => static::class] + $this->data;
    }
    
    /**
     * Testing that all required fields are accessable, called after {@see TableFieldDescription::init()}.
     * @return void
     * @throws \InvalidArgumentException if required keys are missing
     */
    protected function required(): void
    {
        $shared = \array_intersect_key($this->data, array_flip(static::REQUIRED_FIELDS));
        if (count($shared) < count(static::REQUIRED_FIELDS)) {
            throw new \InvalidArgumentException('Required keys are missing in TableFieldDescription on init()');
        }
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
}