<?php

function smarty_modifier_ithUrlScheme($source, ?string $scheme = null) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlScheme($source, $scheme);
}