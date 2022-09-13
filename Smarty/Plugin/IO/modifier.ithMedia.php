<?php

function smarty_modifier_ithMedia($source, ...$args): ?string {
    return \ITholics\Oxid\Application\Core\Smarty::getInstance()->ithMedia($source, ...$args);
}