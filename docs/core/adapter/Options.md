# Options

This class helps to handle the module settings defined in the metadata.php file.

## Usage

```php
use ITholics\Oxid\Application\Core\Adapter\Options;

/**
 * @property-read   string $hello 
 * @property-read   string $select 
 * @property        string $__internal_test 
 */
class MyOptions extends Options {
    public function getModuleId() : string {
        return 'my_module_id';
    }
}

// example settings of metdata.php
$settings = [
    [
        'group' => 'main',
        'name'  => 'hello',
        'type'  => 'str',
        'value' => 'Hello World'
    ],
    [
        'group'         => 'main',
        'name'          => 'select',
        'type'          => 'select',
        'value'         => 'world',
        'constraints'   => 'hello|world'
    ],
    [
        'name'  => 'internal_test',
        'type'  => 'str',
        'value' => 'Unchangable via module interface'
    ]   
];

$opt = MyOptions::getInstance();

echo $opt->hello; // -> 'Hello World'
echo $opt->select; // -> 'world'
// 'internal_test' is a type of setting, that is not changable via the module interface. It is a hidden module setting.
// The double underscore prefix allows accessing this for the defined module id
echo $opt->__internal_test; // -> 'Unchangable via module interface'
// You may also access the value of a module by seperating the module id and value with double underscores.
echo $opt->imy_module_id__internal_test; // -> 'Unchangable via module interface'
// internal values may also be changed programmatically.
$opt->__internal_test = 'new-value'; 
echo $opt->__internal_test; // -> 'new-value'
```