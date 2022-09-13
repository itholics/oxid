<?php

function smarty_modifier_ithUrlPathPrepend($source, ...$elements) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlPathPrepend($source, ...$elements);
}