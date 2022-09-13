<?php

namespace ITholics\Oxid\Application\Core;

use Exception;
use ITholics\Oxid\Application\Exception\Url\UrlParameterException;
use ITholics\Oxid\Application\Exception\Url\UrlPathException;
use ITholics\Oxid\Application\Exception\Url\UrlQueryException;
use ITholics\Oxid\Application\Shared\LoggerTrait;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ViewConfig;
use Psr\Log\LoggerInterface;
use function array_filter;
use function array_intersect_key;
use function array_pop;
use function array_unshift;
use function explode;
use function http_build_query;
use function is_object;
use function json_encode;
use function oxNew;
use function preg_match;
use function property_exists;
use function rtrim;
use function strlen;
use function strpos;
use function trim;
use const JSON_PRETTY_PRINT;

/**
 * Hanling URLs.
 * This class uses a lot of magic getters.
 * Each getter is also callable through array syntax.
 *
 * @property-read string|null $scheme
 * @property-read string|null $host
 * @property-read int         $port
 * @property-read string|null $fragment
 * @property-read string|null $user
 * @property-read string|null $password
 * @property-read string|null $login
 *
 * @property-read string[]    $pathRaw
 * @property-read string[]    $queryRaw
 * @property-read bool        $leading
 * @property-read bool        $trailing
 * @property-read bool        $immutable
 *
 * @property-read string|null $domain
 * @property-read string      $path
 * @property-read string      $query
 *
 * @property-read bool        $relative
 *
 * @property-read Url|$this   $clone
 * @property-read Url|$this   $copy
 * @property-read Url|$this   $schemeless
 * @property-read Url|$this   $hostless
 * @property-read Url|$this   $loginless
 * @property-read Url|$this   $portless
 * @property-read Url|$this   $fragmentless
 * @property-read Url|$this   $pathless
 * @property-read Url|$this   $queryless
 * @property-read Url|$this   $leaded
 * @property-read Url|$this   $leadingless
 * @property-read Url|$this   $trailed
 * @property-read Url|$this   $trailingless
 * @property-read Url|$this   $popPath
 * @property-read Url|$this   $shiftPath
 * @property-read Url|$this   $globals
 *
 * @property-read Url|$this   $request
 * @property-read Url|$this   $oxid
 * @property-read Url|$this   $self
 * @property-read Url|$this   $selfaction
 *
 * @property-read string      $json
 * @property-read string      $jsonNice
 * @property-read array       $array
 *
 * @property-read string      $uri
 * @property-read string      $url
 * @property-read string      $relativeUri
 * @property-read string      $relativeUrl
 *
 */
class Url implements \JsonSerializable, \ArrayAccess
{
    use LoggerTrait;
    
    public const DEFAULT_PORT = 80;
    protected $_scheme;
    protected $_host;
    protected $_port      = self::DEFAULT_PORT;
    protected $_user;
    protected $_password;
    protected $_fragment;
    protected $_path      = [];
    protected $_query     = [];
    protected $_leading   = true;
    protected $_trailing  = false;
    protected $_immutable = false;
    
    /**
     * Uses {@see Url::__construct()} to createa an instance that is oxid extendable.
     *
     * Special values for source:
     * - 'oxid' aka {@see Config::getShopUrl()} alias {@see Url::getOxidInstance()}.
     * - 'request' alias {@see Url::getRequestInstance()}
     * - 'self: aka {@see ViewConfig::getSelfLink()} alias {@see Url::getSelfInstance()}
     * - 'selfaction': aka {@see ViewConfig::getSelfActionLink()} alias {@see Url::getSelfActionInstance()}
     *
     * @param null|string|array|Url $source
     * @param LoggerInterface|null  $logger
     *
     * @return Url
     */
    public static function getInstance($source, ?LoggerInterface $logger = null)
    {
        if (is_string($source)) {
            switch ($source) {
                case 'oxid':
                    return static::getOxidInstance($logger);
                case 'request':
                    return static::getRequestInstance($logger);
                case 'self':
                    return static::getSelfInstance($logger);
                case 'selfaction':
                    return static::getSelfACtionInstance($logger);
            }
        }
        return oxNew(static::class, $source, $logger);
    }
    
