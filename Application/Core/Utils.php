<?php

namespace ITholics\Oxid\Application\Core;

use ITholics\Oxid\Application\Core\IO\Database\Provider;
use ITholics\Oxid\Application\Exception\Exception;
use ITholics\Oxid\Application\Shared\StaticInstanceTrait;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ViewConfig;
use function array_map;
use function count;
use function debug_backtrace;
use function explode;
use function implode;
use function is_array;
use function ltrim;
use function max;
use function min;
use function sprintf;
use function str_pad;
use const PHP_EOL;
use const STR_PAD_LEFT;

/**
 * @method static Utils getInstance()
 */
class Utils
{
    use StaticInstanceTrait;
    
    /**
     * Access out folder of shop or given module
     *
     * @param string      $path
     * @param string|null $moduleId
     *
     * @return string|null
     */
    public function getPublicFile(string $path, ?string $moduleId = null): ?string
    {
        try {
            $path = ltrim($path, "/\t\n\r\0\x0B");
            /** @var ViewConfig $vc */
            $vc = Registry::get(ViewConfig::class);
            if (null !== $moduleId) {
                return $vc->getModuleUrl($moduleId, $path);
            }
            return Registry::getConfig()->getOutUrl() . $vc->getActiveTheme() . '/' . $path;
        } catch (\Throwable $e) {
            Registry::getLogger()->error('Failed loading resource from outfolder: ' . $e->getMessage(), [$e, 'path' => $path, 'moduleId' => $moduleId]);
            return null;
        }
    }
    
    /**
     * Get name of function found in the trace at a given $level.
     *
     * @param int  $level
     * @param bool $withClass
     *
     * @return string
     */
    public function getCallee(int $level = 0, bool $withClass = true): string
    {
        ++$level;
        $trace = debug_backtrace();
        $trace = $trace[max(0, min(count($trace) - 1, $level))] ?? null;
        if (null === $trace) {
            return "METHOD_UNAVAILABLE_IN_$level";
        }
        if (!$withClass) {
            return $trace['function'];
        }
        return $trace['class'] . '::' . $trace['function'];
    }
    
    /**
     * Add padding to each line of $content, this may be a string or an array of strings.
     *
     * @param string|string[] $content
     * @param int             $padSize how many $fill'ers to use
     * @param string          $fill    fill char/string, by default ' ' (space character)
     *
     * @return string
     */
    public function padLeft($content, int $padSize, string $fill = ' '): string
    {
        if (!is_array($content)) {
            $content = explode(PHP_EOL, $content);
        }
        $content = array_map(static function ($line) use ($padSize, $fill) {
            $padding = '';
            while (abs($padSize)) {
                $padding .= $fill;
                --$padSize;
            }
            return $padding . $line;
        }, $content);
        return implode(PHP_EOL, $content);
    }
    
    /**
     * Format an exception
     *
     * @param \Throwable $e
     *
     * @return string
     */
    public function formatException(\Throwable $e): string
    {
        $more = '';
        if ($e instanceof Exception) {
            $more = sprintf("\n#method=[%s] #internalCode=[%s]", $e->getMethod(), $e->getInternalCode());
        }
        $f = array_map(static function (string $line) { return str_pad($line, 2, "\t", STR_PAD_LEFT); }, explode(PHP_EOL, $e->getTraceAsString()));
        $f = implode(PHP_EOL, $f);
        return sprintf("[%s] (%d) > %s (%s/%d)%s%s%s",
            get_class($e),
            $e->getCode() ?? -1,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $more,
            PHP_EOL,
            $this->padLeft($e->getTraceAsString(), 8)
        );
    }
    
    public function getDb(): IO\Database\DatabaseInterface
    {
        return Provider::getInstance()->get();
    }
}