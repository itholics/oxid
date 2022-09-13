<?php

function smarty_modifier_ithFallbackStrict($source, $fallback = null)
{
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithFallbackStrict($source, $fallback);
}