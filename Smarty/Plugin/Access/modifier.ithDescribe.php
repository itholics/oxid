<?php

function smarty_modifier_ithDescribe($source, bool $showString = false)
{
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithDescribe($source, $showString);
}