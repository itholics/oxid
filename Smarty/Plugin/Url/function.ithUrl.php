<?php

function smarty_function_ithUrl($params, &$smarty)
{
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithUrlFunction($params, $smarty);
}