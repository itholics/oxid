<?php

function smarty_modifier_ithUrlPathPop($source, $times = 1) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlPathPop($source, (int)$times);
}