<?php
/**
 * This Software is the property of ITholics GmbH and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link          http://www.itholics.de
 * @copyright (C) ITholics GmbH 2011-2022
 * @author        ITholics GmbH <oxid@itholics.de>
 * @author        Gabriel Peleskei <gp@itholics.de>
 */

namespace ITholics\Oxid\Application\Core;

use Exception;
use ITholics\Oxid\Application\Exception\Url\UrlPathException;
use ITholics\Oxid\Application\Exception\Url\UrlQueryException;
use ITholics\Oxid\Application\Shared\StaticInstanceTrait;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;
use function array_chunk;
use function array_filter;
use function array_map;
use function array_shift;
use function array_values;
use function call_user_func_array;
use function count;
use function explode;
use function filter_var;
use function get_class;
use function gettype;
use function is_array;
use function is_bool;
use function is_int;
use function is_object;
use function is_scalar;
use function is_string;
use function json_encode;
use function preg_match;
use function preg_split;
use function property_exists;
use function smarty_modifier_ithUrl;
use function sprintf;
use function strlen;
use function substr;
use function trigger_error;
use function trim;
use const ARRAY_FILTER_USE_KEY;
use const E_USER_ERROR;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_INT;

/**
 * Smarty handler.
 * Holds all methods/function that are used by the smarty extension.
 * This allows overriding the methods, if desired.
 * @method static Smarty getInstance()
 */
class Smarty
{
    use StaticInstanceTrait;
    
    //////////////////////
    // ACCESS
    //////////////////////
    /**
     * A helper function to describe the contents behind $source.
     *  - array -> "Array (count)"
     *  - object -> "Namespace\Classname"
     *  - string -> "String(length)" or with $showString: "String(length)='the actual string'
     *  - bool -> "Bool(true)" or "Bool(false)"
     *  - null -> "NULL"
     *  - remaining -> "type(value)"
     *
     * @param mixed      $source
     * @param bool|false $showString
     *
     * @return string
     */
    public function ithDescribe($source, bool $showString = false): string
    {
        if (is_object($source)) {
            return get_class($source);
        }
        if (is_array($source)) {
            return sprintf('Array(%d)', count($source));
        }
        if (is_string($source)) {
            if ($showString) {
                return sprintf('String(%s)="%s"', strlen($source), $source);
            }
            return sprintf('String(%d)', strlen($source));
        }
        if (is_bool($source)) {
            return sprintf('Bool(%s)', json_encode($source));
        }
        if (null === $source) {
            return 'NULL';
        }
        return sprintf('%s(%s)', gettype($source), $source);
    }
    
