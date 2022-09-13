<?php

function smarty_modifier_ithUrlEquals($source, $other) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlEquals($source, $other);
}