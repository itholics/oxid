<?php

function smarty_modifier_ithFallback($source, $fallback = null) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithFallback($source, $fallback);
}