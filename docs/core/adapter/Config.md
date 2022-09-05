# Config

This adapter helps to implement a configuration based on JSON withing a MySQL table.  
Each configuration is shop dependant.

## Structure
```mysql
CREATE TABLE IF NOT EXISTS `YOUR_TABLE_NAME` (
    `shop_id` INT(11) NOT NULL,
    `config` JSON NULL DEFAULT NULL,
    PRIMARY KEY (`shop_id`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
```

## Usage
This is an abstract class and must be implemented to your liking.

```php
use ITholics\Oxid\Application\Core\Adapter\Config;

/**
 * @property-read string $hello_world // IDE support
 * @method static MyConfig getInstance(?int $shopId = null) // IDE support
 */
class MyConfig extends Config {
    protected function getTableName(): string {
        return 'hello_world_config';
    }
    
    protected function set(): bool {
        return false; // let the default handler work (basic assignment)
    }
    
    /**
    * This method is implicitliy called by the magic getter with the same name.
    * @return string
    */
    public function hello_world() {
        // You may also use 'hello_world' instead of __FUNCTION__ 
        return $this->_(__FUNCTION__, 'Hello World!'); 
        // Apply the default value, if the content is not saved to DB yet.
    }
}

$config = MyConfig::getInstance(); // chooses the current active shop by default

$config->onActivate(); // Add this to your onActivate event of your module, to automatically generate the MySQL table

echo $config->hello_world; // -> 'Hello World!'
echo $config->hello_world(); // -> 'Hello World!' (both do the same)
$config->hello_world = 'Bye bye';
echo $config->hello_world; // -> 'Bye bye' (unsafed)
$config->save(); // persisting data
```