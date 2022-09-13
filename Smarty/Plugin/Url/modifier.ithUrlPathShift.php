<?php

function smarty_modifier_ithUrlPathShift($source, $times = 1)
{
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlPathShift($source, (int)$times);
}