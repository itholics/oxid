<?php

function smarty_modifier_ithUrlQuery($source, ...$args)
{
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlQuery($source, ...$args);
}