# Url

## Functions

### ithUrl

    [{ ithUrl param1=arg1 param2=arg2 ... }] as string

#### Parameters

Alle parameters are executed in the following order.

| parameter        | type                                               | defaults | description                                                                                                                                                                                                                                                                         |
|------------------|----------------------------------------------------|----------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **url / uri**    | null / string / ITholics\Oxid\Application\Core\Url | null     | Used as base URL input. It can be the class itself or a string. The class tries to extract the URL. <br>Special values are:<br>`oxid` of Config::getShopUrl()<br>`request` through $_SERVER<br>`self` of ViewConfig::getSelfUrl()<br>`selfaction` of ViewConfig::getSelfActionUrl() |
| **clone**        | bool                                               | false    | Creates a clone of the created URL after extracting the URL. Useful to not mutate given Url instance.                                                                                                                                                                               |
| **globals**      | bool                                               | false    | Applies globals found in $_GET to the query parameters.                                                                                                                                                                                                                             |
| **scheme**       | string / null                                      | null     | Set the URL scheme: null is none.                                                                                                                                                                                                                                                   |
| **host**         | string / null                                      | null     | Set the URL host: null is none.                                                                                                                                                                                                                                                     |
| **port**         | int / null                                         | null     | Set the URL port. null equals 80 and 80 is invisible in the visible URL.                                                                                                                                                                                                            | 
| **fragment**     | string / null                                      | null     | Set the URL fragment: null is none.                                                                                                                                                                                                                                                 |
| **user**         | string / null                                      | null     | Set the URL user: null is none.                                                                                                                                                                                                                                                     |
| **password**     | string / null                                      | null     | Set the user's passowrd. Only applied if `user` is set. null is none.                                                                                                                                                                                                               |
| **path**         | string / array / null                              | null     | Sets the URL path: `path/to/succes` or as array `["path", "to", "success"]`. null is noop.                                                                                                                                                                                          |                                                                                                  | 
| **query**        | string / array / object / null                     | null     | Sets the URL query: `foo=bar&hello=world` or as array `["foo" => "bar", "hello" => "world"]`                                                                                                                                                                                        |
| **leading**      | bool / null                                        | null     | Sets the leading flag slash for the URL Path if not null                                                                                                                                                                                                                            |
| **trailing**     | bool / null                                        | null     | Sets the trailing slash flag for the URL path if not null                                                                                                                                                                                                                           |
| **schemeless**   | bool                                               | false    | Drops the URL scheme                                                                                                                                                                                                                                                                |
| **hostless**     | bool                                               | false    | Drops the URL host (makes URL relative)                                                                                                                                                                                                                                             |
| **loginless**    | bool                                               | false    | Drops the user credentials                                                                                                                                                                                                                                                          |
| **portless**     | bool                                               | false    | Drops the port (aka resets to 80, which is invisible)                                                                                                                                                                                                                               |
| **fragmentless** | bool                                               | false    | Drops the URL fragment                                                                                                                                                                                                                                                              |
| **pathless**     | bool                                               | false    | Drops the URL path                                                                                                                                                                                                                                                                  |
| **queryless**    | bool                                               | false    | Drops the URL query                                                                                                                                                                                                                                                                 |
| **leadingless**  | bool                                               | false    | Drops the leading slash (flag) of the URL path section (visible only on relative URLs)                                                                                                                                                                                              |
| **trailingless** | bool                                               | false    | Drops the trailing slash (flag) of the URL path section                                                                                                                                                                                                                             |
| **relative**     | bool                                               | false    | Makes URL relative aka `hostless`.                                                                                                                                                                                                                                                  |


## Modifiers

Modifiers will mostly return an instance of ITholics\Oxid\Application\Core\Url or null on fail.
Some modifiers return sections or the representation of the URL itself, depending on the parameters.

### ithUrl

    [{ $source|ithUrl:$action1:$action2:... }] as null | string | int | ITholics\Oxid\Application\Core\Url

`$source` is converted to `ITholics\Oxid\Application\Core\Url` if it is not a `Url`.
If the `$source` is not convertable `null` will be returned. This is true for all `Url modifiers`.
`$source` may have special values:

| $source        | origin                         |
|----------------|--------------------------------|
| **oxid**       | Config::getShopUrl()           |
| **request**    | using $_SERVER to build url    |
| **self**       | ViewConfig::getSelfUrl()       |
| **selfaction** | ViewConfig::getSelfActionUrl() |

#### Actions

Each action is executed sequentially aka in order. Same action can be repeated.