    /**
     * Uses {@see Url::getInstance()} to create an instance that will use the data from $_SERVER to get the current url.
     *
     * @param LoggerInterface|null $logger
     *
     * @return Url|$this
     */
    public static function getRequestInstance(?LoggerInterface $logger = null)
    {
        return static::getInstance(null, $logger)->request;
    }
    
    /**
     * Uses {@see Url::getInstance()} to create an instant that will use the shop's base url.
     *
     * @param LoggerInterface|null $logger
     *
     * @return Url|$this
     */
    public static function getOxidInstance(?LoggerInterface $logger = null)
    {
        return static::getInstance(null, $logger)->oxid;
    }
    
    /**
     * @param LoggerInterface|null $logger
     *
     * @return Url|$this
     */
    public static function getSelfInstance(?LoggerInterface $logger = null)
    {
        return static::getInstance(null, $logger)->self;
    }
    
    /**
     * @param LoggerInterface|null $logger
     *
     * @return Url|$this
     */
    public static function getSelfActionInstance(?LoggerInterface $logger = null)
    {
        return static::getInstance(null, $logger)->selfaction;
    }
    
    /**
     * Creates instance from null (empty), from self (cloning), from array or from string (url source).
     *
     * @param null|string|array|Url $source
     * @param LoggerInterface|null  $logger
     *
     * @throws UrlParameterException
     */
    public function __construct($source, ?LoggerInterface $logger = null)
    {
        $this->withLogger($logger);
        if (null === $source) {
            // nooop
        } else {
            if ($source instanceof static) {
                $this->extractClone($source);
            } else {
                try {
                    $source = (string)$source;
                    $this->extract($source);
                } catch (\Throwable $e) {
                    throw UrlParameterException::getInstance('Expected $source to be a stringable or instance of Url...');
                }
            }
        }
    }
    
    /**
     *
     * @param Url|string $other
     *
     * @return bool
     */
    public function equals($other): bool
    {
        try {
            return $this->url === (string)$other;
        } catch (\Throwable $e) {
            return false;
        }
    }
    
    /**
     * @return string|null The domain, if hosts is set, else null
     */
    public function getDomain(): ?string
    {
        if ($host = $this->_host) {
            if ($scheme = $this->_scheme ?? '') {
                $scheme .= ':';
            }
            if ($login = $this->login) {
                $login .= '@';
            }
            if ($port = $this->_port and $port !== static::DEFAULT_PORT) {
                $port = ':' . $port;
            } else {
                $port = '';
            }
            return "{$scheme}//{$login}{$host}{$port}";
        }
        return null;
    }
    
    /**
     * @return $this clones this.
     */
    public function clone(): Url
    {
        return static::getInstance($this);
    }
    
    /**
     * Set the scheme.
     *
     * @param string|null $scheme
     *
     * @return $this
     */
    public function withScheme(?string $scheme)
    {
        $this->_scheme = $scheme;
        return $this;
    }
    
    /**
     * Set the host.
     *
     * @param string|null $host
     *
     * @return $this
     */
    public function withHost(?string $host)
    {
        $this->_host = $host;
        return $this;
    }
    
    /**
     * Set the port. The {@see Url::DEFAULT_PORT} won't be displayed in the final url.
     *
     * @param int $port
     *
     * @return $this
     */
    public function withPort(int $port = self::DEFAULT_PORT)
    {
        $this->_port = $port;
        return $this;
    }
    
    /**
     * Add login information prepanded before the host.
     *
     * @param string|null $user
     * @param string|null $password
     *
     * @return $this
     */
    public function withLogin(?string $user, ?string $password)
    {
        $this->_user     = $user;
        $this->_password = $password;
        return $this;
    }
    
    /**
     * Add a fragment
     *
     * @param string|null $fragment
     *
     * @return $this
     */
    public function withFragment(?string $fragment)
    {
        $this->_fragment = $fragment;
        return $this;
    }
    
    /**
     * Set the path to have leading slash (if host exists and the url is fully presented, this will be ignored).
     *
     * @param bool $leading
     *
     * @return $this
     */
    public function withLeading(bool $leading)
    {
        $this->_leading = $leading;
        return $this;
    }
    
