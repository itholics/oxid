# Traits

## InstanceTrait

Allows static instanziation of classes the oxid way.

```php
class MyClass {
    use \ITholics\Oxid\Application\Shared\InstanceTrait;
    
    public function __construct($a, $n) {}
}

$class = MyClass('a', 'b');
```