    /**
     * Accessing arrays/objects using point notation in the $index.
     *
     * @template T
     *
     * @param T|array|object $source    Only object|array make sense here, otherwise it always will return null.
     * @param string         $index     point notation to access $source's hierarchy
     * @param mixed          ...$params appends as method params on first method used.
     *
     * @return T|mixed|null
     */
    public function ithAccess($source, string $index, ...$params)
    {
        $paths = preg_split('/\s*\.\s*/', $index);
        $paths = array_filter($paths, static function ($source) {
            return trim((string)$source) !== '';
        });
        $paths = array_values($paths);
        if (!$paths) {
            return null;
        }
        foreach ($paths as $section => $path) {
            try {
                if (is_array($source)) {
                    $int = filter_var($path, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if (is_int($int)) {
                        $source = array_values(array_filter($source, 'is_int', ARRAY_FILTER_USE_KEY));
                        $path   = $int;
                        if ($path < 0) {
                            $len  = count($source);
                            $path = (($path % $len) + $len) % $len;
                        }
                    }
                    $source = $source[$path] ?? null;
                    continue;
                }
                if (is_object($source)) {
                    if (preg_match('/\(\)$/', $path)) {
                        $path   = substr($path, 0, -2);
                        $source = $source->{$path}(... $params) ?? null;
                        $params = [];
                        continue;
                    }
                    $source = $source->{$path} ?? null;
                    continue;
                }
                return null;
            } catch (\Throwable $e) {
                return null;
            }
        }
        return $source ?? null;
    }
    
    /**
     * This method acts in 2 ways.
     * If $source is {@see BaseModel} it uses $fieldOrRaw als field name to access the value.
     * Otherwise, if used on {@see Field} $fieldOrRaw will act as $raw.
     *
     * @param BaseModel|Field $source
     * @param string|bool     $fieldOrRaw string if $source is BaseModel else bool
     * @param bool            $raw        only used if $source is BaseModel
     *
     * @return mixed|null
     */
    public function ithField($source, $fieldOrRaw = false, bool $raw = false)
    {
        if ($source instanceof BaseModel) {
            $fieldOrRaw = (string)$fieldOrRaw;
            return $raw ? ($source->getRawFieldData($fieldOrRaw) ?? $source->getFieldData($fieldOrRaw) ?? null) : ($source->getFieldData($fieldOrRaw) ?? $source->getRawFieldData($fieldOrRaw) ?? null);
        }
        if ($source instanceof Field) {
            $raw = (bool)$fieldOrRaw;
            return $raw ? ($source->rawValue ?? $source->value ?? null) : ($source->value ?? $source->rawValue ?? null);
        }
        return null;
    }
    
    /**
     * Gets the request parameter defined in $source.
     *
     * @param string $source name of the request variable to read
     * @param bool   $raw    unescaped if true
     *
     * @return mixed|string|null
     */
    public function ithReq($source, bool $raw = false)
    {
        if (is_string($source)) {
            if (($source[0] ?? '') === '?'){
                $source = substr($source, 1);
                return isset($_REQUEST[$source]);
            }
            $req = Registry::getRequest();
            return $raw ? $req->getRequestParameter($source, null) : $req->getRequestEscapedParameter($source, null);
        }
        return null;
    }
    
    //////////////////////
    // IO
    //////////////////////
    /**
     * Constructs the url from the `out` path folder of the shop or if $moduleId is provided,
     * from the `out` path of the module folder.
     *
     * @param string      $source
     * @param string|null $moduleId
     *
     * @return string|null
     */
    public function ithOut($source, ?string $moduleId = null): ?string
    {
        if (!is_string($source)) {
            return null;
        }
        return Utils::getInstance()->getPublicFile($source, $moduleId);
    }
    
    /**
     * Like {@see Smarty::ithOut()} but as template function.
     * `file` expexts the relative url from `out` folder.
     * `moduleId` the module id if related to module.
     *
     * @param array   $params
     * @param \Smarty $smarty
     *
     * @return string|null
     */
    public function ithOutFunction(array $params, &$smarty)
    {
        $file     = $params['file'] ?? null;
        $moduleId = $params['module'] ?? null;
        return $this->ithOut($file, $moduleId);
    }
    
    /**
     * Return the url to the mediathek file provided by the $source.
     * If the source is folderless path, it maps the correct path.
     *
     * @param string $source
     * @param        ...$args unused currently
     *
     * @return string|null
     */
    public function ithMedia($source, ...$args): ?string
    {
        if (is_string($source) && trim($source)) {
            $sections = explode('/', $source);
            if (count($sections) < 2) {
                return '/out/pictures/ddmedia/' . $source;
            }
            return $source;
        }
        return null;
    }
    
    /**
     * Like {@see Smarty::ithMedia()} but as template function expecting the template parameter `file`.
     *
     * @param array   $params
     * @param \Smarty $smarty
     *
     * @return string|null
     */
    public function ithMediaFunction(array $params, &$smarty)
    {
        $file = $params['file'] ?? null;
        return $this->ithMedia($file);
    }
    
    //////////////////////
    // Url
    //////////////////////
    /**
     * This method creates and manipulates an url.
     * It outputs the url to the smarty content.
     *
     * $params expexts url/uri as basic parameter.
     *
     * @param array   $params
     * @param \Smarty $smarty
     *
     * @return string
     */
    public function ithUrlFunction(array $params, &$smarty)
    {
        try {
            $url = Url::getInstance($params['uri'] ?? $params['url'] ?? null);
            if ($clone = $params['clone'] ?? false) {
                $url = $url->clone;
            }
            if ($globals = $params['globals'] ?? false) {
                $url->withGlobals();
            }
            if ($scheme = $params['scheme'] ?? null) {
                $url->withScheme($scheme);
            }
            if ($host = $params['host'] ?? null) {
                $url->withHost($host);
            }
            if ($port = $params['port'] ?? null) {
                $url->withPort((int)$port);
            }
            if ($frag = $params['fragment'] ?? null) {
                $url->withFragment($frag);
            }
            if ($user = $params['user'] ?? null) {
                $url->withLogin($user, $params['password'] ?? null);
            }
            if ($path = $params['path'] ?? null) {
                try {
                    $url->withPath($path);
                } catch (UrlPathException $e) {
                    Registry::getLogger()->error('smary_function_ithUrl($params) got wrong `path`', [$e, 'path' => $path]);
                }
            }
            if ($query = $params['query'] ?? null) {
                try {
                    $url->withQuery($query);
                } catch (UrlQueryException $e) {
                    Registry::getLogger()->error('smary_function_ithUrl($params) got wrong `query`', [$e, 'query' => $query]);
                }
            }
            if (null !== ($leading = $params['leading'] ?? null)) {
                $url->withLeading($leading);
            }
            if (null !== ($trailing = $params['trailing'] ?? null)) {
                $url->withTrailing($trailing);
            }
            if ($params['schemeless'] ?? false) {
                $url = $url->schemeless;
            }
            if ($params['hostless'] ?? false) {
                $url = $url->hostless;
            }
            if ($params['loginless'] ?? false) {
                $url = $url->loginless;
            }
            if ($params['portless'] ?? false) {
                $url = $url->portless;
            }
            if ($params['fragmentless'] ?? false) {
                $url = $url->fragmentless;
            }
            if ($params['pathless'] ?? false) {
                $url = $url->pathless;
            }
            if ($params['queryless'] ?? false) {
                $url = $url->queryless;
            }
            if ($params['leadingless'] ?? false) {
                $url = $url->leadingless;
            }
            if ($params['trailingless'] ?? false) {
                $url = $url->trailingless;
            }
            if ($params['relative'] ?? false) {
                return $url->relativeUrl;
            }
            return $url->url;
        } catch (\Throwable $e) {
            Registry::getLogger()->error('URL FAILED', [$e]);
            return $url ?? null;
        }
    }
    /**
     * Url builder and modifier.
     * Use action string to modify or access some properties.
     * Each action is processes step by step.
     * If it is a modifing action, it will return the class, otherwise it will return the respective content.
     *
     * @param Url|string $source
     * @param string     ...$actions
     *
     * @return array|bool|int|Url|string|null
     */
    public function ithUrl($source, string ...$actions)
    {
        if (null === $source) {
            return null;
        }
        try {
            $url = Url::getInstance($source ?: null);
            if (empty($actions)) {
                return $url;
            }
            $action = array_shift($actions);
            switch ($action) {
                case 'full':
                    return $url->url;
                case 'relative':
                    return $url->relativeUrl;
                case 'clone':
                case 'copy':
                case 'schemeless':
                case 'hostless':
                case 'loginless':
                case 'portless':
                case 'fragmentless':
                case 'pathless':
                case 'queryless':
                case 'leaded':
                case 'leadingless':
                case 'trailed':
                case 'trailingless':
                case 'popPath':
                case 'shiftPath':
                case 'globals':
                case 'request':
                case 'oxid':
                case 'self':
                case 'selfaction':
                    return $this->ithUrl($url->{$action}, ...$actions);
                case 'scheme':
                case 'host':
                case 'domain':
                case 'port':
                case 'fragment':
                case 'user':
                case 'password':
                case 'login':
                case 'path':
                case 'query':
                case 'json':
                case 'jsonNice':
                case 'uri':
                case 'url':
                    return $url->{$action};
                default:
                    return empty($actions) ? $url : $this->ithUrl($url, ...$actions);
            }
        } catch (\Throwable $e) {
            return $url ?? null;
        }
    }
    
    /**
     * Set or unset the fragment of the url.
     * If $key is null, the fragment is unset.
     *
     * @param Url|string  $source
     * @param string|null $key
     *
     * @return Url|null
     */
    public function ithUrlFragment($source, ?string $key = null): ?Url
    {
        if (null === $source) {
            return null;
        }
        try {
            return Url::getInstance($source)->withFragment($key);
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Set the url path (overrides existing one).
     * You can use one element like: "the/next/path" or
     * you can call each element seperately: "the", "next", "path".
     *
     * @param Url|string $source
     * @param string     ...$elements
     *
     * @return Url|null
     */
    public function ithUrlPath($source, ...$elements): ?Url
    {
        if (null === $source) {
            return null;
        }
        try {
            $url      = Url::getInstance($source);
            $elements = array_map(static function ($el) { return trim((string)$el, "/"); }, $elements);
            return $url->withPath($elements);
        } catch (\Throwable $e) {
            return $url ?? null;
        }
    }
    
    /**
     * Appends $elements to the current path.
     * You can use one element like: "the/next/path" or
     * you can call each element seperately: "the", "next", "path".
     *
     * @param Url|string $source
     * @param string     ...$elements
     *
     * @return Url|null
     */
    public function ithUrlPathAppend($source, ...$elements): ?Url
    {
        if (null === $source) {
            return null;
        }
        try {
            $url      = Url::getInstance($source);
            $elements = array_map(static function ($el) { return trim((string)$el, "/"); }, $elements);
            return $url->addPath(...$elements);
        } catch (\Throwable $e) {
            return $url ?? null;
        }
    }
    /**
     * Prepends a list of strings as path.
     * You can use one element like: "the/next/path" or
     * you can call each element seperately: "the", "next", "path".
     *
     * @param string|Url $source
     * @param string     ...$elements
     *
     * @return Url|null
     */
    public function ithUrlPathPrepend($source, ...$elements): ?Url
    {
        if (null === $source) {
            return null;
        }
        try {
            $url      = Url::getInstance($source);
            $elements = array_map(static function ($el) { return trim((string)$el, "/"); }, $elements);
            return $url->prependPath(...$elements);
        } catch (\Throwable $e) {
            return $url ?? null;
        }
    }
    
    /**
     * Removes the Path parts x $times at the ending of the Url (like array_pop).
     *
     * @param Url|string $source
     * @param int        $times
     *
     * @return Url|null
     */
    public function ithUrlPathPop($source, int $times = 1): ?Url
    {
        if (null === $source) {
            return null;
        }
        try {
            $url = Url::getInstance($source);
            return $url->popPath($times);
        } catch (\Throwable $e) {
            return $url ?? null;
        }
    }
    
    /**
     * Removes the Path parts x $times at the beginning of the Url. (like array_shift)
     *
     * @param Url|string $source
     * @param int        $times
     *
     * @return Url|null
     */
    public function ithUrlPathShift($source, int $times = 1): ?Url
    {
        if (null === $source) {
            return null;
        }
        try {
            $url = Url::getInstance($source);
            return $url->shiftPath($times);
        } catch (\Throwable $e) {
            return $url ?? null;
        }
    }
    
    /**
     * Applies new query pairs to url.
     * $args is splited into chunks of key/value pairs and filled up with null.
     * If the last argument is missing, it is set null.
     * If value is null, the query parameter will be unset.
     *
     * @param Url|string $source
     * @param            ...$args
     *
     * @return Url|null
     */
    public function ithUrlQuery($source, ...$args): ?Url
    {
        if (null === $source) {
            return null;
        }
        try {
            $args = array_chunk($args, 2);
            $url  = Url::getInstance($source);
            foreach ($args as $values) {
                if (!is_array($values)) {
                    continue;
                }
                [$key, $value] = $values + [null, null];
                try {
                    $key = (string)$key;
                } catch (\Throwable $e) {
                    continue;
                }
                if ($value !== null && !is_scalar($value)) {
                    $value = (string)$value;
                }
                $url->addQuery($key, $value);
            }
            return $url;
        } catch (\Throwable $e) {
            return $url ?? null;
        }
    }
    
    /**
     * Applies scheme to url(-string). If $scheme is null, it will be unset.
     *
     * @param Url|string  $source
     * @param string|null $scheme
     *
     * @return Url|null
     *
     */
    public function ithUrlScheme($source, ?string $scheme = null): ?Url
    {
        if (null === $source) {
            return null;
        }
        try {
            $url = Url::getInstance($source);
            return $url->withScheme($scheme);
        } catch (\Throwable $e) {
            return $url ?? null;
        }
    }
    
    /**
     * Compares if urls are the same.
     *
     * @param Url|string $source
     * @param Url|string $other
     *
     * @return bool
     */
    public function ithUrlEquals($source, $other): bool
    {
        try {
            if (null === $source || null === $other) {
                return false;
            }
            return Url::getInstance($source)->equals($other);
        } catch (\Throwable $e) {
            return false;
        }
    }
    
    //////////////////////
    // MISC
    //////////////////////
    /**
     * Returns $source if truish else $fallback.
     *
     * @template A
     * @template B
     *
     * @param A $source
     * @param B $fallback
     *
     * @return A|B
     */
    public function ithFallback($source, $fallback = null)
    {
        return $source ?: $fallback;
    }
    
    /**
     * Returns $source if not null else $fallback
     * @template A
     * @template B
     *
     * @param A $source
     * @param B $fallback
     *
     * @return A|B
     */
    public function ithFallbackStrict($source, $fallback = null)
    {
        return $source ?? $fallback;
    }
}