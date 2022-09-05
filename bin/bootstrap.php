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
