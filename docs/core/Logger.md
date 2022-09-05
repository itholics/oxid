# Logger

Monolog is used for logging, and we extend Monolog.  
The basic idea is to beautify the output.

## Examples

### Generic logger (functions.php)

```php
use ITholics\Oxid\Application\Core\Logger;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\StreamHandler;

$logger = Logger::getInstance('Dev');
$formatter = Logger\LineFormatter::getInstance()->withJsonOptions(JSON_PRETTY_PRINT);
$fileHandler = new StreamHandler(OX_LOG_FILE, Logger::ERROR);
$fileHandler->setFormatter($formatter);
$logger->pushHandler($fileHandler);
$browserHandler = new BrowserConsoleHandler();
$logger->pushHandler($browserHandler);
```

### CLI Logger

```php
use ITholics\Oxid\Application\Core\Logger;
use Monolog\Handler\StreamHandler;

$logger = Logger::getInstance('CLI');
$formatter = Logger\ColoredLineFormatter::getInstance()->withJsonOptions(JSON_PRETTY_PRINT);
$handler = new StreamHandler(STDOUT);
$logger->pushHandler($handler);
// add extra file logger for errors
$formatter2 = Logger\LineFormatter::getInstance()->withJsonOptions(JSON_PRETTY_PRINT);
$fileHandler = new StreamHandler(OX_LOG_FILE, Logger::ERROR);
$fileHandler->setFormatter($formatter2);
$logger->pushHandler($fileHandler);
```

### Output example to oxieshop.log

    [2022-08-30 15:42:32] Dev.ERROR: 
        [ ITholics\Oxid\Application\Core\Adapter\Config::init ] >> *Failed to initialize config from JSON* [JsonException] (4) > Syntax error (/var/www/html/source/modules/ith_modules/oxid/Application/Core/Adapter/Config.php/120)
            #0 /var/www/html/source/modules/ith_modules/oxid/Application/Core/Adapter/Config.php(120): json_decode('', true, 512, 4194304)
            #1 /var/www/html/source/modules/ith_modules/oxid/Application/Core/Adapter/Config.php(69): ITholics\Oxid\Application\Core\Adapter\Config->init()
            #2 /var/www/html/vendor/oxid-esales/oxideshop-ce/source/Core/UtilsObject.php(231): ITholics\Oxid\Application\Core\Adapter\Config->__construct(1)
            #3 /var/www/html/source/oxfunctions.php(104): OxidEsales\EshopCommunity\Core\UtilsObject->oxNew('conf', 1)
            #4 /var/www/html/source/modules/ith_modules/oxid/Application/Core/Adapter/Config.php(84): oxNew('Conf', 1)
            #5 /var/www/html/source/modules/ith_modules/oxid/bin/url.php(45): ITholics\Oxid\Application\Core\Adapter\Config::getInstance(1)
            #6 {main} {
        "shopId": 1
    } 