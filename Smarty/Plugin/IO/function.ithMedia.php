<?php

function smarty_function_ithMedia($params, &$smarty) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithMediaFunction($params, $smarty);
}