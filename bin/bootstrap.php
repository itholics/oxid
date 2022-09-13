<?php

$bootstrap = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'bootstrap.php';
if (!file_exists($bootstrap)) {
    $bootstrap = [dirname(__DIR__, 4), 'source', 'bootstrap.php'];
    $bootstrap = implode(DIRECTORY_SEPARATOR, $bootstrap);
    if (!file_exists($bootstrap)) {
        die("\nFailed to load OXID bootstrap! ($bootstrap) \n\n");
    }
}
/** @noinspection PhpIncludeInspection */
require_once $bootstrap;
