# Database-Wrapper

Different versions of OXID uses different database classes.
This wrapper tries to union the methods under an own interface.

## Provider

The Provider loads the latest usable database class.

```php
use ITholics\Oxid\Application\Core\IO\Database\Provider;

$db = Provider::getInstance()->get();
// you may use the invoker
$db = Provider::getInstance()();
// or even drop that
$db = Provider::getInstance(); // here we can use magic getters to access the underlying interface, but won't get IDE support.
// or event through
$db = oxNew(Provider::class)->get(); // the ::getInstance() version should be preferred, because it uses singleton pattern.
// the utils class provides also a getter
$db = \ITholics\Oxid\Application\Core\Utils::getInstance()->getDb();
```

After loading the interface, you cann access the methods of `ITholics\Oxid\Application\Core\IO\Database\DatabaseInterface`.

```php
$db->describeTable('oxarticles'); // returns a ITholics\Oxid\Application\Core\IO\Database\Meta\TableDescription
$db->iterateAssociative('SELECT ...'); // yielding over associatve rows
$db->fetchAllAssociative('SELECT ...'); // retrieving the whole set with assiociative rows
```

If you know the underlying structure, you can also call the methods, that are not specified in the interface.

```php
$db->isTransactionActive(); // calls Doctrine's method if available
```

## TableDescription
You can receive a table description object by using

```php
$tableDescription = \ITholics\Oxid\Application\Core\IO\Database\Provider::getInstance()->get()->describeTable('YOUR_TABLE_NAME');
```

A `TableDescription` lists all columns as `TableFieldDescription`. Each field's name is used as lowercased hash to get the field.

```php
$tableDescription = \ITholics\Oxid\Application\Core\IO\Database\Provider::getInstance()->get()->describeTable('YOUR_TABLE_NAME');
// retrieve the `TableFieldDescription` of `field` or null of not existent.
$field = $tableDescription->get('field');
// or (magic getter)
$field = $tableDescription->field;
// or (array access)
$field = $tableDescription['field'];
//
// check if field exists
$tableDescription->has('field');
// or (magic getter)
isset($tableDescription->field);
// or (array access)
isset($tableDescription['field']);
```

## TableDescriptionField
Contains information about a table column.

```php
$tableDescription = \ITholics\Oxid\Application\Core\IO\Database\Provider::getInstance()->get()->describeTable('YOUR_TABLE_NAME');
$field = $tableDescription->get('field');
// get field name
$field->name();
$field->index();
$field->field();
// or magic getter
$field->name;
$field->index;
$field->field;
// or array access
$field['name'];
$field['index'];
$field['field'];
// each accessor is available through corresponding magic getter or array access,
// thus none is listed below
// almost all strings are lowercased

// get original name (case-sensitive)
$field->fieldOrigin();
// get raw type definiton
$field->typeRaw();
// get type (tinyint, etc) 
$field->type();
// type length/size
$field->typeLength();
// unsigned value
$field->unsigned();
// nullable
$field->nullable();
$field->null();
// column key
$field->key();
// default value
$field->default();
// extra column information
$field->extra();
```