| action           | returns        | description                                     |
|------------------|----------------|-------------------------------------------------|
| **clone**        | self           | Clones the source instance                      |
| **copy**         | self           | Alias for `clone`                               |
| **schemeless**   | self           | Drrops scheme                                   |
| **hostless**     | self           | Drops the host, making url relative             |
| **domainless**   | self           | Drops domain (scheme, host, login, port)        |
| **loginless**    | self           | Drops login/credentials                         |
| **portless**     | self           | Drops port (resets to invisible 80)             |
| **fragmentless** | self           | Drop fragment                                   |
| **pathless**     | self           | Drops path                                      |
| **queryless**    | self           | Drops query                                     |
| **leadingless**  | self           | Drops leading slash of the path                 | 
| **trailingless** | self           | Drops trailing slash of the path                | 
| **popPath**      | self           | Drops the last section of the path              | 
| **shiftPath**    | self           | Drops the first section of the path             | 
| **globals**      | self           | Applies query from $_GET                        | 
| **oxid**         | self           | Applies URL from Config::getShopUrl()           | 
| **request**      | self           | Applies URL from $_SERVER                       | 
| **self**         | self           | Applies URL from ViewConfig::getSelfUrl()       | 
| **selfaction**   | self           | Applies URL from ViewConfig::getSelfActionUrl() | 
| **scheme**       | string / null  | URL scheme                                      | 
| **host**         | string / null  | URL host                                        | 
| **domain**       | string / null  | URL domain                                      | 
| **port**         | int            | URL port                                        | 
| **fragment**     | string / null  | URL fragment                                    | 
| **user**         | string / null  | URL user loginname (credential)                 | 
| **password**     | string / null  | URL user password (credential)                  | 
| **login**        | string / null  | URL login/credentials `username:password`       | 
| **path**         | string         | URL path                                        | 
| **query**        | string         | URL query                                       | 
| **json**         | string / false | JSON representation                             | 
| **jsonNice**     | string / false | JSON representation / beautiful                 | 
| **url**          | string         | Full URL                                        | 
| **uri**          | string         | Full URL aka `url`                              |
| **relative**     | string         | Relative URL                                    |


### ithUrlFragment

    [{ $source|ithUrlFragment:$key=null }]

Update the fragment of the source (returns ITholics\Oxid\Application\Core\Url|null).

- `key` (string|null): Set the fragment key, null removes fragment.

### ithUrlPath <a name="urlpath"></a>

    [{ $source|ithUrlPath:...$element }]

Set the path which each `$element`. $element itself may be a path like `path/to/success`.
But you may also split it up to `path`, `to`, `success`' or combine it like:
`path/to`, `success`.

- `element` (string): Empty or invalid items will be dropped.

### ithUrlPathAppend

    [{ $source|ithUrlPathAppend:...$element }]

Same mechanics [lLike ithUrlPath](#urlpath), just appending the `$element` parts.

- `element` (string): Empty or invalid items will be dropped.

### ithUrlPathAppend

    [{ $source|ithUrlPathPrepend:...$element }]

Same mechanics [lLike ithUrlPath](#urlpath), just prepending the `$element` parts.

- `element` (string): Empty or invalid items will be dropped.

### ithUrlPathPop

    [{ $source|ithUrlPathPop:$times=1 }]

Drops the first `$times` sections of the path.

- `times` (int): Number of section of the path to drop from the beginning

### ithUrlPathShift

    [{ $source|ithUrlPathShift:$times=1 }]

Drops the last `$times` sections of the path.

- `times` (int): Number of section of the path to drop from the ending

### ithUrlQuery

    [{ $source|ithUrlQuery:...$args }]

Modifier to change the query parameters. This is resolved by creating `pairs of $args`.
Each pair represents a query parementer by `key:value` pair. If the value is `null` it will be unset.

Example:

    ithUrlQuery:foo:bar -> foo=bar
    ithUrlQuery:foo:bar:next:value -> foo=bar&next=value
    ithUrlQuery:foo:bar:next:value:foo:null -> next=value
    ithUrlQuery:foo:bar:next:value:foo -> next=value // last is automatically null.

### ithUrlScheme

    [{ $source|ithUrlScheme:scheme=null }]

Sets the scheme.

- scheme (string|null): scheme to set, null unsets

### ithUrlEquals

    [{ $source|ithUrlEquals:$other }]

Compares `$source` agaianst `$other` and returns true, if the urls are the same.

- $other (string|ITholics\Oxid\Application\Core\Url): the comparable URL