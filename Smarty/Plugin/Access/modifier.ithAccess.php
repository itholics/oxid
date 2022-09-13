<?php

function smarty_modifier_ithAccess($source, string $index, ... $params) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithAccess($source, $index, ...$params);
}