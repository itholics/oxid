<?php

function smarty_function_ithOut($params, &$smarty): ?string
{
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithOutFunction($params, $smarty);
}