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
$handler->setFormatter($formatter);
$logger->pushHandler($handler);
// add extra file logger for errors
$formatter2 = Logger\LineFormatter::getInstance()->withJsonOptions(JSON_PRETTY_PRINT);
$fileHandler = new StreamHandler(OX_LOG_FILE, Logger::ERROR);
$fileHandler->setFormatter($formatter2);
$logger->pushHandler($fileHandler);
```

### Output example to oxieshop.log

    [2022-09-13 16:29:58] Dev.ERROR: 
        [ OxidEsales\EshopCommunity\Application\Controller\StartController::render ]
            >> URL="//oxid6.localhost/index.php"
            >> *nooo, please help* [Exception] (0) > nope (/var/www/html/vendor/oxid-esales/oxideshop-ce/source/Application/Controller/StartController.php/114)
            #0 /var/www/html/vendor/oxid-esales/oxideshop-ce/source/Core/ShopControl.php(471): OxidEsales\EshopCommunity\Application\Controller\StartController->render()
            #1 /var/www/html/vendor/oxid-esales/oxideshop-ce/source/Core/ShopControl.php(359): OxidEsales\EshopCommunity\Core\ShopControl->_render(Object(OxidEsales\Eshop\Application\Controller\StartController))
            #2 /var/www/html/vendor/oxid-esales/oxideshop-ce/source/Core/ShopControl.php(282): OxidEsales\EshopCommunity\Core\ShopControl->formOutput(Object(OxidEsales\Eshop\Application\Controller\StartController))
            #3 /var/www/html/vendor/oxid-esales/oxideshop-ce/source/Core/ShopControl.php(142): OxidEsales\EshopCommunity\Core\ShopControl->_process('OxidEsales\\Esho...', NULL, NULL, NULL)
            #4 /var/www/html/vendor/oxid-esales/oxideshop-ce/source/Core/Oxid.php(27): OxidEsales\EshopCommunity\Core\ShopControl->start()
            #5 /var/www/html/source/index.php(16): OxidEsales\EshopCommunity\Core\Oxid::run()
            #6 {main} {
        "shopId": 1
    } 
