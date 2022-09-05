# Url

Handling URLs through fluid mutations. Plus adding [Smarty support](../smarty/Url.md) on top.

## Basic usage

```php
$source = 'https://gooogle.com';
// $source can be a URL or Url instance or null, or some predefines strings.
$url = \ITholics\Oxid\Application\Core\Url::getInstance($source);
// use magic getter to mutate values or methods starting with 'with'.
$url->schemeless; // unsets scheme
$url->withHost('www.itholics.de'); // updates the host

```

## Smarty
Smarty functions & modifiers help to mutate given URLs in the template.

## Further notice
This documentation is incomplete and will be expanded at a later date.