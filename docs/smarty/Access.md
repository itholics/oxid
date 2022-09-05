# Accessors

## Modifier

### $complex

    { (array)
        "elem": "HELLO",
        "first": { (object)
            "second": { (ITholics\\Oxid\\Application\\Core\\Url)
                "class": "ITholics\\Oxid\\Application\\Core\\Url",
                "host": "oxid6.localhost",
                "port": 80,
                "scheme": "https",
                "fragment": null,
                "user": null,
                "password": null,
                "path": {
                    "1": "index.php"
                },
                "query": {
                    "force_admin_sid": "am9nkd277p17futbkjvr07k3b1",
                    "stoken": "49D1D340"
                },
                "leading": true,
                "trailing": false,
                "immutable": false,
                "url": "https:\/\/oxid6.localhost\/index.php?force_admin_sid=am9nkd277p17futbkjvr07k3b1&stoken=49D1D340"
            },
            "third": "HIT",
            "4th": [
                1,
                2,
                3,
                false,
                3.14
            ]
        }
    }

### ithAccess

    [{ $source|ithAccess : $index : ...$params }]

- `$source` array|object: otherwise `null` returned
- `$index` string(required): dot notion to access object/array hierarchy. if the section ends with `()` a method should be called.
- `$params` mixed: if a method is called by `$index`, these parameters are appended as parameters to the called method.
- To call more than one method with different `$params`, you can also chain the `ithAccess` filter.

#### Examples on $complex

    $complex|@ithAccess:elem
        > "HELLO" (string)

    $complex|@ithAccess:first
        > stdClass (Url)

    $complex|@ithAccess:"first.third" (using dot, string delimiters are needed)
        > "HIT" (string)

    $complex|@ithAccess:"first.4th"
        > Array (5)

    $complex|@ithAccess:"first.4th.0"
        > 1 (int)

    $complex|@ithAccess:first|ithAccess:"4th"|ithAccess:0
        > 1 (int) 
        > equivalent to before

    $complex|@ithAccess:"first.4th.4"
        > 3.14 (float)
    
    $complex|@ithAccess:"first.4th.-1"
        > 3.14 (float) 
        > equivalent to before with negative index

    $complex|@ithAccess:"first.second.scheme"
        > https (string) 
        > property of Url accessed

    $complex|@ithAccess:"first.second.clone.schemeless.url"
        > //oxid6.localhost/index.php?force_admin_sid=am9nkd277p17futbkjvr07k3b1&stoken=49D1D340 (string)
        > property chaining of url (using clone to not modify the Url)

    $complex|@ithAccess:"first.second.clone.addQuery().url":test:nice
        > https://oxid6.localhost/index.php?force_admin_sid=am9nkd277p17futbkjvr07k3b1&stoken=49D1D340&test=nice (string)
        > using addQuery("test", "nice") to add "text=nice" query parameter and returning the url.

    $complex|@ithAcess:"first.second.clone.keepQuery()":stoken|ithAccess:url
        > https://oxid6.localhost/index.php?stoken=49D1D340 (string)
        > here we used ithAccess-chaining.
    
### ithField

    [{ $source|ithField : $fieldOrRaw=false : $raw=false }]

Extracts value from `OxidEsales\Eshop\Core\Field` or `OxidEsales\Eshop\Core\Model\BaseModel`.  
If the Model is provided for `$source`, then `$fieldOrRaw` must be the field name (just the field names no table prefix).
Other `$soure` values return null;

#### Examples on OxidEsales\Eshop\Application\Model\Article as $article

    $article|ithField:oxtitle
        > Trapez ION MADTRIXX (string)

    $article|ithField:oxtitle:true
        > Trapez ION MADTRIXX (string)
        > in this case the same (calling the raw value)

#### Examples on OxidEsales\Eshop\Core\Field as $field

    $field|ithField
        > Trapez ION MADTRIXX (string) 

    $field|ithField:true
        > Trapez ION MADTRIXX (string) 
        > in this case the same (calling the raw value)

    
### ithReq

    [{ $source|ithReq : $raw=false}]

#### Examples

    URL: index.php?id=qqi02n41hoj0fh8bfip8qcecl3&token=9A4C625F&foo=bar
    
    'token'|ithReq
    > 9A4C625F (string)
    > by default using oxid's parameter escaping

    'miss'|ithReq
    > null

    '?foo'|ithReq
    > true (bool)
    > testing existence by prepending ? (question mark)

    '?test'|ithReq
    > false (bool)

    'id'|ithReq:true
    > qqi02n41hoj0fh8bfip8qcecl3 (string)
    > accessing raw content 


### ithFallback

    [{ $source|ithFallback : $fallback }]

This modifer returns `$fallback` in case that `$source` is not truish.

#### Examples

    ''|ithFallback:'Apply this string'
    > Apply this string (string)

    null|ithFallback:nope
    > nope (string)

    'truish'|ithFallback:nope
    > truish (string)


### ithFallbackStrict

    [{ $source|ithFallbackStrict : $fallback }]

Like the `ithFallback` modifier, but it falls back on `null`.

#### Examples

    ''|ithFallbackStrict:nope
    > (empty string)

    null|ithFallback:nope
    > nope (string)

    