    /**
     * Set the path to have a trailing slash.
     *
     * @param bool $trailing
     *
     * @return $this
     */
    public function withTrailing(bool $trailing)
    {
        $this->_trailing = $trailing;
        return $this;
    }
    
    /**
     * Set the path. There are several options.
     * Each option result in extracting or getting a numeric array.
     *
     * callable(currentPath) -> !callable: The callable provides the current path and expects a response from the available types other than a callback.
     * object: is converted to array
     * string: is parsed to array
     * array: is applied directly
     *
     * @param string|object|array|callable $source
     *
     * @return $this
     * @throws UrlPathException
     */
    public function withPath($source)
    {
        if (is_object($source)) {
            return $this->withPath((array)$source);
        }
        if (is_array($source)) {
            $this->_path = array_values($this->flattenPath(...$source));
            return $this;
        }
        if (is_callable($source)) {
            $value = $source($this->_path);
            if (is_callable($value)) {
                throw UrlPathException::getInstance('withPath(callable) returned callable, but expected string or array');
            }
            return $this->withPath($value);
        }
        if (is_string($source)) {
            $this->extractPath($source);
            return $this;
        }
        throw UrlPathException::getInstance('withPath($source) used unexpected parameter');
    }
    
    public function flattenPath(string ...$elements): array
    {
        $next = [];
        foreach ($elements as $element) {
            $element = trim($element, '/');
            array_push($next, ...explode('/', $element));
        }
        return $next;
    }
    
    /**
     * Appends given paths to the path.
     *
     * @param string ...$path
     *
     * @return $this
     */
    public function addPath(string ...$path)
    {
        array_push($this->_path, ...$this->flattenPath(...$path));
        $this->logger->notice('Adding path...', ['path' => $path, 'new' => $this->_path]);
        return $this;
    }
    
    /**
     * Prepends given paths to the path.
     *
     * @param string ...$path
     *
     * @return $this
     */
    public function prependPath(string ...$path)
    {
        array_unshift($this->_path, ...$this->flattenPath(...$path));
        return $this;
    }
    
    /**
     * Drops the $times last path elements.
     *
     * @param int $times
     *
     * @return $this
     */
    public function popPath(int $times = 1)
    {
        for ($i = $times; $i > 0; --$i) {
            array_pop($this->_path);
        }
        return $this;
    }
    
    /**
     * Drops the $times first path elements.
     *
     * @param int $times
     *
     * @return $this
     */
    public function shiftPath(int $times = 1)
    {
        for ($i = $times; $i > 0; --$i) {
            array_shift($this->_path);
        }
        $this->logger->notice('Shifted path', ['times' => $times, 'path' => $this->_path]);
        return $this;
    }
    
    /**
     * Defines the query parameters.
     * Everything will be reduces to an associative array.
     *
     * callable(currentQuery) -> !callable: The current query array is provided and a qualified response other than a callable is required.
     * object: converted to array
     * string: parsed to array
     * array: direclty used
     *
     * @param object|array|callable|string $source
     * @param bool                         $mergeArray if the query should be merged with the existing.
     *
     * @return $this
     * @throws UrlQueryException
     */
    public function withQuery($source, bool $mergeArray = false)
    {
        if (is_array($source)) {
            $this->_query = $mergeArray ? array_merge($this->_query, $source) : $source;
            return $this;
        }
        if (is_object($source)) {
            return $this->withQuery((array)$source);
        }
        if (is_callable($source)) {
            $value = $source($this->_query);
            if (is_callable($value)) {
                throw UrlQueryException::getInstance('withQuery(callable) returned callable, but expected string or array');
            }
            return $this->withQuery($value);
        }
        if (is_string($source)) {
            $this->extractQuery($source);
            return $this;
        }
        throw UrlQueryException::getInstance('withQuery($source) used unexpected parameter');
    }
    
    /**
     * Adds a query parameter
     *
     * @param string $name
     * @param mixed  $value if null (removed on string)
     *
     * @return $this
     */
    public function addQuery(string $name, $value)
    {
        $this->_query[$name] = $value;
        return $this;
    }
    
