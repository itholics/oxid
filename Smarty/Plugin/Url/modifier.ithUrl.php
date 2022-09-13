<?php

function smarty_modifier_ithUrl($source, string ...$actions)
{
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrl($source, ...$actions);
}