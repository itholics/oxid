<?php

function smarty_modifier_ithReq($source, bool $raw = false) {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithReq($source, $raw);
}