    /**
     * Removes given query parameters.
     *
     * @param string ...$name
     *
     * @return $this
     */
    public function removeQuery(string ...$name)
    {
        if (empty($name)) {
            return $this;
        }
        $this->_query = array_diff_key($this->_query, array_flip($name));
        return $this;
    }
    
    /**
     * Removes all the other query parameters.
     *
     * @param string ...$name
     *
     * @return $this
     */
    public function keepQuery(string ...$name)
    {
        if (empty($name)) {
            return $this;
        }
        $this->_query = array_intersect_key($this->_query, array_flip($name));
        return $this;
    }
    /**
     * Apply global $_GET and/or $_POST
     *
     * @param bool $get
     * @param bool $post
     *
     * @return $this
     */
    public function withGlobals(bool $get = true, bool $post = false)
    {
        $query = $get ? ($_GET ?? []) : [];
        if ($post) {
            $query = array_merge($query, $_POST ?? []);
        }
        foreach ($query as $name => $value) {
            $this->_query[$name] = $value;
        }
        return $this;
    }
    
    public function __get($name)
    {
        switch ($name) {
            case 'scheme':
                return $this->_scheme;
            case 'host':
                return $this->_host;
            case 'port':
                return $this->_port;
            case 'fragment':
                return $this->_fragment;
            case 'user':
                return $this->_user;
            case 'password':
                return $this->_password;
            case 'login':
                if ($login = $this->_user) {
                    if ($pass = $this->_password) {
                        $login .= ":{$pass}";
                    }
                    return $login;
                }
                return null;
            case 'pathRaw':
                return $this->_path;
            case 'queryRaw':
                return $this->_query;
            case 'leading':
                return $this->_leading;
            case 'trailing':
                return $this->_trailing;
            case 'immutable':
                return $this->_immutable;
            case 'domain':
                return $this->getDomain();
            case 'clone':
            case 'copy':
                return $this->clone();
            case 'schemeless':
                return $this->withScheme(null);
            case 'hostless':
                return $this->withHost(null);
            case 'loginless':
                return $this->withLogin(null, null);
            case 'portless':
                return $this->withPort(static::DEFAULT_PORT);
            case 'fragmentless':
                return $this->withFragment(null);
            case 'pathless':
                $this->logger->notice('pathless called');
                return $this->withPath([]);
            case 'queryless':
                return $this->withQuery([]);
            case 'leaded':
                return $this->withLeading(true);
            case 'trailed':
                return $this->withTrailing(true);
            case 'leadingless':
                return $this->withLeading(false);
            case 'trailingless':
                return $this->withTrailing(false);
            case 'relative':
                return !$this->_host;
            case 'path':
                $path = implode('/', array_map('rawurlencode', $this->_path));
                if (!$path) {
                    if ($this->_leading || $this->_trailing) {
                        return '/';
                    }
                    return '';
                }
                if ($this->_leading) {
                    $path = '/' . $path;
                }
                if ($this->_trailing) {
                    $path .= '/';
                }
                return $path;
            case 'query':
                return http_build_query($this->_query, '', null, PHP_QUERY_RFC3986) ?: null;
            case 'request':
                $uri = '';
                if ($host = $_SERVER['HTTP_HOST'] ?? null) {
                    $uri .= '//' . $host;
                }
                if ($port = $_SERVER['HTTP_PORT'] ?? null and $port != static::DEFAULT_PORT) {
                    $uri .= ':' . $port;
                }
                if ($path = $_SERVER['REQUEST_URI'] ?? null) {
                    $uri .= $path;
                }
                $this->extract($uri);
                return $this;
            case 'oxid':
                $uri = str_replace('&amp;', '&', Registry::getConfig()->getShopUrl());
                $this->extract($uri);
                return $this;
            case 'self':
                $uri = str_replace('&amp;', '&', Registry::get(ViewConfig::class)->getSelfLink());
                $this->extract($uri);
                return $this;
            case 'selfaction':
                $uri = str_replace('&amp;', '&', Registry::get(ViewConfig::class)->getSelfActionLink());
                $this->extract($uri);
                return $this;
            case 'array':
                return $this->jsonSerialize();
            case 'json':
                return json_encode($this);
            case 'jsonNice':
                return json_encode($this, JSON_PRETTY_PRINT);
            case 'uri':
            case 'url':
                return $this->__toString();
            case 'relativeUri':
            case 'relativeUrl':
                return $this->clone->hostless->url;
            case 'popPath':
                return $this->popPath(1);
            case 'shiftPath':
                return $this->shiftPath(1);
            case 'globals':
                return $this->withGlobals(true, false);
            default:
                return null;
        }
    }
    
