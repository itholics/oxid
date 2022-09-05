<?php
/**
 * This Software is the property of ITholics GmbH and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link          https://www.itholics.de
 * @copyright (C) ITholics GmbH 2011-2022
 *
 */
/**
 * Metadata version definition
 */
use ITholics\Oxid\Application\Core\Module;

$sMetadataVersion = '2.1';
/**
 *
 */
$aModule = [
    'id'                      => Module::ID,
    'title'                   => [
        'de' => '<div style="display:flex; align-items: center;"><img src="../modules/ith_modules/oxid/out/thumb.png" alt="ith" title="ITholics" style="height: 15px; margin-right: 5px;" /> <span><strong>IT</strong>holics - oXID Basis Modul - ' . Module::VERSION . '</span></div>',
        'en' => '<div style="display:flex; align-items: center;"><img src="../modules/ith_modules/oxid/out/thumb.png" alt="ith" title="ITholics" style="height: 15px; margin-right: 5px;" /> <span><strong>IT</strong>holics - oXID Basic Module - ' . Module::VERSION . '</span></div>'
    ],
    'description'             => [
        'de' => 'Dieses Modul enthält praktische Funktionen zur Verwendung innerhalb oxid.',
        'en' => 'This module contains practical functions for use within oxid.'
    ],
    'thumbnail'               => 'out/logo.png',
    'version'                 => Module::VERSION,
    'author'                  => 'ITholics GmbH',
    'url'                     => 'https://itholics.de',
    'email'                   => 'info@itholics.de',
    'controllers'             => [],
    'templates'               => [],
    'extend'                  => [],
    'blocks'                  => [],
    'events'                  => [],
    'settings'                => [],
    'smartyPluginDirectories' => [
        'Smarty/Plugin',
        'Smarty/Plugin/Access',
        'Smarty/Plugin/IO',
        'Smarty/Plugin/Url',
    ]
];