    public function __isset($name)
    {
        switch ($name) {
            case 'scheme':
            case 'host':
            case 'port':
            case 'fragment':
            case 'user':
            case 'password':
            case 'login':
            case 'pathRaw':
            case 'queryRaw':
            case 'leading':
            case 'trailling':
            case 'immutable':
            case 'domain':
            case 'path':
            case 'query':
            case 'relative':
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
            case 'request':
            case 'oxid':
            case 'self':
            case 'selfaction':
            case 'json':
            case 'jsonNice':
            case 'array':
            case 'uri':
            case 'url':
            case 'relativeUri':
            case 'relativeUrl':
                return true;
            default:
                return false;
        }
    }
    
    /**
     * Get the url string representation.
     *
     * @param bool $withQuery if to add the query section.
     *
     * @return string
     */
    public function get(bool $withQuery = true): string
    {
        $url = $this->domain ?? '';
        if ($path = $this->path) {
            if (!$this->_leading && !$this->_trailing && $url) {
                $url .= '/';
            }
            $url .= $path;
        }
        if ($withQuery and $query = $this->query) {
            $url .= '?' . $query;
        }
        if ($frag = $this->fragment) {
            $url .= '#' . $frag;
        }
        return $url;
    }
    
    /**
     * Extract all information from the string and apply it to this class.
     *
     * @param string $url
     *
     * @return void
     */
    protected function extract(string $url): void
    {
        $base            = parse_url($url);
        $this->_scheme   = $base['scheme'] ?? null;
        $this->_host     = $base['host'] ?? null;
        $this->_port     = $base['port'] ?? 80;
        $this->_fragment = $base['fragment'] ?? null;
        $this->_user     = $base['user'] ?? null;
        $this->_password = $base['pass'] ?? null;
        $this->extractPath($base['path'] ?? '');
        $this->extractQuery($base['query'] ?? '');
    }
    
    /**
     * Extract the query string and apply it.
     *
     * @param string $query
     *
     * @return void
     */
    protected function extractQuery(string $query): void
    {
        parse_str(urldecode($query), $this->_query);
    }
    
    /**
     * Extract the clone by using json serialization. Used for cloning.
     *
     * @param Url $source
     *
     * @return void
     */
    protected function extractClone(Url $source): void
    {
        foreach ($source->jsonSerialize() as $key => $value) {
            if (property_exists($this, "_$key")) {
                $this->{"_$key"} = $value;
            }
        }
    }
    
    /**
     * Extract and apply the path section from string.
     *
     * @param string $path
     *
     * @return void
     */
    protected function extractPath(string $path): void
    {
        $path            = trim($path);
        $this->_trailing = ((bool)preg_match("/\/$/", $path)) && strlen($path) > 1;
        $path            = rtrim(trim($path), '/');
        $this->withLeading(strpos($path, '/') === 0 and strlen($path) > 1);
        $this->_path = array_filter(explode('/', $path), 'trim');
    }
    
    /**
     * @return string show the full url on string casting.
     */
    public function __toString(): string
    {
        return $this->get(true);
    }
    
    public function jsonSerialize()
    {
        return [
            'class'     => static::class,
            'host'      => $this->_host,
            'port'      => $this->_port,
            'scheme'    => $this->_scheme,
            'fragment'  => $this->_fragment,
            'user'      => $this->_user,
            'password'  => $this->_password,
            'path'      => $this->_path,
            'query'     => $this->_query,
            'leading'   => $this->_leading,
            'trailing'  => $this->_trailing,
            'immutable' => $this->_immutable,
            'url'       => $this->__toString()
        ];
    }
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }
    public function offsetSet($offset, $value)
    {
        // noop
    }
    public function offsetUnset($offset)
    {
        // noop
    }
}